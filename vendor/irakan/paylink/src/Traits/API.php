<?php

namespace Paylink\Traits;

trait API
{
    use HttpClient;

    public function authorize()
    {
        $response = $this->sendRequest('post', 'auth', [
            'apiId' => $this->getVendorId(),
            'secretKey' => $this->getVendorSecret(),
            'persistToken' => $this->getPersistToken(),
        ]);

        $this->setAccessToken($token = $response['id_token']);

        return $token;
    }

    public function createInvoice(array $data)
    {
        if (! $token = $this->getAccessToken()) {
            $token = $this->authorize();
        }

        return $this->sendRequest('post', 'addInvoice', $data, $token);
    }

    public function getInvoice(string $transactionNo)
    {
        if (! $token = $this->getAccessToken()) {
            $token = $this->authorize();
        }

        return $this->sendRequest('get', "getInvoice/${transactionNo}", [], $token);
    }
}
