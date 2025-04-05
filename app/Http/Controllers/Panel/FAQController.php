<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Faq;
use App\Models\Translation\FaqTranslation;
use App\Models\UpcomingCourse;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Validator;

class FAQController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $canStore = $this->checkItem($user, $data);

        if ($canStore) {
            $columnName = !empty($data['webinar_id']) ? 'webinar_id' : (!empty($data['bundle_id']) ? 'bundle_id' : 'upcoming_course_id');
            $columnValue = !empty($data['webinar_id']) ? $data['webinar_id'] : (!empty($data['bundle_id']) ? $data['bundle_id'] : $data['upcoming_course_id']);

            $order = Faq::query()
                    ->where(function ($query) use ($user, $columnName, $columnValue) {
                        $query->where('creator_id', $user->id);
                        $query->orWhere($columnName, $columnValue);
                    })
                    ->count() + 1;

            $faq = Faq::create([
                'creator_id' => $user->id,
                'webinar_id' => !empty($data['webinar_id']) ? $data['webinar_id'] : null,
                'bundle_id' => !empty($data['bundle_id']) ? $data['bundle_id'] : null,
                'upcoming_course_id' => !empty($data['upcoming_course_id']) ? $data['upcoming_course_id'] : null,
                'order' => $order,
                'created_at' => time()
            ]);

            if (!empty($faq)) {
                FaqTranslation::updateOrCreate([
                    'faq_id' => $faq->id,
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

    private function checkItem($user, $data)
    {
        $canStore = false;

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::find($data['webinar_id']);

            if (!empty($webinar) and $webinar->canAccess($user)) {
                $canStore = true;
            }
        } elseif (!empty($data['bundle_id'])) {
            $bundle = Bundle::find($data['bundle_id']);

            if (!empty($bundle) and $bundle->canAccess($user)) {
                $canStore = true;
            }
        } elseif (!empty($data['upcoming_course_id'])) {
            $upcomingCourse = UpcomingCourse::find($data['upcoming_course_id']);

            if (!empty($upcomingCourse) and $upcomingCourse->canAccess($user)) {
                $canStore = true;
            }
        }

        return $canStore;
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $canStore = $this->checkItem($user, $data);

        if ($canStore) {
            $columnName = !empty($data['webinar_id']) ? 'webinar_id' : (!empty($data['bundle_id']) ? 'bundle_id' : 'upcoming_course_id');
            $columnValue = !empty($data['webinar_id']) ? $data['webinar_id'] : (!empty($data['bundle_id']) ? $data['bundle_id'] : $data['upcoming_course_id']);

            $faq = Faq::where('id', $id)
                ->where(function ($query) use ($user, $columnName, $columnValue) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere($columnName, $columnValue);
                })
                ->first();

            if (!empty($faq)) {
                $faq->update([
                    'updated_at' => time()
                ]);

                FaqTranslation::updateOrCreate([
                    'faq_id' => $faq->id,
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
        $user = auth()->user();
        $faq = Faq::where('id', $id)
            ->first();

        if (!empty($faq)) {
            $item = null;
            if (!empty($faq->webinar_id)) {
                $item = Webinar::query()->find($faq->webinar_id);
            } else if (!empty($faq->bundle_id)) {
                $item = Bundle::query()->find($faq->bundle_id);
            } else if (!empty($faq->upcoming_course_id)) {
                $item = UpcomingCourse::find($faq->upcoming_course_id);
            }

            if ($faq->creator_id == $user->id or (!empty($item) and $item->canAccess($user))) {
                $faq->delete();
            }
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
