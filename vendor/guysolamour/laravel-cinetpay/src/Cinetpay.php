<?php

namespace Guysolamour\Cinetpay;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Guysolamour\Cinetpay\Exceptions\CinetpayException;

class Cinetpay extends \CinetPay\CinetPay
{
    const DEFAULT_FORM_NAME   = 'goCinetPay';

    const BUTTON_LABEL_BUY    = 1;
    const BUTTON_LABEL_PAY    = 2;
    const BUTTON_LABEL_DONATE = 3;
    const BUTTON_LABEL_PAY_WITH_CINETPAY = 4;

    const BUTTON_SIZE_SMALL = 'small';
    const BUTTON_SIZE_LARGE = 'larger';

    const TRANSACTION_KEY = 'cpm_trans_id';

    protected $pay_button_label;

    /**
     * Use the init function to initialize the object
     *
     * @param string|null $siteId
     * @param integer|null $apiKey
     * @param string $mode
     * @param string $version
     * @param null|array $params
     */
    private function __construct(
        $siteId = null,
        $apiKey = null,
        $mode = "PROD",
        $version = 'v1'
    )
    {
        $siteId = $siteId ?: $this->getSiteId();
        $apiKey = $apiKey ?: $this->getApiKey();
        $params = config('cinetpay.button.use_default_style') ? null : [];

        parent::__construct($siteId, $apiKey, $mode, $version, $params);
    }

    /**
     * Use the init function to initialize the object and set all redirect urls
     *
     * @param string|null $siteId
     * @param integer|null $apiKey
     * @param string $mode
     * @param string $version
     * @param null|array $params
     */
    public static function init(
        $siteId = null,
        $apiKey = null,
        $mode = "PROD",
        $version = 'v1',
        $params = null
    ) :self {
       $cinetpay =  new self($siteId, $apiKey, $mode, $version, $params);

       $cinetpay->setNotifyUrl();
       $cinetpay->setReturnUrl();
       $cinetpay->setCancelUrl();

       return $cinetpay;
    }

    /**
     * @param array|int|null $data
     * @return self
     */
    public static function getTransactionById($data = null)
    {
        if (is_null($data) && request(self::TRANSACTION_KEY)){
            $id = request(self::TRANSACTION_KEY);
        }elseif (is_array($data) && Arr::exists($data, self::TRANSACTION_KEY)){
            $id = Arr::get($data, self::TRANSACTION_KEY);
        }elseif (ctype_digit($data)){
            $id = $data;
        }

        $cinetpay =  self::init();

        if (isset($id) && !is_null($id)){
            $cinetpay->setTransactionId($id)->getPayStatus();
        }

        return $cinetpay;
    }

    /**
     * @param string|null $notify_url
     * @return $this
     */
    public function setNotifyUrl($notify_url = null)
    {
        if (is_null($notify_url) && Route::has('cinetpay.notify')){
            $notify_url = route('cinetpay.notify');
        }

        if (is_null($notify_url) && filter_var(config('cinetpay.urls.notify'), FILTER_VALIDATE_URL)) {
            $notify_url = config('cinetpay.urls.notify');
        }

        return parent::setNotifyUrl($notify_url);
    }

    /**
     * @param string|null $return_url
     * @return $this
     */
    public function setReturnUrl($return_url = null)
    {
        if (is_null($return_url) && Route::has('cinetpay.return')) {
            $return_url = route('cinetpay.return');
        }

        if (is_null($return_url) && filter_var(config('cinetpay.urls.return'), FILTER_VALIDATE_URL)) {
            $return_url = config('cinetpay.urls.return');
        }

        return parent::setReturnUrl($return_url);
    }

    /**
     * @param string|null $cancel_url
     * @return $this
     */
    public function setCancelUrl($cancel_url = null)
    {
        if (is_null($cancel_url) && Route::has('cinetpay.cancel')) {
            $cancel_url = route('cinetpay.cancel');
        }

        if (is_null($cancel_url) && filter_var(config('cinetpay.urls.cancel'), FILTER_VALIDATE_URL)) {
            $cancel_url = config('cinetpay.urls.cancel');
        }

        return parent::setCancelUrl($cancel_url);
    }

    /**
     *
     * @param string $identifiant
     * @return self
     */
    public function setBuyerIdentifiant($identifiant)
    {
        return $this->setCustom($identifiant);
    }

    /**
     *
     * @param string $label
     * @return self
     */
    public function setPayButtonLabel($label)
    {
        $this->pay_button_label = $label;

        return $this;
    }

    /**
     * @param string|nul $formName
     * @param int $btnType
     * @param string $size
     * @throws \Exception $e
     */
    public function displayPayButton($formName = null, $btnType = 1, $size = "larger")
    {
        if (is_null($this->_cfg_cpm_trans_date)){
            $this->setTransDate(date("Y-m-d H:i:s"));
        }

        $formName = $formName ?: self::DEFAULT_FORM_NAME;

        return $this->getPayButton($formName, $btnType, $size);
    }

    /**
     * Undocumented function
     *
     * @param string|null $buttonLabel
     * @param int $btnType
     * @param string $size
     * @return string
     */
    public function show($buttonLabel = null,$btnType = self::BUTTON_LABEL_BUY, $size = self::BUTTON_SIZE_LARGE)
    {
        if (!is_null($buttonLabel)){
            $this->setPayButtonLabel($buttonLabel);
        }
        return $this->displayPayButton(null, $btnType, $size);
    }


    /**
     * @param $formName
     * @param int $btnType
     * @param string $size
     * @return string
     */
    public function getOnlyPayButtonToSubmit($formName, $btnType = 1, $size = 'large')
    {
        $size = ($size == 'small') ? 'small' : (($size == 'larger') ? 'larger' : 'large');

        $button_label = $this->getPayButtonLabel($btnType);

        return $this->getPayButtonHtml($button_label, $size);
    }

