<?php

namespace App\Models;

use App\Mixins\RegistrationBonus\RegistrationBonusAccounting;
use App\Models\Observers\SaleNumberObserver;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public static $webinar = 'webinar';
    public static $meeting = 'meeting';
    public static $subscribe = 'subscribe';
    public static $promotion = 'promotion';
    public static $registrationPackage = 'registration_package';
    public static $product = 'product';
    public static $bundle = 'bundle';
    public static $gift = 'gift';
    public static $installmentPayment = 'installment_payment';

    public static $credit = 'credit';
    public static $paymentChannel = 'payment_channel';

    public $timestamps = false;

    protected $guarded = ['id'];


    protected static function boot()
    {
        parent::boot();

        Sale::observe(SaleNumberObserver::class);
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Bundle', 'bundle_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo('App\User', 'seller_id', 'id');
    }

    public function meeting()
    {
        return $this->belongsTo('App\Models\Meeting', 'meeting_id', 'id');
    }

    public function subscribe()
    {
        return $this->belongsTo('App\Models\Subscribe', 'subscribe_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo('App\Models\Promotion', 'promotion_id', 'id');
    }

    public function registrationPackage()
    {
        return $this->belongsTo('App\Models\RegistrationPackage', 'registration_package_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket', 'ticket_id', 'id');
    }

    public function saleLog()
    {
        return $this->hasOne('App\Models\SaleLog', 'sale_id', 'id');
    }

    public function productOrder()
    {
        return $this->belongsTo('App\Models\ProductOrder', 'product_order_id', 'id');
    }

    public function gift()
    {
        return $this->belongsTo('App\Models\Gift', 'gift_id', 'id');
    }

    public function installmentOrderPayment()
    {
        return $this->belongsTo('App\Models\InstallmentOrderPayment', 'installment_payment_id', 'id');
    }

    public static function createSales($orderItem, $payment_method)
    {
        $orderType = Order::$webinar;
        if (!empty($orderItem->reserve_meeting_id)) {
            $orderType = Order::$meeting;
        } elseif (!empty($orderItem->subscribe_id)) {
            $orderType = Order::$subscribe;
        } elseif (!empty($orderItem->promotion_id)) {
            $orderType = Order::$promotion;
        } elseif (!empty($orderItem->registration_package_id)) {
            $orderType = Order::$registrationPackage;
        } elseif (!empty($orderItem->product_id)) {
            $orderType = Order::$product;
        } elseif (!empty($orderItem->bundle_id)) {
            $orderType = Order::$bundle;
        } elseif (!empty($orderItem->installment_payment_id)) {
            $orderType = Order::$installmentPayment;
        }

        if (!empty($orderItem->gift_id)) {
            $orderType = Order::$gift;
        }

        $seller_id = OrderItem::getSeller($orderItem);

        $sale = Sale::create([
            'buyer_id' => $orderItem->user_id,
            'seller_id' => $seller_id,
            'order_id' => $orderItem->order_id,
            'webinar_id' => (empty($orderItem->gift_id) and !empty($orderItem->webinar_id)) ? $orderItem->webinar_id : null,
            'bundle_id' => (empty($orderItem->gift_id) and !empty($orderItem->bundle_id)) ? $orderItem->bundle_id : null,
            'meeting_id' => !empty($orderItem->reserve_meeting_id) ? $orderItem->reserveMeeting->meeting_id : null,
            'meeting_time_id' => !empty($orderItem->reserveMeeting) ? $orderItem->reserveMeeting->meeting_time_id : null,
            'subscribe_id' => $orderItem->subscribe_id,
            'promotion_id' => $orderItem->promotion_id,
            'registration_package_id' => $orderItem->registration_package_id,
            'product_order_id' => (!empty($orderItem->product_order_id)) ? $orderItem->product_order_id : null,
            'installment_payment_id' => $orderItem->installment_payment_id ?? null,
            'gift_id' => $orderItem->gift_id ?? null,
            'type' => $orderType,
            'payment_method' => $payment_method,
            'amount' => $orderItem->amount,
            'tax' => $orderItem->tax_price,
            'commission' => $orderItem->commission_price,
            'discount' => $orderItem->discount,
            'total_amount' => $orderItem->total_amount,
            'product_delivery_fee' => $orderItem->product_delivery_fee,
            'created_at' => time(),
        ]);

        self::handleSaleNotifications($orderItem, $seller_id);

        if (!empty($orderItem->product_id)) {
            $buyStoreReward = RewardAccounting::calculateScore(Reward::BUY_STORE_PRODUCT, $orderItem->total_amount);
            RewardAccounting::makeRewardAccounting($orderItem->user_id, $buyStoreReward, Reward::BUY_STORE_PRODUCT, $orderItem->product_id);
        }

        $buyReward = RewardAccounting::calculateScore(Reward::BUY, $orderItem->total_amount);
        RewardAccounting::makeRewardAccounting($orderItem->user_id, $buyReward, Reward::BUY);

        /* Registration Bonus Accounting */
        $registrationBonusAccounting = new RegistrationBonusAccounting();
        $registrationBonusAccounting->checkBonusAfterSale($orderItem->user_id);

        return $sale;
    }

    private static function handleSaleNotifications($orderItem, $seller_id)
    {
        $title = '';
        if (!empty($orderItem->webinar_id)) {
            $title = $orderItem->webinar->title;
        } elseif (!empty($orderItem->bundle_id)) {
            $title = $orderItem->bundle->title;
        } else if (!empty($orderItem->meeting_id)) {
            $title = trans('meeting.reservation_appointment');
        } else if (!empty($orderItem->subscribe_id)) {
            $title = $orderItem->subscribe->title . ' ' . trans('financial.subscribe');
        } else if (!empty($orderItem->promotion_id)) {
            $title = $orderItem->promotion->title . ' ' . trans('panel.promotion');
        } else if (!empty($orderItem->registration_package_id)) {
            $title = $orderItem->registrationPackage->title . ' ' . trans('update.registration_package');
        } else if (!empty($orderItem->product_id)) {
            $title = $orderItem->product->title;
        } else if (!empty($orderItem->installment_payment_id)) {
            $title = ($orderItem->installmentPayment->type == 'upfront') ? trans('update.installment_upfront') : trans('update.installment');
        }

        if (!empty($orderItem->gift_id) and !empty($orderItem->gift)) {
            $title .= ' (' . trans('update.a_gift_for_name_on_date_without_bold', ['name' => $orderItem->gift->name, 'date' => dateTimeFormat($orderItem->gift->date, 'j M Y H:i')]) . ')';
        }

        if ($orderItem->reserve_meeting_id) {
            $reserveMeeting = $orderItem->reserveMeeting;

            $notifyOptions = [
                '[amount]' => handlePrice($orderItem->amount),
                '[u.name]' => $orderItem->user->full_name,
                '[time.date]' => $reserveMeeting->day . ' ' . $reserveMeeting->time,
            ];
            sendNotification('new_appointment', $notifyOptions, $orderItem->user_id);
            sendNotification('new_appointment', $notifyOptions, $reserveMeeting->meeting->creator_id);
        } elseif (!empty($orderItem->product_id)) {
            $notifyOptions = [
                '[p.title]' => $title,
                '[amount]' => handlePrice($orderItem->total_amount),
                '[u.name]' => $orderItem->user->full_name,
            ];

            sendNotification('product_new_sale', $notifyOptions, $seller_id);
            sendNotification('product_new_purchase', $notifyOptions, $orderItem->user_id);
            sendNotification('new_store_order', $notifyOptions, 1);
        } elseif (!empty($orderItem->installment_payment_id)) {
            // TODO:: installment notification
        } else {
            $notifyOptions = [
                '[c.title]' => $title,
            ];

            sendNotification('new_sales', $notifyOptions, $seller_id);
            sendNotification('new_purchase', $notifyOptions, $orderItem->user_id);
        }

        if (!empty($orderItem->webinar_id)) {
            $notifyOptions = [
                '[u.name]' => $orderItem->user->full_name,
                '[c.title]' => $title,
                '[amount]' => handlePrice($orderItem->total_amount),
                '[time.date]' => dateTimeFormat(time(), 'j M Y H:i'),
            ];
            sendNotification("new_course_enrollment", $notifyOptions, 1);
        }

        if (!empty($orderItem->subscribe_id)) {
            $notifyOptions = [
                '[u.name]' => $orderItem->user->full_name,
                '[item_title]' => $orderItem->subscribe->title,
                '[amount]' => handlePrice($orderItem->total_amount),
            ];
            sendNotification("subscription_plan_activated", $notifyOptions, 1);
        }
    }

    public function getIncomeItem()
    {
        if ($this->payment_method == self::$subscribe) {
            $used = SubscribeUse::where('webinar_id', $this->webinar_id)
                ->where('sale_id', $this->id)
                ->first();

            if (!empty($used)) {
                $subscribe = $used->subscribe;

                $financialSettings = getFinancialSettings();
                $commission = $financialSettings['commission'] ?? 0;

                $pricePerSubscribe = $subscribe->price / $subscribe->usable_count;
                $commissionPrice = $commission ? $pricePerSubscribe * $commission / 100 : 0;

                return round($pricePerSubscribe - $commissionPrice, 2);
            }
        }

        $income = $this->total_amount - $this->tax - $this->commission;
        return round($income, 2);
    }

    public function getUsedSubscribe($user_id, $itemId, $itemName = 'webinar_id')
    {
        $subscribe = null;
        $use = SubscribeUse::where('sale_id', $this->id)
            ->where($itemName, $itemId)
            ->where('user_id', $user_id)
            ->first();

        if (!empty($use)) {
            $subscribe = Subscribe::where('id', $use->subscribe_id)->first();

            if (!empty($subscribe)) {
                $subscribe->installment_order_id = $use->installment_order_id;
            }
        }

        return $subscribe;
    }

    public function checkExpiredPurchaseWithSubscribe($user_id, $itemId, $itemName = 'webinar_id')
    {
        $result = true;

        $subscribe = $this->getUsedSubscribe($user_id, $itemId, $itemName);

        if (!empty($subscribe)) {
            $subscribeSale = self::where('buyer_id', $user_id)
                ->where('type', self::$subscribe)
                ->where('subscribe_id', $subscribe->id)
                ->whereNull('refund_at')
                ->latest('created_at')
                ->first();

            if (!empty($subscribeSale)) {
                $usedDays = (int)diffTimestampDay(time(), $subscribeSale->created_at);

                if ($usedDays <= $subscribe->days) {
                    $result = false;
                }
            }
        }

        return $result;
    }
}
