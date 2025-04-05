<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFaq;
use App\Models\Translation\ProductFaqTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductFaqController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_store_edit_product');

        $data = $request->get('ajax')['new'];

        $rules = [
            'product_id' => 'required',
            'title' => 'required|max:255',
            'answer' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::where('id', $data['product_id'])
            ->first();

        if (!empty($product)) {
            $faq = ProductFaq::create([
                'creator_id' => $product->creator_id,
                'product_id' => $product->id,
                'order' => null,
                'created_at' => time(),
            ]);

            if (!empty($faq)) {
                ProductFaqTranslation::updateOrCreate([
                    'product_faq_id' => $faq->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'answer' => $data['answer'],
                ]);
            }

            return response()->json([
                'code' => 200,
            ], 200);
        }

        abort(403);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_store_edit_product');

        $data = $request->get('ajax')[$id];

        $rules = [
            'product_id' => 'required',
            'title' => 'required|max:255',
            'answer' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::where('id', $data['product_id'])
            ->first();

        if (!empty($product)) {
            $faq = ProductFaq::where('id', $id)
                ->where('product_id', $product->id)
                ->first();

            if (!empty($faq)) {
                ProductFaqTranslation::updateOrCreate([
                    'product_faq_id' => $faq->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'answer' => $data['answer'],
                ]);

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        abort(403);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_store_edit_product');

        $faq = ProductFaq::where('id', $id)
            ->first();

        if (!empty($faq)) {
            $faq->delete();
        }

        return back();
    }
}
