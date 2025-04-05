<?php

namespace App\Http\Controllers\Admin\traits;

use App\Models\UserBank;
use App\Models\UserBankSpecification;
use App\Models\Translation\UserBankSpecificationTranslation;
use App\Models\Translation\UserBankTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait FinancialUserBankSettings
{

    public function financialUserBankForm()
    {
        $data = [
            'locale' => mb_strtolower(app()->getLocale())
        ];

        $html = (string)view()->make("admin.settings.financial.user_banks.modal", $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ]);
    }

    public function financialUserBankStore(Request $request)
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

        $bank = UserBank::query()->create([
            "logo" => $data['logo'],
            "created_at" => time(),
        ]);

        UserBankTranslation::query()->updateOrCreate([
            'user_bank_id' => $bank->id,
            'locale' => mb_strtolower($data['locale'])
        ], [
            'title' => $data['title']
        ]);

        $this->handleUserBankSpecifications($bank, $data);

        return response()->json([
            'code' => 200
        ]);
    }

    private function handleUserBankSpecifications($bank, $data)
    {
        $locale = mb_strtolower($data['locale']);

        $ignoreIds = [];

        if (!empty($data['specifications'])) {
            foreach ($data['specifications'] as $specificationId => $specification) {
                if (!empty($specification['name'])) {

                    $item = UserBankSpecification::query()->where('id', $specificationId)
                        ->where('user_bank_id', $bank->id)
                        ->first();

                    if (empty($item)) {
                        $item = UserBankSpecification::query()->create([
                            'user_bank_id' => $bank->id,
                        ]);
                    }

                    $ignoreIds[] = $item->id;

                    UserBankSpecificationTranslation::query()->updateOrCreate([
                        'user_bank_specification_id' => $item->id,
                        'locale' => $locale
                    ], [
                        'name' => $specification['name']
                    ]);
                }
            }
        }

        UserBankSpecification::query()->whereNotIn('id', $ignoreIds)
            ->where('user_bank_id', $bank->id)
            ->delete();
    }

    public function financialUserBankEdit(Request $request, $id)
    {
        $bank = UserBank::query()->findOrFail($id);

        $data = [
            'editBank' => $bank,
            'locale' => mb_strtolower($request->get('locale', app()->getLocale()))
        ];

        $html = (string)view()->make('admin.settings.financial.user_banks.modal', $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ], 200);
    }

    public function financialUserBankUpdate(Request $request, $id)
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

        $bank = UserBank::query()->findOrFail($id);

        $bank->update([
            "logo" => $data['logo'],
        ]);

        UserBankTranslation::query()->updateOrCreate([
            'user_bank_id' => $bank->id,
            'locale' => mb_strtolower($data['locale'])
        ], [
            'title' => $data['title']
        ]);

        $this->handleUserBankSpecifications($bank, $data);

        return response()->json([
            'code' => 200
        ]);
    }

    public function financialUserBankDelete($id)
    {
        $bank = UserBank::query()->findOrFail($id);

        $bank->delete();

        return redirect()->back();
    }
}
