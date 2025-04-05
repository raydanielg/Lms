<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBadge;
use App\Models\ProductBadgeContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductBadgeContentsController extends Controller
{

    public function getForm($badgeId)
    {
        $this->authorize("admin_product_badges_create");
        $badge = ProductBadge::query()->findOrFail($badgeId);

        $data = [
            'badge' => $badge,
        ];

        $html = (string)view()->make("admin.product_badges.create.content_modal", $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ]);
    }

    public function store(Request $request, $badgeId)
    {
        $this->authorize("admin_product_badges_create");
        $badge = ProductBadge::query()->findOrFail($badgeId);

        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => 'required',
            'course' => 'required_if:type,course',
            'product' => 'required_if:type,product',
            'bundle' => 'required_if:type,bundle',
            'blog_article' => 'required_if:type,blog_article',
            'upcoming_course' => 'required_if:type,upcoming_course',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }


        $targetId = $data[$data['type']];
        $targetType = $this->getTargetType($data);

        $check = ProductBadgeContent::query()
            ->where('product_badge_id', $badge->id)
            ->where('targetable_type', $targetType)
            ->where('targetable_id', $targetId)
            ->first();

        if (!empty($check)) {
            return response([
                'code' => 422,
                'errors' => [
                    "{$data['type']}" => [trans('validation.unique', ['attribute' => trans("update.{$data['type']}")])]
                ],
            ], 422);
        }

        ProductBadgeContent::query()->create([
            'product_badge_id' => $badge->id,
            'targetable_id' => $targetId,
            'targetable_type' => $targetType,
        ]);

        return response()->json([
            'code' => 200,
            'title' => trans('public.request_success'),
            'msg' => trans('update.badge_content_created_successfully'),
        ]);
    }


    public function delete($badgeId, $contentId)
    {
        $this->authorize("admin_product_badges_create");
        $badge = ProductBadge::query()->findOrFail($badgeId);

        $content = ProductBadgeContent::query()
            ->where('id', $contentId)
            ->where('product_badge_id', $badge->id)
            ->first();

        if (!empty($content)) {
            $content->delete();

            return redirect()->back();
        }

        abort(404);

    }

    private function getTargetType($data)
    {
        $type = "";

        switch ($data["type"]) {
            case "course":
                $type = "App\Models\Webinar";
                break;

            case "bundle":
                $type = "App\Models\Bundle";
                break;

            case "product":
                $type = "App\Models\Product";
                break;

            case "blog_article":
                $type = "App\Models\Blog";
                break;

            case "upcoming_course":
                $type = "App\Models\UpcomingCourse";
                break;
        }

        return $type;
    }
}
