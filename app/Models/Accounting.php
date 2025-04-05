<?php

namespace App\Models;

use App\Mixins\Cashback\CashbackRules;
use App\Models\Observers\AccountingNumberObserver;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    protected $table = "accounting";

    public static $addiction = 'addiction';
    public static $deduction = 'deduction';

    public static $asset = 'asset';
    public static $income = 'income';
    public static $subscribe = 'subscribe';
    public static $promotion = 'promotion';
    public static $storeManual = 'manual';
    public static $storeAutomatic = 'automatic';
    public static $registrationPackage = 'registration_package';
    public static $installmentPayment = 'installment_payment';

    public $timestamps = false;

    protected $guarded = ['id'];


    protected static function boot()
    {
        parent::boot();

        Accounting::observe(AccountingNumberObserver::class);
    }


    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Bundle', 'bundle_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo('App\Models\Promotion', 'promotion_id', 'id');
    }

    public function registrationPackage()
    {
        return $this->belongsTo('App\Models\RegistrationPackage', 'registration_package_id', 'id');
    }

    public function subscribe()
    {
        return $this->belongsTo('App\Models\Subscribe', 'subscribe_id', 'id');
    }

    public function meetingTime()
    {
        return $this->belongsTo('App\Models\MeetingTime', 'meeting_time_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function orderItem()
    {
        return $this->belongsTo('App\Models\OrderItem', 'order_item_id', 'id');
    }

    public function installmentOrderPayment()
    {
        return $this->belongsTo('App\Models\InstallmentOrderPayment', 'installment_payment_id', 'id');
    }

    public function gift()
    {
        return $this->belongsTo(Gift::class, 'gift_id', 'id');
    }

    public static function createAccounting($orderItem, $type = null)
    {
        self::createAccountingBuyer($orderItem, $type);

        if ($orderItem->tax_price and $orderItem->tax_price > 0) {
            self::createAccountingTax($orderItem);
        }

        self::createAccountingSeller($orderItem);

        if ($orderItem->commission_price) {
            self::createAccountingCommission($orderItem);
        }
    }

    public static function createAccountingBuyer($orderItem, $type = null)
    {
        if ($type !== 'credit') {
            Accounting::create([
                'user_id' => $orderItem->user_id,
                'order_item_id' => $orderItem->id,
                'amount' => $orderItem->total_amount,
                'webinar_id' => !empty($orderItem->webinar_id) ? $orderItem->webinar_id : null,
                'bundle_id' => !empty($orderItem->bundle_id) ? $orderItem->bundle_id : null,
                'meeting_time_id' => $orderItem->reserveMeeting ? $orderItem->reserveMeeting->meeting_time_id : null,
                'subscribe_id' => $orderItem->subscribe_id ?? null,
                'promotion_id' => $orderItem->promotion_id ?? null,
                'registration_package_id' => $orderItem->registration_package_id ?? null,
                'installment_payment_id' => $orderItem->installment_payment_id ?? null,
                'product_id' => $orderItem->product_id ?? null,
                'gift_id' => $orderItem->gift_id ?? null,
                'type' => Accounting::$addiction,
                'type_account' => Accounting::$asset,
                'description' => trans('public.paid_for_sale'),
                'created_at' => time()
            ]);
        }

        $deductionDescription = trans('public.paid_form_online_payment');
        if (!empty($orderItem->reserveMeeting)) {
            $time = $orderItem->reserveMeeting->meetingTime->time;
            $explodeTime = explode('-', $time);
            $minute = (strtotime($explodeTime[1]) - strtotime($explodeTime[0])) / 60;

            $deductionDescription = trans('meeting.paid_for_x_hour', ['hours' => convertMinutesToHourAndMinute($minute)]);
        } elseif ($type == 'credit') {
            $deductionDescription = trans('public.paid_form_credit');
        }

        $accountingType = Accounting::$deduction;

        Accounting::create([
            'user_id' => $orderItem->user_id,
            'order_item_id' => $orderItem->id,
            'amount' => $orderItem->total_amount,
            'webinar_id' => !empty($orderItem->webinar_id) ? $orderItem->webinar_id : null,
            'bundle_id' => !empty($orderItem->bundle_id) ? $orderItem->bundle_id : null,
            'meeting_time_id' => $orderItem->reserveMeeting ? $orderItem->reserveMeeting->meeting_time_id : null,
            'subscribe_id' => $orderItem->subscribe_id ?? null,
            'promotion_id' => $orderItem->promotion_id ?? null,
            'registration_package_id' => $orderItem->registration_package_id ?? null,
            'installment_payment_id' => $orderItem->installment_payment_id ?? null,
            'product_id' => $orderItem->product_id ?? null,
            'gift_id' => $orderItem->gift_id ?? null,
            'type' => $accountingType,
            'type_account' => Accounting::$asset,
            'description' => $deductionDescription,
            'created_at' => time()
        ]);

        $notifyOptions = [
            '[f.d.type]' => trans("update.{$accountingType}"),
            '[amount]' => handlePrice($orderItem->total_amount, true, true, false, $orderItem->user),
        ];

        if (!empty($orderItem->webinar_id)) {
            $notifyOptions['[c.title]'] = $orderItem->webinar->title;
        } elseif (!empty($orderItem->bundle_id)) {
            $notifyOptions['[c.title]'] = $orderItem->bundle->title;
        } elseif (!empty($orderItem->reserve_meeting_id)) {
            $notifyOptions['[c.title]'] = trans('meeting.reservation_appointment');
        } elseif (!empty($orderItem->product_id)) {
            $notifyOptions['[c.title]'] = $orderItem->product->title;
        } elseif (!empty($orderItem->installment_payment_id)) {
            $notifyOptions['[c.title]'] = ($orderItem->installmentPayment->type == 'upfront') ? trans('update.installment_upfront') : trans('update.installment');
        } else if (!empty($orderItem->subscribe_id)) {
            $notifyOptions['[c.title]'] = $orderItem->subscribe->title . ' ' . trans('financial.subscribe');
        } else if (!empty($orderItem->promotion_id)) {
            $notifyOptions['[c.title]'] = $orderItem->promotion->title . ' ' . trans('panel.promotion');
        } else if (!empty($orderItem->registration_package_id)) {
            $notifyOptions['[c.title]'] = $orderItem->registrationPackage->title . ' ' . trans('update.registration_package');
        }

        if (!empty($orderItem->gift_id) and !empty($orderItem->gift)) {
            $notifyOptions['[c.title]'] .= ' (' . trans('update.a_gift_for_name_on_date_without_bold', ['name' => $orderItem->gift->name, 'date' => dateTimeFormat($orderItem->gift->date, 'j M Y H:i')]) . ')';
        }

        sendNotification('new_financial_document', $notifyOptions, $orderItem->user_id);
    }

    public static function createAccountingTax($orderItem)
    {
        Accounting::create([
            'user_id' => $orderItem->user_id,
            'order_item_id' => $orderItem->id,
            'tax' => true,
            'amount' => $orderItem->tax_price,
            'webinar_id' => $orderItem->webinar_id,
            'bundle_id' => $orderItem->bundle_id,
            'meeting_time_id' => $orderItem->reserveMeeting ? $orderItem->reserveMeeting->meeting_time_id : null,
            'subscribe_id' => $orderItem->subscribe_id ?? null,
            'promotion_id' => $orderItem->promotion_id ?? null,
            'registration_package_id' => $orderItem->registration_package_id ?? null,
            'installment_payment_id' => $orderItem->installment_payment_id ?? null,
            'product_id' => $orderItem->product_id ?? null,
            'gift_id' => $orderItem->gift_id ?? null,
            'type_account' => Accounting::$asset,
            'type' => Accounting::$addiction,
            'description' => trans('public.tax_get_form_buyer'),
            'created_at' => time()
        ]);

        return true;
    }

    public static function createAccountingSeller($orderItem)
    {
        if (!empty($orderItem->bundle_id)) {
            self::createAccountingForBundle($orderItem);
        } else {
            $sellerId = OrderItem::getSeller($orderItem);

            Accounting::create([
                'user_id' => $sellerId,
                'order_item_id' => $orderItem->id,
                'installment_order_id' => $orderItem->installment_order_id ?? null,
                'amount' => $orderItem->total_amount - $orderItem->tax_price - $orderItem->commission_price,
                'webinar_id' => $orderItem->webinar_id,
                'bundle_id' => $orderItem->bundle_id,
                'meeting_time_id' => $orderItem->reserveMeeting ? $orderItem->reserveMeeting->meeting_time_id : null,
                'subscribe_id' => $orderItem->subscribe_id ?? null,
                'promotion_id' => $orderItem->promotion_id ?? null,
                'product_id' => $orderItem->product_id ?? null,
                'type_account' => Accounting::$income,
                'type' => Accounting::$addiction,
                'description' => trans('public.income_sale'),
                'created_at' => time()
            ]);
        }

        return true;
    }

    private static function createAccountingForBundle($orderItem)
    {
        $bundle = $orderItem->bundle;
        $bundleWebinars = $bundle->bundleWebinars;

        $orderAmount = $orderItem->total_amount - $orderItem->tax_price - $orderItem->commission_price;
        $sellersWithPrices = [];

        foreach ($bundleWebinars as $bundleWebinar) {
            $webinar = $bundleWebinar->webinar;

            if (empty($sellersWithPrices[$webinar->creator_id])) {
                $sellersWithPrices[$webinar->creator_id] = 0;
            }

            $sellersWithPrices[$webinar->creator_id] += $webinar->price;
        }

        if (count($sellersWithPrices)) {
            $sumPrices = array_sum($sellersWithPrices);
            $insert = [];

            foreach ($sellersWithPrices as $sellerId => $price) {
                if ($price > 0) {
                    $percent = ($price / $sumPrices) * 100;
                    $incomePrice = ($orderAmount * $percent) / 100;

                    $insert[] = [
                        'user_id' => $sellerId,
                        'order_item_id' => $orderItem->id,
                        'installment_order_id' => $orderItem->installment_order_id ?? null,
                        'amount' => $incomePrice,
                        'webinar_id' => $orderItem->webinar_id,
                        'bundle_id' => $orderItem->bundle_id,
                        'meeting_time_id' => $orderItem->reserveMeeting ? $orderItem->reserveMeeting->meeting_time_id : null,
                        'subscribe_id' => $orderItem->subscribe_id ?? null,
                        'promotion_id' => $orderItem->promotion_id ?? null,
                        'product_id' => $orderItem->product_id ?? null,
                        'type_account' => Accounting::$income,
                        'type' => Accounting::$addiction,
                        'description' => trans('public.income_sale'),
                        'created_at' => time()
                    ];
                }
            }

            if (count($insert)) {
                Accounting::insert($insert);
            }
        }
    }

    public static function createAccountingSystemForSubscribe($orderItem)
    {
        $sellerId = OrderItem::getSeller($orderItem);

        Accounting::create([
            'user_id' => $sellerId,
            'order_item_id' => $orderItem->id,
            'system' => true,
            'amount' => $orderItem->total_amount - $orderItem->tax_price,
            'subscribe_id' => $orderItem->subscribe_id,
            'type_account' => Accounting::$subscribe,
            'type' => Accounting::$addiction,
            'description' => trans('public.income_for_subscribe'),
            'created_at' => time()
        ]);

        return true;
    }

    public static function createAccountingCommission($orderItem)
    {
        $authId = $orderItem->user_id;
        $sellerId = OrderItem::getSeller($orderItem);

        $commission = $orderItem->commission;
        $commissionPrice = $orderItem->commission_price;
        $affiliateCommissionPrice = 0;


        $referralSettings = getReferralSettings();
        $affiliateStatus = (!empty($referralSettings) and !empty($referralSettings['status']));
        $affiliateUser = null;

        if ($affiliateStatus) {
            $affiliate = Affiliate::where('referred_user_id', $authId)->first();

            if (!empty($affiliate)) {
                $affiliateUser = $affiliate->affiliateUser;

                if (!empty($affiliateUser) and $affiliateUser->affiliate) {

                    if (!empty($affiliate)) {
                        if (!empty($orderItem->product_id) and !empty($referralSettings['store_affiliate_user_commission']) and $referralSettings['store_affiliate_user_commission'] > 0) {
                            $affiliateCommission = $referralSettings['store_affiliate_user_commission'];

                            if ($commission > 0) {
                                $affiliateCommissionPrice = ($affiliateCommission * $commissionPrice) / $commission;
                                $commissionPrice = $commissionPrice - $affiliateCommissionPrice;
                            }
                        } elseif (empty($orderItem->product_id) and !empty($referralSettings['affiliate_user_commission']) and $referralSettings['affiliate_user_commission'] > 0) {
                            $affiliateCommission = $referralSettings['affiliate_user_commission'];

                            if ($commission > 0) {
                                $affiliateCommissionPrice = ($affiliateCommission * $commissionPrice) / $commission;
                                $commissionPrice = $commissionPrice - $affiliateCommissionPrice;
                            }
                        }
                    }
                }
            }
        }

        Accounting::create([
            'user_id' => !empty($sellerId) ? $sellerId : 1,
            'order_item_id' => $orderItem->id,
            'system' => true,
            'amount' => $commissionPrice,
            'webinar_id' => $orderItem->webinar_id ?? null,
            'bundle_id' => $orderItem->bundle_id ?? null,
            'product_id' => $orderItem->product_id ?? null,
            'meeting_time_id' => $orderItem->reserveMeeting ? $orderItem->reserveMeeting->meeting_time_id : null,
            'subscribe_id' => $orderItem->subscribe_id ?? null,
            'promotion_id' => $orderItem->promotion_id ?? null,
            'type_account' => Accounting::$income,
            'type' => Accounting::$addiction,
            'description' => trans('public.get_commission_from_seller'),
            'created_at' => time()
        ]);

        if (!empty($affiliateUser) and $affiliateCommissionPrice > 0) {
            Accounting::create([
                'user_id' => $affiliateUser->id,
                'order_item_id' => $orderItem->id,
                'system' => false,
                'referred_user_id' => $authId,
                'is_affiliate_commission' => true,
                'amount' => $affiliateCommissionPrice,
                'webinar_id' => $orderItem->webinar_id ?? null,
                'bundle_id' => $orderItem->bundle_id ?? null,
                'product_id' => $orderItem->product_id ?? null,
                'meeting_time_id' => $orderItem->reserveMeeting ? $orderItem->reserveMeeting->meeting_time_id : null,
                'subscribe_id' => null,
                'promotion_id' => null,
                'type_account' => Accounting::$income,
                'type' => Accounting::$addiction,
                'description' => trans('public.get_commission_from_referral'),
                'created_at' => time()
            ]);
        }

        return true;
    }

    public static function createAffiliateUserAmountAccounting($userId, $referredUserId, $amount)
    {
        if ($amount) {
            Accounting::create([
                'user_id' => $userId,
                'referred_user_id' => $referredUserId,
                'is_affiliate_amount' => true,
                'system' => false,
                'amount' => $amount,
                'webinar_id' => null,
                'bundle_id' => null,
                'meeting_time_id' => null,
                'subscribe_id' => null,
                'promotion_id' => null,
                'type_account' => Accounting::$income,
                'type' => Accounting::$addiction,
                'description' => trans('public.get_amount_from_referral'),
                'created_at' => time()
            ]);

            Accounting::create([
                'user_id' => $userId,
                'referred_user_id' => $referredUserId,
                'is_affiliate_amount' => true,
                'system' => true,
                'amount' => $amount,
                'webinar_id' => null,
                'bundle_id' => null,
                'meeting_time_id' => null,
                'subscribe_id' => null,
                'promotion_id' => null,
                'type_account' => Accounting::$income,
                'type' => Accounting::$deduction,
                'description' => trans('public.get_amount_from_referral'),
                'created_at' => time()
            ]);
        }
    }


    public static function refundAccounting($sale, $productOrderId = null)
    {
        self::refundAccountingBuyer($sale);

        if ($sale->tax) {
            self::refundAccountingTax($sale);
        }

        self::refundAccountingSeller($sale);

        if ($sale->commission) {
            self::refundAccountingCommission($sale);
        }
    }

    public static function refundAccountingBuyer($sale)
    {
        Accounting::create([
            'user_id' => $sale->buyer_id,
            'amount' => $sale->total_amount,
            'webinar_id' => $sale->webinar_id,
            'bundle_id' => $sale->bundle_id,
            'meeting_time_id' => $sale->meeting_time_id,
            'subscribe_id' => $sale->subscribe_id ?? null,
            'promotion_id' => $sale->promotion_id ?? null,
            'product_id' => !empty($sale->productOrder) ? $sale->productOrder->product_id : null,
            'type' => Accounting::$addiction,
            'type_account' => Accounting::$asset,
            'description' => trans('public.refund_money_to_buyer'),
            'created_at' => time()
        ]);

        return true;
    }

    public static function refundAccountingTax($sale)
    {
        if (!empty($sale->tax) and $sale->tax > 0) {
            Accounting::create([
                'tax' => true,
                'amount' => $sale->tax,
                'webinar_id' => $sale->webinar_id,
                'bundle_id' => $sale->bundle_id,
                'meeting_time_id' => $sale->meeting_time_id,
                'subscribe_id' => $sale->subscribe_id ?? null,
                'promotion_id' => $sale->promotion_id ?? null,
                'product_id' => !empty($sale->productOrder) ? $sale->productOrder->product_id : null,
                'type_account' => Accounting::$asset,
                'type' => Accounting::$deduction,
                'description' => trans('public.refund_tax'),
                'created_at' => time()
            ]);
        }

        return true;
    }

    public static function refundAccountingCommission($sale)
    {
        if (!empty($sale->commission) and $sale->commission > 0) {
            Accounting::create([
                'system' => true,
                'user_id' => $sale->seller_id,
                'amount' => $sale->commission,
                'webinar_id' => $sale->webinar_id,
                'bundle_id' => $sale->bundle_id,
                'meeting_time_id' => $sale->meeting_time_id,
                'subscribe_id' => $sale->subscribe_id ?? null,
                'promotion_id' => $sale->promotion_id ?? null,
                'product_id' => !empty($sale->productOrder) ? $sale->productOrder->product_id : null,
                'type_account' => Accounting::$income,
                'type' => Accounting::$deduction,
                'description' => trans('public.refund_commission'),
                'created_at' => time()
            ]);
        }

        return true;
    }

    public static function refundAccountingSeller($sale)
    {
        $amount = $sale->total_amount;

        if (!empty($sale->tax) and $sale->tax > 0) {
            $amount = $amount - $sale->tax;
        }

        if (!empty($sale->commission) and $sale->commission > 0) {
            $amount = $amount - $sale->commission;
        }

        Accounting::create([
            'user_id' => $sale->seller_id,
            'amount' => $amount,
            'webinar_id' => $sale->webinar_id,
            'bundle_id' => $sale->bundle_id,
            'meeting_time_id' => $sale->meeting_time_id,
            'subscribe_id' => $sale->subscribe_id ?? null,
            'promotion_id' => $sale->promotion_id ?? null,
            'product_id' => !empty($sale->productOrder) ? $sale->productOrder->product_id : null,
            'type_account' => Accounting::$income,
            'type' => Accounting::$deduction,
            'description' => trans('public.refund_income'),
            'created_at' => time()
        ]);

        return true;
    }

    public static function charge($order)
    {
        Accounting::create([
            'user_id' => $order->user_id,
            'amount' => $order->total_amount,
            'type_account' => Order::$asset,
            'type' => Order::$addiction,
            'description' => trans('public.charge_account'),
            'created_at' => time()
        ]);

        $accountChargeReward = RewardAccounting::calculateScore(Reward::ACCOUNT_CHARGE, $order->total_amount);
        RewardAccounting::makeRewardAccounting($order->user_id, $accountChargeReward, Reward::ACCOUNT_CHARGE);

        $chargeWalletReward = RewardAccounting::calculateScore(Reward::CHARGE_WALLET, $order->total_amount);
        RewardAccounting::makeRewardAccounting($order->user_id, $chargeWalletReward, Reward::CHARGE_WALLET);

        $notifyOptions = [
            '[u.name]' => $order->user->full_name,
            '[amount]' => handlePrice($order->total_amount),
        ];
        sendNotification('user_wallet_charge', $notifyOptions, 1);

        return true;
    }


    public static function createAccountingForSubscribe($orderItem, $type = null)
    {
        self::createAccountingBuyer($orderItem, $type);
        if ($orderItem->tax_price and $orderItem->tax_price > 0) {
            self::createAccountingTax($orderItem);
        }

        self::createAccountingSystemForSubscribe($orderItem);

        $notifyOptions = [
            '[u.name]' => $orderItem->user->full_name,
            '[s.p.name]' => $orderItem->subscribe->title,
        ];

        sendNotification('new_subscribe_plan', $notifyOptions, $orderItem->user_id);
    }

    public static function createAccountingForPromotion($orderItem, $type = null)
    {
        self::createAccountingBuyer($orderItem, $type);

        if ($orderItem->tax_price and $orderItem->tax_price > 0) {
            self::createAccountingTax($orderItem);
        }

        self::createAccountingSystemForPromotion($orderItem);

        $notifyOptions = [
            '[c.title]' => $orderItem->webinar->title,
            '[p.p.name]' => $orderItem->promotion->title,
        ];

        sendNotification('promotion_plan', $notifyOptions, $orderItem->user_id);
    }


    public static function createAccountingSystemForPromotion($orderItem)
    {
        Accounting::create([
            'user_id' => $orderItem->webinar_id ? $orderItem->webinar->creator_id : (!empty($orderItem->reserve_meeting_id) ? $orderItem->reserveMeeting->meeting->creator_id : 1),
            'order_item_id' => $orderItem->id,
            'system' => true,
            'amount' => $orderItem->total_amount - $orderItem->tax_price,
            'promotion_id' => $orderItem->promotion_id,
            'type_account' => Accounting::$promotion,
            'type' => Accounting::$addiction,
            'description' => trans('public.income_for_promotion'),
            'created_at' => time()
        ]);
    }

    public static function createAccountingForRegistrationPackage($orderItem, $type = null)
    {
        self::createAccountingBuyer($orderItem, $type);

        if ($orderItem->tax_price and $orderItem->tax_price > 0) {
            self::createAccountingTax($orderItem);
        }

        self::createAccountingSystemForRegistrationPackage($orderItem);

        $registrationPackage = $orderItem->registrationPackage;
        $registrationPackageExpire = time() + ($registrationPackage->days * 24 * 60 * 60);

        $notifyOptions = [
            '[u.name]' => $orderItem->user->full_name,
            '[item_title]' => $registrationPackage->title,
            '[amount]' => handlePrice($orderItem->total_amount),
            '[time.date]' => dateTimeFormat($registrationPackageExpire, 'j M Y')
        ];
        sendNotification("registration_package_activated", $notifyOptions, $orderItem->user_id);
        sendNotification("registration_package_activated_for_admin", $notifyOptions, 1);
    }

    public static function createAccountingSystemForRegistrationPackage($orderItem)
    {
        Accounting::create([
            'user_id' => 1,
            'order_item_id' => $orderItem->id,
            'system' => true,
            'amount' => $orderItem->total_amount - $orderItem->tax_price,
            'registration_package_id' => $orderItem->registration_package_id,
            'type_account' => Accounting::$registrationPackage,
            'type' => Accounting::$addiction,
            'description' => trans('update.paid_for_registration_package'),
            'created_at' => time()
        ]);
    }

    public static function createAccountingForSaleWithSubscribe($item, $subscribe, $itemName)
    {
        $admin = User::getMainAdmin();

        $commission = $item->creator->getCommission();

        $pricePerSubscribe = $subscribe->price / $subscribe->usable_count;
        $commissionPrice = $commission ? ($pricePerSubscribe * $commission / 100) : 0;

        $totalAmount = $pricePerSubscribe - $commissionPrice;

        Accounting::create([
            'user_id' => $item->creator_id,
            'amount' => $totalAmount,
            $itemName => $item->id,
            'type' => Accounting::$addiction,
            'type_account' => Accounting::$income,
            'description' => trans('public.paid_form_subscribe'),
            'created_at' => time()
        ]);

        Accounting::create([
            'system' => true,
            'user_id' => $admin->id,
            'amount' => $totalAmount,
            $itemName => $item->id,
            'type' => Accounting::$deduction,
            'type_account' => Accounting::$asset,
            'description' => trans('public.paid_form_subscribe'),
            'created_at' => time()
        ]);
    }

    public static function refundAccountingForSaleWithSubscribe($webinar, $subscribe)
    {
        $admin = User::getMainAdmin();

        $financialSettings = getFinancialSettings();
        $commission = $financialSettings['commission'] ?? 0;

        $pricePerSubscribe = $subscribe->price / $subscribe->usable_count;
        $commissionPrice = $commission ? $pricePerSubscribe * $commission / 100 : 0;

        $totalAmount = $pricePerSubscribe - $commissionPrice;

        Accounting::create([
            'user_id' => $webinar->creator_id,
            'amount' => $totalAmount,
            'webinar_id' => $webinar->id,
            'type' => Accounting::$deduction,
            'type_account' => Accounting::$income,
            'description' => trans('public.paid_form_subscribe'),
            'created_at' => time()
        ]);

        Accounting::create([
            'system' => true,
            'user_id' => $admin->id,
            'amount' => $totalAmount,
            'webinar_id' => $webinar->id,
            'type' => Accounting::$addiction,
            'type_account' => Accounting::$asset,
            'description' => trans('public.paid_form_subscribe'),
            'created_at' => time()
        ]);
    }


    public static function createAccountingForInstallmentPayment($orderItem, $type = null)
    {
        self::createAccountingBuyer($orderItem, $type);

        if ($orderItem->tax_price and $orderItem->tax_price > 0) {
            self::createAccountingTax($orderItem);
        }

        self::createAccountingSystemForInstallmentPayment($orderItem);
    }

    public static function createAccountingSystemForInstallmentPayment($orderItem)
    {
        Accounting::create([
            'user_id' => 1,
            'order_item_id' => $orderItem->id,
            'system' => true,
            'amount' => $orderItem->total_amount - $orderItem->tax_price,
            'installment_payment_id' => $orderItem->installment_payment_id,
            'type_account' => Accounting::$installmentPayment,
            'type' => Accounting::$addiction,
            'description' => ($orderItem->installmentPayment->type == 'upfront') ? trans('update.installment_upfront') : trans('update.installment'),
            'created_at' => time()
        ]);
    }


    public static function createRegistrationBonusUserAmountAccounting($userId, $amount, $typeAccount)
    {
        $check = Accounting::query()->where('user_id', $userId)
            ->where('is_registration_bonus', true)
            ->first();

        if (!empty($amount) and empty($check)) { //
            Accounting::updateOrCreate([
                'user_id' => $userId,
                'is_registration_bonus' => true,
                'system' => false,
                'type_account' => $typeAccount,
                'type' => Accounting::$addiction,
            ], [
                'amount' => $amount,
                'description' => trans('update.get_amount_from_registration_bonus'),
                'created_at' => time()
            ]);

            Accounting::updateOrCreate([
                'user_id' => $userId,
                'is_registration_bonus' => true,
                'system' => true,
                'type_account' => Accounting::$income,
                'type' => Accounting::$deduction,
            ], [
                'amount' => $amount,
                'description' => trans('update.get_amount_from_registration_bonus'),
                'created_at' => time()
            ]);

            $notifyOptions = [
                '[amount]' => handlePrice($amount),
            ];
            sendNotification("registration_bonus_achieved", $notifyOptions, $userId);
        }
    }
}
