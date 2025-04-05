<?php

namespace App\Http\Controllers\Admin\traits;

use App\Models\OfflineBank;
use App\Models\OfflineBankSpecification;
use App\Models\Translation\OfflineBankSpecificationTranslation;
use App\Models\Translation\OfflineBankTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait FinancialOfflineBankSettings
{

    public function financialOfflineBankForm()
    {
        $data = [
            'locale' => mb_strtolower(app()->getLocale())
        ];

        $html = (string)view()->make("admin.settings.financial.offline_banks.modal", $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ]);
    }

    public function financialOfflineBankStore(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            "title" => "required",
            "logo" => "required",
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bank = OfflineBank::query()->create([
            "logo" => $data['logo'],
            "created_at" => time(),
        ]);

        OfflineBankTranslation::query()->updateOrCreate([
            'offline_bank_id' => $bank->id,
            'locale' => mb_strtolower($data['locale'])
        ], [
            'title' => $data['title']
        ]);

        $this->handleBankSpecifications($bank, $data);

        return response()->json([
            'code' => 200
        ]);
    }

    private function handleBankSpecifications($bank, $data)
    {
        $locale = mb_strtolower($data['locale']);

        $ignoreIds = [];

        if (!empty($data['specifications'])) {
            foreach ($data['specifications'] as $specificationId => $specification) {
                if (!empty($specification['name']) and !empty($specification['value'])) {

                    $item = OfflineBankSpecification::query()->where('id', $specificationId)
                        ->where('offline_bank_id', $bank->id)
                        ->first();

                    if (empty($item)) {
                        $item = OfflineBankSpecification::query()->create([
                            'offline_bank_id' => $bank->id,
                            'value' => $specification['value']
                        ]);
                    } else {
                        $item->update([
                            'value' => $specification['value']
                        ]);
                    }

                    $ignoreIds[] = $item->id;

                    OfflineBankSpecificationTranslation::query()->updateOrCreate([
                        'offline_bank_specification_id' => $item->id,
                        'locale' => $locale
                    ], [
                        'name' => $specification['name']
                    ]);
                }
            }
        }

        OfflineBankSpecification::query()->whereNotIn('id', $ignoreIds)
            ->where('offline_bank_id', $bank->id)
            ->delete();
    }

    public function financialOfflineBankEdit(Request $request, $id)
    {
        $bank = OfflineBank::query()->findOrFail($id);


        $data = [
            'editBank' => $bank,
            'locale' => mb_strtolower($request->get('locale', app()->getLocale()))
        ];

        $html = (string)view()->make('admin.settings.financial.offline_banks.modal', $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ], 200);
    }

    public function financialOfflineBankUpdate(Request $request, $id)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            "title" => "required",
            "logo" => "required",
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bank = OfflineBank::query()->findOrFail($id);

        $bank->update([
            "logo" => $data['logo'],
        ]);

        OfflineBankTranslation::query()->updateOrCreate([
            'offline_bank_id' => $bank->id,
            'locale' => mb_strtolower($data['locale'])
        ], [
            'title' => $data['title']
        ]);

        $this->handleBankSpecifications($bank, $data);

        return response()->json([
            'code' => 200
        ]);
    }

    public function financialOfflineBankDelete($id)
    {
        $bank = OfflineBank::query()->findOrFail($id);

        $bank->delete();

        return redirect()->back();
    }
}
