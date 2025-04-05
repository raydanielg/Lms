<?php

namespace AKCybex\JazzCash\JazzCashModes\Http;

use AKCybex\JazzCash\Models\JazzCashTransaction;

class JazzCashRedirectResponse
{
    /**
     * @var array
     */
    private $data = [];

    private $transaction;

    /**
     *
     */
    public function __construct()
    {
        $this->data = request()->all();


        $this->transaction = JazzCashTransaction::where('txn_ref_no', $this->transactionReferenceNumber())
                                                ->where('status', 'pending')
                                                ->latest()
                                                ->first();

        if ($this->transaction) {
            if ($this->code() === 000) {
                $this->transaction->status = 'completed';
            } else {
                $this->transaction->status = 'error';
            }
            $this->transaction->response = $this->data;
            $this->transaction->save();
        }
    }

    public function transactionReferenceNumber()
    {
        return $this->data['pp_TxnRefNo'] ?? null;
    }

    public function code()
    {
        return $this->data['pp_ResponseCode'] ?? null;
    }

    public function order()
    {
        return $this->transaction->order;
    }

    public function request()
    {
        return $this->transaction->order;
    }

    public function response()
    {
        return $this->transaction->order;
    }

    public function amount()
    {
        return $this->data['pp_Amount'] ?? 0;
    }

    public function authCode()
    {
        return $this->data['pp_AuthCode'] ?? null;
    }

    public function bankID()
    {
        return $this->data['pp_BankID'] ?? '';
    }

    public function billReference()
    {
        return $this->data['pp_BillReference'] ?? '';
    }

    public function language()
    {
        return $this->data['pp_Language'] ?? '';
    }

    public function merchantID()
    {
        return $this->data['pp_MerchantID'] ?? '';
    }

    public function message()
    {
        return $this->data['pp_ResponseMessage'] ?? '';
    }

    public function retreivalReferenceNo()
    {
        return $this->data['pp_RetreivalReferenceNo'] ?? '';
    }

    public function secureHash()
    {
        return $this->data['pp_SecureHash'] ?? '';
    }

    public function settlementExpiry()
    {
        return $this->data['pp_SettlementExpiry'] ?? null;
    }

    public function subMerchantId()
    {
        return $this->data['pp_SubMerchantId'] ?? null;
    }

    public function transactionCurrency()
    {
        return $this->data['pp_TxnCurrency'] ?? 'PKR';
    }

    public function transactionDateTime()
    {
        return $this->data['pp_TxnDateTime'] ?? null;
    }

    public function transactionType()
    {
        return $this->data['pp_TxnType'] ?? null;
    }

    public function version()
    {
        return $this->data['pp_Version'] ?? null;
    }

    public function ppmpf1()
    {
        return $this->data['ppmpf_1'] ?? null;
    }

    public function ppmpf2()
    {
        return $this->data['ppmpf_2'] ?? null;
    }

    public function ppmpf3()
    {
        return $this->data['ppmpf_3'] ?? null;
    }

    public function ppmpf4()
    {
        return $this->data['ppmpf_4'] ?? null;
    }

    public function ppmpf5()
    {
        return $this->data['ppmpf_5'] ?? null;
    }


}
