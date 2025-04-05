<?php

namespace App\Http\Controllers\Panel\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\Translation\ProductFileTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductFileController extends Controller
{

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

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
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($product)) {
            $file = ProductFile::create([
                'creator_id' => $user->id,
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

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

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
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($product)) {
            $file = ProductFile::where('id', $id)
                ->where('creator_id', $user->id)
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

    public function destroy(Request $request, $id)
    {
        $file = ProductFile::where('id', $id)
            ->where('creator_id', auth()->id())
            ->first();

        if (!empty($file)) {
            $file->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
    }

    public function orderItems(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'items' => 'required',
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
                ProductFile::where('id', $id)
                    ->where('creator_id', $user->id)
                    ->update(['order' => ($order + 1)]);
            }
        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function download($id)
    {
        $file = ProductFile::where('id', $id)->first();
        if (!empty($file)) {
            $product = Product::where('id', $file->product_id)
                ->where('status', Product::$active)
                ->first();

            if (!empty($product) and $product->checkUserHasBought()) {
                $fileType = explode('.', $file->path);
                $fileType = end($fileType);

                $filePath = public_path($file->path);

                if (file_exists($filePath)) {
                    $fileName = str_replace([' ', '.'], '-', $file->title);
                    $fileName .= '.' . $fileType;

                    $headers = [
                        'Content-Type: application/' . $fileType,
                    ];

                    return response()->download($filePath, $fileName, $headers);
                }
            }
        }

        $toastData = [
            'title' => trans('public.not_access_toast_lang'),
            'msg' => trans('public.not_access_toast_msg_lang'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
