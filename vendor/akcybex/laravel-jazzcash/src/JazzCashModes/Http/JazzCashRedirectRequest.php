<?php

namespace AKCybex\JazzCash\JazzCashModes\Http;

use AKCybex\JazzCash\Models\JazzCashTransaction;

class JazzCashRedirectRequest
{
    /**
     * @var array
     */
    private $data = [];

    /**
     *
     */
    public function __construct()
    {
        $environment = config('jazzcash.environment');

        $this->data = [
            'pp_MerchantID'        => config("jazzcash.$environment.merchant_id"),
            'pp_Password'          => config("jazzcash.$environment.password"),
            'pp_ReturnURL'         => config("jazzcash.$environment.return_url"),
            'pp_HashKey'           => config("jazzcash.$environment.integerity_salt"),
            'pp_Amount'            => 0,
            'pp_BillReference'     => 'billRef',
            'pp_SubMerchantID'     => '',
            'pp_Description'       => 'Thank you for using Jazz Cash',
            'pp_Language'          => 'EN',
            'pp_TxnCurrency'       => 'PKR',
            'pp_TxnDateTime'       => date('YmdHis'),
            'pp_TxnExpiryDateTime' => date('YmdHis', strtotime('+8 Days')),
            'pp_TxnRefNo'          => "T" . date('YmdHis'),
            'pp_TxnType'           => 'MWALLET',
            'pp_Version'           => '1.1',
            'pp_DiscountedAmount'  => '',
            'pp_DiscountedBank'    => '',
            'pp_ProductID'         => 'RETL',
            'pp_BankID'            => 'TBANK',
            'ppmpf_1'              => '1',
            'ppmpf_2'              => '2',
            'ppmpf_3'              => '3',
            'ppmpf_4'              => '4',
            'ppmpf_5'              => '5',
        ];
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->data['pp_Amount'] = $amount * 100;
        return $this;
    }

    /**
     * @param $returnURL
     *
     * @return $this
     */
    public function setReturnURL($returnURL)
    {
        $this->data['pp_ReturnURL'] = $returnURL;
        return $this;
    }

    /**
     * @param $billReference
     *
     * @return $this
     */
    public function setBillReference($billReference)
    {
        $this->data['pp_BillReference'] = $billReference;
        return $this;
    }

    /**
     * @param $subMerchantID
     *
     * @return $this
     */
    public function setSubMerchantID($subMerchantID)
    {
        $this->data['pp_SubMerchantID'] = $subMerchantID;
        return $this;
    }

    /**
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->data['pp_Description'] = $description;
        return $this;
    }

    /**
     * @param $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->data['pp_Language'] = $language;
        return $this;
    }

    /**
     * @param $txnCurrency
     *
     * @return $this
     */
    public function setTransactionCurrency($txnCurrency)
    {
        $this->data['pp_TxnCurrency'] = $txnCurrency;
        return $this;
    }

    /**
     * @param $txnDateTime
     *
     * @return $this
     */
    public function setTransactionDateTime($txnDateTime)
    {
        $this->data['pp_TxnDateTime'] = $txnDateTime;
        return $this;
    }

    /**
     * @param $txnExpiryDateTime
     *
     * @return $this
     */
    public function setTransactionExpiryDateTime($txnExpiryDateTime)
    {
        $this->data['pp_TxnExpiryDateTime'] = $txnExpiryDateTime;
        return $this;
    }

    /**
     * @param $txnReferenceNo
     *
     * @return $this
     */
    public function setTransactionReferenceNumber($txnReferenceNo)
    {
        $this->data['pp_TxnRefNo'] = $txnReferenceNo;
        return $this;
    }

    /**
     * @param $txnType
     *
     * @return $this
     */
    public function setTransactionType($txnType)
    {
        $this->data['pp_TxnType'] = $txnType;
        return $this;
    }

    /**
     * @param $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->data['pp_Version'] = $version;
        return $this;
    }

    /**
     * @param $discountedAmount
     *
     * @return $this
     */
    public function setDiscountedAmount($discountedAmount)
    {
        $this->data['pp_DiscountedAmount'] = $discountedAmount;
        return $this;
    }

    /**
     * @param $discountedBank
     *
     * @return $this
     */
    public function setDiscountedBank($discountedBank)
    {
        $this->data['pp_DiscountedBank'] = $discountedBank;
        return $this;
    }

    /**
     * @param $productID
     *
     * @return $this
     */
    public function setProductID($productID)
    {
        $this->data['pp_ProductID'] = $productID;
        return $this;
    }

    /**
     * @param $bankID
     *
     * @return $this
     */
    public function setBankID($bankID)
    {
        $this->data['pp_BankID'] = $bankID;
        return $this;
    }

    /**
     * @param $mpf1
     *
     * @return $this
     */
    public function setMPF1($mpf1)
    {
        $this->data['ppmpf_1'] = $mpf1;
        return $this;
    }

    /**
     * @param $mpf2
     *
     * @return $this
     */
    public function setMPF2($mpf2)
    {
        $this->data['ppmpf_2'] = $mpf2;
        return $this;
    }

    /**
     * @param $mpf3
     *
     * @return $this
     */
    public function setMPF3($mpf3)
    {
        $this->data['ppmpf_3'] = $mpf3;
        return $this;
    }

    /**
     * @param $mpf4
     *
     * @return $this
     */
    public function setMPF4($mpf4)
    {
        $this->data['ppmpf_4'] = $mpf4;
        return $this;
    }

    /**
     * @param $mpf5
     *
     * @return $this
     */
    public function setMPF5($mpf5)
    {
        $this->data['ppmpf_5'] = $mpf5;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray($order = [])
    {
        $this->generateSecureHash();
        JazzCashTransaction::create([
            'txn_ref_no' => $this->data['pp_TxnRefNo'],
            'order'      => $order,
            'request'    => $this->data,
        ]);
        return $this->data;
    }

    /**
     *
     */
    private function generateSecureHash()
    {
        ksort($this->data);

        $str = '';
        foreach ($this->data as $key => $value) {
            if (!empty($value) && $key !== 'pp_HashKey') {
                $str = $str . '&' . $value;
            }
        }

        $str = $this->data['pp_HashKey'] . $str;

        $this->data['pp_SecureHash'] = hash_hmac('sha256', $str, $this->data['pp_HashKey']);
    }
}
