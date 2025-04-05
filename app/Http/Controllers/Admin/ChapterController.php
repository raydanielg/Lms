<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Quiz;
use App\Models\Session;
use App\Models\TextLesson;
use App\Models\Translation\WebinarChapterTranslation;
use App\Models\Webinar;
use App\Models\WebinarAssignment;
use App\Models\WebinarChapter;
use App\Models\WebinarChapterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ChapterController extends Controller
{
    public function getChapter(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $chapter = WebinarChapter::where('id', $id)
            ->first();

        $locale = $request->get('locale', app()->getLocale());

        if (!empty($chapter)) {
            foreach ($chapter->translatedAttributes as $attribute) {
                try {
                    $chapter->$attribute = $chapter->translate(mb_strtolower($locale))->$attribute;
                } catch (\Exception $e) {
                    $chapter->$attribute = null;
                }
            }

            $data = [
                'chapter' => $chapter
            ];

            return response()->json($data, 200);
        }

        abort(403);
    }

    public function getAllByWebinarId($webinar_id)
    {
        $this->authorize('admin_webinars_edit');

        $webinar = Webinar::find($webinar_id);

        if (!empty($webinar)) {

            $chapters = $webinar->chapters->where('status', WebinarChapter::$chapterActive);

            $data = [
                'chapters' => [],
            ];

            if (!empty($chapters) and count($chapters)) {
                // for translate send on array of data

                foreach ($chapters as $chapter) {
                    $data['chapters'][] = [
                        'user_id' => $chapter->user_id,
                        'webinar_id' => $chapter->webinar_id,
                        'id' => $chapter->id,
                        'order' => $chapter->order,
                        'status' => $chapter->status,
                        'title' => $chapter->title,
                        'type' => $chapter->type,
                        'created_at' => $chapter->created_at,
                    ];
                }
            }

            return response()->json($data, 200);
        }

        abort(403);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->get('ajax')['chapter'];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            //'type' => 'required|' . Rule::in(WebinarChapter::$chapterTypes),
            'title' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])->first();

            if (!empty($webinar)) {
                $teacher = $webinar->creator;
                $status = (!empty($data['status']) and $data['status'] == 'on') ? WebinarChapter::$chapterActive : WebinarChapter::$chapterInactive;

                $chapter = WebinarChapter::create([
                    'user_id' => $teacher->id,
                    'webinar_id' => $webinar->id,
                    //'type' => $data['type'],
                    'title' => $data['title'],
                    'status' => $status,
                    'check_all_contents_pass' => (!empty($data['check_all_contents_pass']) and $data['check_all_contents_pass'] == 'on'),
                    'created_at' => time(),
                ]);

                if (!empty($chapter)) {
                    WebinarChapterTranslation::updateOrCreate([
                        'webinar_chapter_id' => $chapter->id,
                        'locale' => mb_strtolower($data['locale']),
                    ], [
                        'title' => $data['title'],
                    ]);
                }


                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        return response()->json([], 422);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $chapter = WebinarChapter::where('id', $id)->first();

        if (!empty($chapter)) {
            $locale = $request->get('locale', app()->getLocale());
            if (empty($locale)) {
                $locale = app()->getLocale();
            }
            storeContentLocale($locale, $chapter->getTable(), $chapter->id);

            $chapter->title = $chapter->getTitleAttribute();
            $chapter->locale = mb_strtoupper($locale);

            return response()->json([
                'chapter' => $chapter
            ], 200);
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->get('ajax')['chapter'];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            //'type' => 'required|' . Rule::in(WebinarChapter::$chapterTypes),
            'title' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $chapter = WebinarChapter::where('id', $id)->first();

        if (!empty($chapter)) {
            $webinar = Webinar::where('id', $data['webinar_id'])->first();

            if (!empty($webinar)) {
                $status = (!empty($data['status']) and $data['status'] == 'on') ? WebinarChapter::$chapterActive : WebinarChapter::$chapterInactive;

                $chapter->update([
                    'check_all_contents_pass' => (!empty($data['check_all_contents_pass']) and $data['check_all_contents_pass'] == 'on'),
                    'status' => $status,
                ]);

                if (!empty($chapter)) {
                    WebinarChapterTranslation::updateOrCreate([
                        'webinar_chapter_id' => $chapter->id,
                        'locale' => mb_strtolower($data['locale']),
                    ], [
                        'title' => $data['title'],
                    ]);
                }

                removeContentLocale();

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        removeContentLocale();

        return response()->json([], 422);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $chapter = WebinarChapter::where('id', $id)->first();

        if (!empty($chapter)) {
            $chapter->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
    }

    public function change(Request $request)
    {
        $data = $request->get('ajax');

        $validator = Validator::make($data, [
            'item_id' => 'required',
            'item_type' => 'required',
            'chapter_id' => 'required',
            'webinar_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $item = null;

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($webinar)) {

            switch ($data['item_type']) {
                case WebinarChapterItem::$chapterSession:
                    $item = Session::where('id', $data['item_id'])
                        ->where('webinar_id', $data['webinar_id'])
                        ->first();
                    break;

                case WebinarChapterItem::$chapterFile:
                    $item = File::where('id', $data['item_id'])
                        ->where('webinar_id', $data['webinar_id'])
                        ->first();
                    break;

                case WebinarChapterItem::$chapterTextLesson:
                    $item = TextLesson::where('id', $data['item_id'])
                        ->where('webinar_id', $data['webinar_id'])
                        ->first();
                    break;

                case WebinarChapterItem::$chapterQuiz:
                    $item = Quiz::where('id', $data['item_id'])
                        ->where('webinar_id', $data['webinar_id'])
                        ->first();
                    break;

                case WebinarChapterItem::$chapterAssignment:
                    $item = WebinarAssignment::where('id', $data['item_id'])
                        ->where('webinar_id', $data['webinar_id'])
                        ->first();
                    break;
            }
        }

        if (!empty($item)) {
            $item->update([
                'chapter_id' => !empty($data['chapter_id']) ? $data['chapter_id'] : null
            ]);

            WebinarChapterItem::where('item_id', $item->id)
                ->where('type', $data['item_type'])
                ->delete();

            if (!empty($data['chapter_id'])) {
                WebinarChapterItem::makeItem($item->creator_id, $data['chapter_id'], $item->id, $data['item_type']);
            }
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