    /**
     *
     * @return integer
     */
	public function getSiteId()
	{
		$siteId = config('cinetpay.site_id');

        if (!$siteId){
            throw new CinetpayException("Site id is invalid. Do not forget to have it in .env file [CINETPAY_SITE_ID]");
        }

        return $siteId;
	}

    /**
     *
     * @param string $id
     * @return self
     */
    public function setTransactionId(string $id)
    {
        return $this->setTransId($id);
    }

    /**
     *
     * @return string|null
     */
    public function getTransactionBuyer()
    {
        return $this->getCustom();
    }

    /**
     *
     * @return string|null
     */
    public function getCustom()
    {
        return $this->_cpm_custom;
    }

    /**
     * Get transaction created_at
     * return current date if no transaction
     *
     * @return \Carbon\Carbon
     */
    public function getTransactionDate()
    {
        return \Carbon\Carbon::parse($this->_created_at);
    }

    /**
     *
     * @return string|null
     */
    public function getTransactionCurrency()
    {
        return $this->_cpm_currency;
    }

    /**
     *
     * @return string|null
     */
    public function getTransactionPaymentMethod()
    {
        return $this->_payment_method;
    }

    /**
     *
     * @return string|null
     */
    public function getTransactionPaymentId()
    {
        return $this->_cpm_payid;
    }

    /**
     *
     * @return string|null
     */
    public function getTransactionPhoneNumber()
    {
        return $this->_cel_phone_num;
    }

    /**
     *
     * @return string|null
     */
    public function getTransactionPhonePrefix()
    {
        return $this->_cpm_phone_prefixe;
    }

    /**
     *
     * @return string|null
     */
    public function getTransactionLanguage()
    {
        return $this->_cfg_cpm_language;
    }

    /**
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->_cpm_trans_id;
    }

    public function __toString()
    {
        return $this->show();
    }

    /**
     *
     * @param string $label
     * @param string|null $class
     * @return string
     */
    private function getPayButtonHtml($label, $class = null)
    {
        $html = config('cinetpay.button.html', "<button :class :id :attributes :style>:label</button>");

        $html = str_replace(":label", $label, $html);

        if (config('cinetpay.button.use_default_style')) {
            $html = $this->addHtmlClassAttributeToPayButton($html, " {$class}");
            return $this->removePlaceholdersInPayButton($html);
        }

        if ($custom_class = config('cinetpay.button.class')) {
            $html = $this->addHtmlClassAttributeToPayButton($html, "{$custom_class} {$class}");
        }

        if ($custom_id = config('cinetpay.button.id')) {
            $html = $this->addHtmlIdAttributeToPayButton($html, $custom_id);
        }

        if ($custom_attributes = config('cinetpay.button.attributes')) {
            $custom_attributes_html = '';

            foreach ($custom_attributes as $key => $value) {
                $custom_attributes_html .= "{$key}='{$value}' ";
            }

            $html = $this->addHtmlAttributeToPayButton($html, ":attributes", $custom_attributes_html);
        }

        if ($custom_style = config('cinetpay.button.style')) {
            $html = $this->addHtmlAttributeToPayButton($html, ":style", "style='{$custom_style}'");
        }


        return $html;
    }

    /**
     *
     * @param string $html
     * @return string
     */
    private function removePlaceholdersInPayButton(string $html)
    {
        $html = $this->addHtmlAttributeToPayButton($html, ":attributes", "");
        $html = $this->addHtmlAttributeToPayButton($html, ":style", "");
        return $this->addHtmlIdAttributeToPayButton($html, "");
    }

    /**
     *
     * @param string $html
     * @param string $attribute
     * @param string $value
     * @return string
     */
    private function addHtmlAttributeToPayButton($html, $attribute, $value)
    {
        return str_replace("{$attribute}", $value, $html);
    }

    /**
     *
     * @param string $html
     * @param string $value
     * @return string
     */
    private function addHtmlClassAttributeToPayButton($html, $value)
    {
        return $this->addHtmlAttributeToPayButton($html, ":class", " class='{$value} ' ");
    }

    /**
     *
     * @param string $html
     * @param string $value
     * @return string
     */
    private function addHtmlIdAttributeToPayButton($html, $value)
    {
        return $this->addHtmlAttributeToPayButton($html, ":id", " id='{$value}'");
    }

    /**
     *
     * @param integer $btnType
     * @return string
     */
    private function getPayButtonLabel(int $btnType)
    {
        if ($label = $this->pay_button_label) {
            return $label;
        }

        if ($btnType == self::BUTTON_LABEL_BUY) {
            $label =   Lang::get('cinetpay::translations.button_label_buy');
        } elseif ($btnType == self::BUTTON_LABEL_PAY) {
            $label =   Lang::get('cinetpay::translations.button_label_pay');
        } elseif ($btnType == self::BUTTON_LABEL_DONATE) {
            $label =   Lang::get('cinetpay::translations.button_label_donate');
        } elseif ($btnType == self::BUTTON_LABEL_PAY_WITH_CINETPAY) {
            $label =   Lang::get('cinetpay::translations.button_label_pay_with_cinetpay');
        } else {
            $label =   Lang::get('cinetpay::translations.button_label_pay_now');
        }

        return $label;
    }

    /**
     *
     * @return string
     */
    private function getApiKey()
    {
        $apiKey = config('cinetpay.api_key');

        if (!$apiKey) {
            throw new CinetpayException("Api key is invalid. Do not forget to have it in .env file [CINETPAY_API_KEY]");
        }

        return $apiKey;
    }
}
