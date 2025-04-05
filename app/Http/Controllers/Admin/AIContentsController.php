<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mixins\OpenAI\AiContentGenerator;
use App\Models\AiContent;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AIContentsController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('admin_ai_contents_lists');

        $query = AiContent::query();

        $totalGenerated = deepClone($query)->count();
        $textGenerated = deepClone($query)->where('service_type', 'text')->count();
        $imageGenerated = deepClone($query)->where('service_type', 'image')->count();
        $usersCount = deepClone($query)->groupBy('user_id')->count('user_id');;

        $contents = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $data = [
            'pageTitle' => trans('update.generated_contents'),
            'contents' => $contents,
            'totalGenerated' => $totalGenerated,
            'textGenerated' => $textGenerated,
            'imageGenerated' => $imageGenerated,
            'usersCount' => $usersCount,
        ];

        return view('admin.ai_contents.lists.index', $data);
    }

    public function delete($id)
    {
        $this->authorize('admin_ai_contents_lists');
        $content = AiContent::query()->findOrFail($id);

        $content->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.items_deleted_successful'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function generate(Request $request)
    {
        $this->authorize('admin_ai_contents');

        $user = auth()->user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'service_type' => 'required|in:text,image',
            'text_service_id' => 'required_if:service_type,text',
            'image_service_id' => 'required_if:service_type,image',
            'question' => 'required_if:text_service_id,custom_text',
            'image_size' => 'required_if:image_service_id,custom_image',
            'image_question' => 'required_if:image_service_id,custom_image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $contentGenerator = new AiContentGenerator();
        $content = $contentGenerator->makeContent($user, $data);

        return response()->json([
            'code' => 200,
            'data' => $content
        ]);
    }


    public function settings(Request $request)
    {
        $this->authorize('admin_ai_contents_settings');

        removeContentLocale();

        $setting = Setting::where('page', 'general')
            ->where('name', Setting::$aiContentsSettingsName)
            ->first();

        $data = [
            'pageTitle' => trans('update.settings'),
            'setting' => $setting,
            'selectedLocale' => mb_strtolower($request->get('locale', Setting::$defaultSettingsLocale)),
        ];

        return view('admin.ai_contents.settings.index', $data);
    }

}
