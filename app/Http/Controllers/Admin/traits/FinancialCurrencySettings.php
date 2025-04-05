<?php

namespace App\Http\Controllers\Admin\traits;

use App\Models\Currency;
use App\Models\NavbarButton;
use App\Models\Role;
use App\Models\Translation\NavbarButtonTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait FinancialCurrencySettings
{
    public function financialCurrencyStore(Request $request)
    {
        $data = $request->all();
        $rules = [
            "currency" => "required|unique:currencies,currency" . (!empty($data['item_id']) ? (',' . $data['item_id']) : ''),
            "currency_position" => "required",
            "currency_separator" => "required",
            "currency_decimal" => "numeric",
            "exchange_rate" => "numeric",
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $item = null;
        if (!empty($data['item_id'])) {
            $item = Currency::query()->findOrFail($data['item_id']);
        }

        $order = !empty($item) ? $item->order : (Currency::query()->count() + 1);

        $insertData = [
            "currency" => $data['currency'],
            "currency_position" => $data['currency_position'],
            "currency_separator" => $data['currency_separator'],
            "currency_decimal" => $data['currency_decimal'] ?? 0,
            "exchange_rate" => $data['exchange_rate'] ?? 0,
            "order" => $order,
            "created_at" => time(),
        ];

        if (!empty($item)) {
            $item->update($insertData);
        } else {
            Currency::query()->create($insertData);
        }

        return response()->json([
            'code' => 200
        ]);
    }

    public function financialCurrencyEdit($id)
    {
        $item = Currency::query()->findOrFail($id);

        $data = [
            'editCurrency' => $item
        ];

        $html = (string)view()->make('admin.settings.financial.currency_modal', $data);

        return response()->json([
            'html' => $html
        ], 200);
    }

    public function financialCurrencyDelete($id)
    {
        $item = Currency::query()->findOrFail($id);

        $item->delete();

        return redirect()->back();
    }

    public function financialCurrencyOrderItems(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'items' => 'required',
            'table' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $itemIds = explode(',', $data['items']);

        if (!is_array($itemIds) and !empty($itemIds)) {
            $itemIds = [$itemIds];
        }

        if (!empty($itemIds) and is_array($itemIds) and count($itemIds)) {
            foreach ($itemIds as $order => $id) {
                Currency::query()->where('id', $id)
                    ->update(['order' => ($order + 1)]);
            }
        }

        return response()->json([
            'title' => trans('public.request_success'),
            'msg' => trans('update.items_sorted_successful')
        ]);
    }
}
