<?php

namespace App\PaymentChannels\Drivers\Redsys;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Ssheduardo\Redsys\Facades\Redsys;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $order_session_key;
    protected $key;
    protected $merchantCode;
    protected $tradename;
    protected $terminal;
    protected $test_mode;

    protected array $credentialItems = [
        'key',
        'merchantCode',
        'terminal',
        'tradename',
    ];

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = '978';//currency();
        $this->order_session_key = 'redsys.payments.order_id';
        $this->setCredentialItems($paymentChannel);
    }

    /**
     * @throws \Exception
     */
    public function paymentRequest(Order $order)
    {
        // Send purchase request
        try {
            //$user = $order->user;

            Redsys::setAmount($this->makeAmountByCurrency($order->total_amount, $this->currency));
            Redsys::setOrder(time());
            Redsys::setMerchantcode($this->merchantCode); //Reemplazar por el código que proporciona el banco
            Redsys::setCurrency($this->currency);
            Redsys::setTransactiontype('0');
            Redsys::setTerminal($this->terminal);
            Redsys::setMethod('T'); //Solo pago con tarjeta, no mostramos iupay
            Redsys::setNotification($this->makeCallbackUrl($order, 'success')); //Url de notificacion
            Redsys::setUrlOk($this->makeCallbackUrl($order, 'ok')); //Url OK
            Redsys::setUrlKo($this->makeCallbackUrl($order, 'ko')); //Url KO
            Redsys::setVersion('HMAC_SHA256_V1');
            Redsys::setTradeName('Tienda S.L');
            Redsys::setTitular("pay order " . $order->id);
            Redsys::setProductDescription("pay order " . $order->id);
            Redsys::setEnviroment($this->test_mode ? 'test' : 'live'); //Entorno test

            $signature = Redsys::generateMerchantSignature($this->key);
            Redsys::setMerchantSignature($signature);

            $form = Redsys::createForm();

            session()->put($this->order_session_key, $order->id);

            return $form;
        } catch (\Exception $exception) {
            dd($exception);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }

    private function makeCallbackUrl($order, $status)
    {
        return url("/payments/verify/Redsys?status=$status&order_id=$order->id");
    }

    public function verify(Request $request)
    {
        try {
            $orderId = session()->get($this->order_session_key, null);
            session()->forget($this->order_session_key);

            $user = auth()->user();

            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->with('user')
                ->first();

            if (!empty($order)) {
                $orderStatus = Order::$fail;

                $parameters = Redsys::getMerchantParameters($request->input('Ds_MerchantParameters'));
                $DsResponse = $parameters["Ds_Response"];
                $DsResponse += 0;

                if (Redsys::check($this->key, $request->input()) && $DsResponse <= 99) {
                    // lo que quieras que haya si es positiva la confirmación de redsys

                    $orderStatus = Order::$paying;
                }

                $order->update([
                    'status' => $orderStatus,
                ]);
            }

            return $order;

        } catch (\Exception $exception) {
            //dd($exception);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
