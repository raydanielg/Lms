<?php

namespace App\Mixins\Cashback;

use App\Models\Accounting;
use App\Models\CashbackRule;
use App\Models\Order;
use App\Models\Product;
use App\Models\Webinar;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class CashbackAccounting
{
    private $user;
    private $cashbackRules;

    public function __construct($user = null)
    {
        if (empty($user)) {
            $user = auth()->user();
        }

        $this->user = $user;
        $this->cashbackRules = new CashbackRules($user);
    }

    public function rechargeWallet($order)
    {
        $amount = 0;

        if (getFeaturesSettings('cashback_active')) {
            $totalAmount = $order->total_amount;
            $rules = $this->cashbackRules->getRules('recharge_wallet');

            if (!empty($rules) and count($rules)) {
                foreach ($rules as $rule) {
                    if (empty($rule->min_amount) or $rule->min_amount <= $totalAmount) {
                        $ruleAmount = $rule->getAmount($totalAmount);

                        if (!empty($rule->max_amount) and $rule->max_amount > $ruleAmount) {
                            $amount += $rule->max_amount;
                        } else {
                            $amount += $ruleAmount;
                        }
                    }
                }
            }

            if ($amount > 0) {
                $description = 'Cashback for recharge wallet';

                $this->setAccounting($order->user_id, $amount, $description);
            }
        }
    }

    public function setAccountingForOrderItems($orderItems)
    {
        if (getFeaturesSettings('cashback_active')) {
            $applyPerItemRules = [];
            $totalAmount = $orderItems->sum('total_amount');

            foreach ($orderItems as $orderItem) {
                $itemPrice = $orderItem->amount - $orderItem->discount;
                $rules = $this->cashbackRules->getRulesByItem($orderItem);

                if (!empty($rules) and count($rules)) {
                    foreach ($rules as $rule) {
                        $amount = 0;

                        if (empty($rule->min_amount) or $rule->min_amount <= $totalAmount) {
                            if ($rule->amount_type == "fixed_amount") {
                                if ($rule->apply_cashback_per_item) {
                                    $amount += $rule->amount;
                                } else {
                                    $applyPerItemRules[$rule->id] = $rule;
                                }
                            } else if ($rule->amount_type == "percent") {
                                $ruleAmount = $rule->getAmount($itemPrice);

                                if (!empty($rule->max_amount) and $rule->max_amount < $ruleAmount) {
                                    $amount += $rule->max_amount;
                                } else {
                                    $amount += $ruleAmount;
                                }
                            }
                        }

                        if ($amount > 0) {
                            $itemId = null;
                            $itemName = null;
                            $description = null;

                            if (!empty($orderItem->webinar_id)) {
                                $itemId = $orderItem->webinar_id;
                                $itemName = 'webinar_id';
                                $description = 'Cashback For Course';
                            } elseif (!empty($orderItem->reserve_meeting_id)) {
                                $itemId = $orderItem->reserveMeeting ? $orderItem->reserveMeeting->meeting_time_id : null;
                                $itemName = 'meeting_time_id';
                                $description = 'Cashback For Meeting';
                            } elseif (!empty($orderItem->subscribe_id)) {
                                $itemId = $orderItem->subscribe_id;
                                $itemName = 'subscribe_id';
                                $description = 'Cashback For Subscribe';
                            } elseif (!empty($orderItem->registration_package_id)) {
                                $itemId = $orderItem->registration_package_id;
                                $itemName = 'registration_package_id';
                                $description = 'Cashback For registration package';
                            } elseif (!empty($orderItem->product_id)) {
                                $itemId = $orderItem->product_id;
                                $itemName = 'product_id';
                                $description = 'Cashback For Product';
                            } elseif (!empty($orderItem->bundle_id)) {
                                $itemId = $orderItem->bundle_id;
                                $itemName = 'bundle_id';
                                $description = 'Cashback For Bundle';
                            }

                            $this->setAccounting($orderItem->user_id, $amount, $description, $orderItem->id, $itemId, $itemName);
                        }
                    }
                }
            }

            if (!empty($applyPerItemRules)) {
                $amount = 0;

                foreach ($applyPerItemRules as $applyPerItemRule) {
                    $amount += $applyPerItemRule->amount;
                }

                if ($amount > 0) {
                    $description = "Cashback For all cart items";
                    $this->setAccounting($this->user->id, $amount, $description);
                }
            }
        }
    }

    private function setAccounting($userId, $amount, $description, $orderItemId = null, $itemId = null, $itemName = null)
    {
        $user = User::query()->find($userId);

        if (!empty($user) and !$user->disable_cashback) {
            $insert = [
                'user_id' => $userId,
                'order_item_id' => $orderItemId ?? null,
                'amount' => $amount,
                'is_cashback' => true,
                'type_account' => Order::$asset,
                'type' => Order::$addiction,
                'description' => $description,
                'created_at' => time()
            ];

            if (!empty($itemName) and !empty($itemId)) {
                $insert[$itemName] = $itemId;
            }

            // User addiction
            Accounting::create($insert);

            // System Deduction
            $insert['type'] = Order::$deduction;
            $insert['system'] = true;

            Accounting::create($insert);

            $this->sendNotification($user, $amount);
        }
    }

    private function sendNotification($user, $amount)
    {
        $notifyOptions = [
            '[u.name]' => $user->full_name,
            '[amount]' => handlePrice($amount),
        ];

        sendNotification('user_get_cashback', $notifyOptions, $user->id);
        sendNotification('user_get_cashback_notification_for_admin', $notifyOptions, 1);
    }
}
