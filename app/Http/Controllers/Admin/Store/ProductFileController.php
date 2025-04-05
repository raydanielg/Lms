<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\Translation\ProductFileTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductFileController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_store_edit_product');

        $data = $request->all();

        $rules = [
            'product_id' => 'required',
            'title' => 'required|max:255',
            'path' => 'required|max:255',
            'description' => 'required',
            'file_type' => 'required',
            'volume' => 'required',
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

            $file = ProductFile::create([
                'creator_id' => $product->creator_id,
                'product_id' => $data['product_id'],
                'path' => $data['path'],
                'order' => null,
                'volume' => $data['volume'],
                'file_type' => $data['file_type'],
                'online_viewer' => (!empty($data['online_viewer']) and $data['online_viewer'] == 'on'),
                'status' => (!empty($data['status']) and $data['status'] == 'on') ? ProductFile::$Active : ProductFile::$Inactive,
                'created_at' => time(),
            ]);

            if (!empty($file)) {
                ProductFileTranslation::updateOrCreate([
                    'product_file_id' => $file->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);
            }

            return response()->json([
                'code' => 200,
            ], 200);
        }

        abort(403);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_store_edit_product');

        $file = ProductFile::where('id', $id)->first();

        if (!empty($file)) {
            $locale = $request->get('locale', getDefaultLocale());
            if (empty($locale)) {
                $locale = getDefaultLocale();
            }
            storeContentLocale($locale, $file->getTable(), $file->id);

            $file->title = $file->getTitleAttribute();
            $file->description = $file->getDescriptionAttribute();
            $file->locale = mb_strtoupper($locale);

            return response()->json([
                'file' => $file
            ], 200);
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_store_edit_product');

        $data = $request->all();

        $rules = [
            'product_id' => 'required',
            'title' => 'required|max:255',
            'path' => 'required|max:255',
            'description' => 'required',
            'file_type' => 'required',
            'volume' => 'required',
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
            $file = ProductFile::where('id', $id)
                ->where('product_id', $product->id)
                ->first();

            if (!empty($file)) {
                $file->update([
                    'path' => $data['path'],
                    'volume' => $data['volume'],
                    'file_type' => $data['file_type'],
                    'online_viewer' => (!empty($data['online_viewer']) and $data['online_viewer'] == 'on'),
                    'status' => (!empty($data['status']) and $data['status'] == 'on') ? ProductFile::$Active : ProductFile::$Inactive,
                    'created_at' => time(),
                ]);

                ProductFileTranslation::updateOrCreate([
                    'product_file_id' => $file->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        abort(403);
    }

    public function destroy($id)
    {
        $this->authorize('admin_store_edit_product');

        $file = ProductFile::where('id', $id)
            ->first();

        if (!empty($file)) {
            $file->delete();
        }

        return redirect()->back();
    }
}
