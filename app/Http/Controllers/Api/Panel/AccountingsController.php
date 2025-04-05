<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\Api\Web\WebinarController;
use App\Http\Controllers\Api\Panel\PaymentsController;
use App\Models\Api\Accounting;
use App\Models\OfflineBank;
use App\Models\OfflinePayment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\PaymentChannels\ChannelManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class AccountingsController extends Controller
{
    public function summary()
    {
        $user = apiAuth();
        $accountings = Accounting::where('user_id', $user->id)
            ->where('system', false)
            ->where('tax', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($accounting) {
                return $accounting->details ;
            });


        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'balance'=> $user->getAccountingBalance() ,
            'history'=>$accountings
        ]);

    }

    public function listOfflinePayment()
    {
        $user = apiAuth();
        $offlinePayments = OfflinePayment::where('user_id', $user->id)->orderBy('created_at', 'desc')->get()
            ->map(function ($offlinePayment) {
                return [
                    'amount' => $offlinePayment->amount,
                    'bank' => $offlinePayment->bank,
                    'reference_number' => $offlinePayment->reference_number,
                    'status' => $offlinePayment->status,
                    'created_at' => $offlinePayment->created_at,
                    'pay_date' => $offlinePayment->pay_date,
                ];

            });
        return apiResponse2(1, 'retrieved', trans('public.retrieved'), $offlinePayments);

    }

    public function platformBankAccounts(){

        $accounts=[] ;
        $model = OfflineBank::query()
            ->orderBy('created_at', 'desc')
            ->with([
                'specifications'
            ])
            ->get()->all();
        foreach($model as $account){

            if(isset($account['image'])){
                $account['image']=url( $account['image']) ;
            }
            $accounts[]=$account ;
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),[
            'accounts'=>$accounts
        ]);

    }

    public function accountTypes(){

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),[
            'accounts_type'=> getOfflineBanksTitle()
        ]);
    }







}
