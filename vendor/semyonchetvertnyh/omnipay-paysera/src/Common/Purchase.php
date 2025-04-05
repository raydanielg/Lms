<?php

namespace Omnipay\Paysera\Common;

use Omnipay\Paysera\Message\PurchaseRequest;
use Omnipay\Common\Exception\InvalidRequestException;

class Purchase
{
    /**
     * Generate the encoded string with parameters.
     *
     * @param  \Omnipay\Paysera\Message\PurchaseRequest  $request
     * @return string
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public static function generate(PurchaseRequest $request)
    {
        $parameters = static::parameters($request);

        if ($card = $request->getCard()) {
            $parameters = array_merge($parameters, static::customer($card));
        }

        static::validate($parameters = self::filterParameters($parameters));

        return Encoder::encode(http_build_query($parameters, '', '&'));
    }

    /**
     * Get the parameters from the request.
     *
     * @param  \Omnipay\Paysera\Message\PurchaseRequest  $request
     * @return array
     */
    protected static function parameters(PurchaseRequest $request)
    {
        return [
            'projectid' => $request->getProjectId(),
            'orderid' => $request->getTransactionId(),
            'accepturl' => $request->getReturnUrl(),
            'cancelurl' => $request->getCancelUrl(),
            'callbackurl' => $request->getNotifyUrl(),
            'version' => $request->getVersion(),
            'payment' => $request->getPaymentMethod(),
            'lang' => $request->getLanguage(),
            'amount' => $request->getAmountInteger(),
            'currency' => $request->getCurrency(),
            'test' => $request->getTestMode() ? '1' : '0',
        ];
    }

    /**
     * Get the customer from card.
     *
     * @param  \Omnipay\Common\CreditCard  $card
     * @return array
     */
    protected static function customer($card)
    {
        return [
            'p_firstname' => $card->getFirstName(),
            'p_lastname' => $card->getLastName(),
            'p_email' => $card->getEmail(),
            'p_street' => $card->getAddress1(),
            'p_city' => $card->getCity(),
            'p_state' => $card->getState(),
            'p_zip' => $card->getPostcode(),
            'country' => $card->getCountry(),
        ];
    }

    /**
     * Get the filtered parameters.
     *
     * @param  array  $parameters
     * @return array
     */
    protected static function filterParameters(array $parameters)
    {
        return array_filter($parameters, function ($value) {
            return ! is_null($value) && $value !== '';
        });
    }

    /**
     * Validate the data.
     *
     * @param  array  $data
     * @return void
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public static function validate(array $data)
    {
        foreach (self::getRequestSpecifications() as $specification) {
            list($name, $maxLength, $isRequired, $regexp) = $specification;

            if (static::isRequiredButMissing($isRequired, $data, $name)) {
                throw new InvalidRequestException("Parameter [{$name}] is required but missing.");
            }

            if (static::exists($data, $name)) {
                if (static::isTooLong($maxLength, $data[$name])) {
                    throw new InvalidRequestException(
                        sprintf(
                            'Parameter [%s] value is too long (%d), %d characters allowed.',
                            $name,
                            strlen($data[$name]),
                            $maxLength
                        )
                    );
                }

                if (static::isInvalid($regexp, $data[$name])) {
                    throw new InvalidRequestException("Parameter [{$name}] value [{$data[$name]}] is invalid.");
                }
            }
        }
    }

    /**
     * Get the request specifications.
     *
     * Array structure:
     *   name      – request parameter name
     *   maxLength – max allowed length for parameter
     *   required  – is this parameter required
     *   regexp    – regexp to test parameter value
     *
     * @return array
     */
    protected static function getRequestSpecifications()
    {
        return [
            ['orderid', 40, true, ''],
            ['accepturl', 255, true, ''],
            ['cancelurl', 255, true, ''],
            ['callbackurl', 255, true, ''],
            ['lang', 3, false, '/^[a-z]{3}$/i'],
            ['amount', 11, false, '/^\d+$/'],
            ['currency', 3, false, '/^[a-z]{3}$/i'],
            ['payment', 20, false, ''],
            ['country', 2, false, '/^[a-z_]{2}$/i'],
            ['p_firstname', 255, false, ''],
            ['p_lastname', 255, false, ''],
            ['p_email', 255, false, ''],
            ['p_street', 255, false, ''],
            ['p_city', 255, false, ''],
            ['p_state', 20, false, ''],
            ['p_zip', 20, false, ''],
            ['p_countrycode', 2, false, '/^[a-z]{2}$/i'],
            ['test', 1, false, '/^[01]$/'],
        ];
    }

    /**
     * Determine the parameter is required but missing.
     *
     * @param  bool  $isRequired
     * @param  array  $data
     * @param  string  $name
     * @return bool
     */
    protected static function isRequiredButMissing($isRequired, $data, $name)
    {
        return $isRequired && ! isset($data[$name]);
    }

    /**
     * Determine the parameter exists.
     *
     * @param  array  $data
     * @param  string  $name
     * @return bool
     */
    protected static function exists($data, $name)
    {
        return ! empty($data[$name]);
    }

    /**
     * Determine the parameter value is too long.
     *
     * @param  mixed  $maxLength
     * @param  string  $value
     * @return bool
     */
    protected static function isTooLong($maxLength, $value)
    {
        return $maxLength && strlen($value) > $maxLength;
    }

    /**
     * Determine the parameter value is invalid.
     *
     * @param  string  $regexp
     * @param  string  $value
     * @return bool
     */
    protected static function isInvalid($regexp, $value)
    {
        return $regexp !== '' && ! preg_match($regexp, $value);
    }
}
