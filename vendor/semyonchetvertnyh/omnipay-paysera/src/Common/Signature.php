<?php

namespace Omnipay\Paysera\Common;

use Omnipay\Common\Http\ClientInterface;

class Signature
{
    /**
     * Endpoint to the public key.
     *
     * @var string
     */
    const ENDPOINT = 'http://www.paysera.com/download/public.key';

    /**
     * Generate the signature.
     *
     * @param  string  $data
     * @param  string  $password
     * @return string
     */
    public static function generate($data, $password)
    {
        return md5($data.$password);
    }

    /**
     * Determine the whole signature is valid.
     *
     * @param  array  $data
     * @param  string  $password
     * @param  \Omnipay\Common\Http\ClientInterface  $client
     * @return bool
     */
    public static function isValid(array $data, $password, ClientInterface $client)
    {
        return static::isValidSS1($data, $password) && static::isValidSS2($data, $client);
    }

    /**
     * Determine the SS1 is valid.
     *
     * @param  array  $data
     * @param  string  $password
     * @return bool
     */
    protected static function isValidSS1(array $data, $password)
    {
        return static::generate($data['data'], $password) === $data['ss1'];
    }

    /**
     * Determine the SS2 is valid.
     *
     * @param  array  $data
     * @param  \Omnipay\Common\Http\ClientInterface  $client
     * @return bool
     */
    protected static function isValidSS2(array $data, ClientInterface $client)
    {
        $response = $client->request('GET', self::ENDPOINT);

        $publicKey = openssl_get_publickey($response->getBody());

        if ($response->getStatusCode() === 200 && $publicKey !== false) {
            return openssl_verify($data['data'], Encoder::decode($data['ss2']), $publicKey) === 1;
        }

        return false;
    }
}
