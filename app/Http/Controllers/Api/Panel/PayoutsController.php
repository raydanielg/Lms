<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\UserBankResource;
use App\Http\Resources\UserSelectedBankResource;
use App\Models\Api\Payout;
use Illuminate\Http\Request;

class PayoutsController extends Controller
{
    public function index(Request $request)
    {
        $user = apiAuth();
        $payouts = Payout::where('user_id', $user->id)
            //->where('status',Payout::$done)
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $getFinancialSettings = getFinancialSettings();

        $currentPayout = $this->getCurrentPayout($request, $user);

        return apiResponse2(1, 'retrieved', trans('public.retrieved'), [
            'payouts' => $payouts->map(function ($payout) {
                return $payout->details;
            }),
            'current_payout' => $currentPayout,
            'settings' => [
                'minimum_payout' => (!empty($getFinancialSettings['minimum_payout'])) ? $getFinancialSettings['minimum_payout'] : null,
            ]
        ]);

    }

    private function getCurrentPayout(Request $request, $user)
    {
        $accountCharge = $user->getAccountingCharge();
        $totalIncome = $user->getIncome();
        $withdrawableAmount = $user->getPayout();

        $result = [
            'account_charge' => !empty($accountCharge) ? round((float)$accountCharge, 2) : 0,
            'total_income' => !empty($totalIncome) ? round($totalIncome, 2) : 0,
            'withdrawable_amount' => !empty($withdrawableAmount) ? round($withdrawableAmount, 2) : 0,
            'bank' => null,
            'bank_specifications' => [],
        ];


        if (!empty($user->selectedBank) and !empty($user->selectedBank->bank)) {
            $result['bank'] = (new UserBankResource($user->selectedBank->bank))->toArray($request);

            // Specifications
            $specifications = [];
            foreach ($user->selectedBank->bank->specifications as $specification) {
                $selectedBankSpecification = $user->selectedBank
                    ->specifications
                    ->where('user_selected_bank_id', $user->selectedBank->id)
                    ->where('user_bank_specification_id', $specification->id)
                    ->first();

                $specifications[] = [
                    'name' => $specification->name,
                    'value' => (!empty($selectedBankSpecification)) ? $selectedBankSpecification->value : null,
                ];
            }

            $result['bank_specifications'] = $specifications;
        }

        return $result;
    }

    public function requestPayout()
    {
        $user = apiAuth();
        $getUserPayout = $user->getPayout();
        $getFinancialSettings = getFinancialSettings();

        if (!empty($getFinancialSettings['minimum_payout']) and $getUserPayout < $getFinancialSettings['minimum_payout']) {
            return apiResponse2(0, 'minimum_payout',
                trans('public.income_los_then_minimum_payout'),
                null,
                trans('public.request_failed')
            );
        }

        if (!$user->financial_approval) {
            return apiResponse2(0, 'financial_approval',
                trans('update.your_financial_information_has_not_been_approved_by_the_admin'),
                null,
                trans('public.request_failed')
            );
        }

        if (!empty($user->selectedBank)) {
            Payout::create([
                'user_id' => $user->id,
                'user_selected_bank_id' => $user->selectedBank->id,
                'amount' => $getUserPayout,
                'status' => Payout::$waiting,
                'created_at' => time(),
            ]);

            $notifyOptions = [
                '[payout.amount]' => handlePrice($getUserPayout),
                '[amount]' => handlePrice($getUserPayout),
                '[u.name]' => $user->full_name
            ];

            sendNotification('payout_request', $notifyOptions, $user->id);
            sendNotification('payout_request_admin', $notifyOptions, 1); // for admin
            sendNotification('new_user_payout_request', $notifyOptions, 1); // for admin

            return apiResponse2(1, 'stored', trans('api.public.stored'));

        }


        return apiResponse2(0, 'identity_settings',
            trans('site.check_identity_settings'),
            null,
            trans('public.request_failed')

        );
    }
}
