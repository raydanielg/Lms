<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Faq;
use App\Models\Translation\FaqTranslation;
use App\Models\UpcomingCourse;
use App\Models\Webinar;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_webinars_edit');

        $this->validate($request, [
            'title' => 'required|max:255',
            'answer' => 'required',
        ]);

        $data = $request->all();

        $creator = $this->getCreator($data);

        if (!empty($creator)) {
            $columnName = !empty($data['webinar_id']) ? 'webinar_id' : (!empty($data['bundle_id']) ? 'bundle_id' : 'upcoming_course_id');
            $columnValue = !empty($data['webinar_id']) ? $data['webinar_id'] : (!empty($data['bundle_id']) ? $data['bundle_id'] : $data['upcoming_course_id']);

            $order = Faq::query()->where('creator_id', $creator->id)
                    ->where($columnName, $columnValue)
                    ->count() + 1;

            $faq = Faq::create([
                'creator_id' => $creator->id,
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

        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    private function getCreator($data)
    {
        $creator = null;

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::findOrFail($data['webinar_id']);

            $creator = $webinar->creator;
        } elseif (!empty($data['bundle_id'])) {
            $bundle = Bundle::findOrFail($data['bundle_id']);

            $creator = $bundle->creator;
        } elseif (!empty($data['upcoming_course_id'])) {
            $upcomingCourse = UpcomingCourse::findOrFail($data['upcoming_course_id']);

            $creator = $upcomingCourse->creator;
        }

        return $creator;
    }

    public function description(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        removeContentLocale();

        $faq = Faq::where('id', $id)
            ->first();

        if (!empty($faq)) {
            return response()->json([
                'faq' => $faq
            ], 200);
        }

        return response()->json([], 422);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $faq = Faq::find($id);

        if (!empty($faq)) {
            $locale = $request->get('locale', app()->getLocale());
            if (empty($locale)) {
                $locale = app()->getLocale();
            }
            storeContentLocale($locale, $faq->getTable(), $faq->id);

            $faq->title = $faq->getTitleAttribute();
            $faq->answer = $faq->getAnswerAttribute();
            $faq->locale = mb_strtoupper($locale);

            return response()->json([
                'faq' => $faq
            ], 200);
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $this->validate($request, [
            'title' => 'required|max:64',
            'answer' => 'required',
        ]);

        $data = $request->all();

        $faq = Faq::find($id);

        if ($faq) {
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
        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        Faq::find($id)->delete();

        return redirect()->back();
    }
}
