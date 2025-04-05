<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mixins\OpenAI\AiContentGenerator;
use App\Models\AiContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiContentController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize("panel_ai_contents_lists");

        $user = auth()->user();

        if ($user->checkAccessToAIContentFeature()) {

            $contents = AiContent::query()
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);


            $data = [
                'pageTitle' => trans('update.generated_contents'),
                'contents' => $contents,
            ];

            return view('web.default.panel.ai_contents.lists.index', $data);
        }

        abort(404);
    }

    public function generate(Request $request)
    {
        $user = auth()->user();

        if ($user->checkAccessToAIContentFeature()) {
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

        return response()->json([], 422);
    }
}
