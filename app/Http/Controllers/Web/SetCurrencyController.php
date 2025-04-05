<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mixins\Financial\MultiCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SetCurrencyController extends Controller
{
    public function setCurrency(Request $request)
    {
        $this->validate($request, [
            'currency' => 'required'
        ]);

        $currency = $request->get('currency');

        $multiCurrency = new MultiCurrency();
        $currencies = $multiCurrency->getCurrencies();
        $signs = $currencies->pluck('currency')->toArray();

        if (in_array($currency, $signs)) {
            if (auth()->check()) {
                $user = auth()->user();
                $user->update([
                    'currency' => $currency
                ]);
            } else {
                Cookie::queue('user_currency', $currency, 30 * 24 * 60);
            }
        }

        $previousUrl = $request->get('previous_url');

        if (!empty($previousUrl)) {
            return redirect($previousUrl);
        }

        return redirect()->back();
    }
}
