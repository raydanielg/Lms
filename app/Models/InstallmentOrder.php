<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class InstallmentOrder extends Model
{
    protected $table = 'installment_orders';
    public $timestamps = false;
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class, 'installment_id', 'id');
    }

    public function selectedInstallment()
    {
        return $this->hasOne(SelectedInstallment::class, 'installment_order_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo(Webinar::class, 'webinar_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id', 'id');
    }

    public function subscribe()
    {
        return $this->belongsTo(Subscribe::class, 'subscribe_id', 'id');
    }

    public function registrationPackage()
    {
        return $this->belongsTo(RegistrationPackage::class, 'registration_package_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(InstallmentOrderPayment::class, 'installment_order_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(InstallmentOrderAttachment::class, 'installment_order_id', 'id');
    }


    /********
     * Helpers
     * */

    public function getItem()
    {
        $item = null;

        if (!empty($this->webinar_id)) {
            $item = $this->webinar;
        } else if (!empty($this->bundle_id)) {
            $item = $this->bundle;
        } else if (!empty($this->product_id)) {
            $item = $this->product;
        } else if (!empty($this->subscribe_id)) {
            $item = $this->subscribe;
        } else if (!empty($this->registration_package_id)) {
            $item = $this->registrationPackage;
        }

        return $item;
    }

    public function getItemType()
    {
        $type = null;

        if (!empty($this->webinar_id)) {
            $type = 'course';
        } else if (!empty($this->bundle_id)) {
            $type = "bundle";
        } else if (!empty($this->product_id)) {
            $type = "product";
        } else if (!empty($this->subscribe_id)) {
            $type = "subscribe";
        } else if (!empty($this->registration_package_id)) {
            $type = "registrationPackage";
        }

        return $type;
    }

    public function getItemPrice()
    {
        return $this->attributes['item_price'];
    }

    public function isCompleted()
    {
        $result = false;

        if ($this->status == "open") {
            $installment = $this->selectedInstallment;
            $installmentSteps = $installment->steps;

            $paid = true;
            foreach ($installmentSteps as $step) {
                if ($paid) {
                    $payment = InstallmentOrderPayment::query()
                        ->where('installment_order_id', $this->id)
                        ->where('selected_installment_step_id', $step->id)
                        ->where('status', 'paid')
                        ->first();

                    if (empty($payment)) {
                        $paid = false;
                    }
                }
            }

            $result = $paid;
        }

        return $result;
    }

    public function checkOrderHasOverdue()
    {
        $result = false;
        $time = time();

        if ($this->status == 'open') {
            foreach ($this->selectedInstallment->steps as $step) {
                $dueAt = ($step->deadline * 86400) + $this->created_at;

                if ($time > $dueAt) {
                    $payment = InstallmentOrderPayment::query()
                        ->where('installment_order_id', $this->id)
                        ->where('selected_installment_step_id', $step->id)
                        ->where('status', 'paid')
                        ->first();

                    if (empty($payment)) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }

    public function getOrderOverdueCountAndAmount()
    {
        $count = 0;
        $amount = 0;
        $time = time();

        $itemPrice = $this->getItemPrice();

        if ($this->status == 'open' and !empty($itemPrice)) {
            foreach ($this->selectedInstallment->steps as $step) {
                $dueAt = ($step->deadline * 86400) + $this->created_at;

                if ($time > $dueAt) {
                    $payment = InstallmentOrderPayment::query()
                        ->where('installment_order_id', $this->id)
                        ->where('selected_installment_step_id', $step->id)
                        ->where('status', 'paid')
                        ->first();

                    if (empty($payment)) {
                        $count += 1;
                        $amount += $step->getPrice($itemPrice);
                    }
                }
            }
        }

        return [
            'count' => $count,
            'amount' => $amount,
        ];
    }

    public function overdueDaysPast()
    {
        $result = 0;
        $time = time();

        if ($this->status == 'open') {
            foreach ($this->selectedInstallment->steps as $step) {
                $dueAt = ($step->deadline * 86400) + $this->created_at;

                if ($time > $dueAt) {
                    $payment = InstallmentOrderPayment::query()
                        ->where('installment_order_id', $this->id)
                        ->where('selected_installment_step_id', $step->id)
                        ->where('status', 'paid')
                        ->first();

                    if (empty($payment)) {
                        $daysPast = ($time - $dueAt) / (86400);

                        $result = $daysPast > 0 ? $daysPast : 0;
                    }
                }
            }
        }

        return $result;
    }

    public function getCompletePrice($itemPrice = null)
    {
        if (empty($itemPrice)) {
            $itemPrice = $this->getItemPrice();
        }

        $amount = 0;
        $installment = $this->selectedInstallment;

        if (!empty($installment)) {
            $amount = $installment->getUpfront($itemPrice);

            foreach ($installment->steps as $step) {
                $amount += $step->getPrice($itemPrice);
            }
        }

        return $amount;
    }



}
