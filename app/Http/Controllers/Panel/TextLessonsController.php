<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\TextLesson;
use App\Models\TextLessonAttachment;
use App\Models\Translation\TextLessonTranslation;
use App\Models\Webinar;
use App\Models\WebinarChapterItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class TextLessonsController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required',
            'study_time' => 'required|numeric',
            'image' => 'nullable',
            'accessibility' => 'required|' . Rule::in(File::$accessibility),
            'summary' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
            $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
            $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
        } else {
            $data['check_previous_parts'] = false;
            $data['access_after_day'] = null;
        }

        if (!empty($webinar) and $webinar->canAccess($user)) {
            $lessonsCount = TextLesson::where('webinar_id', $data['webinar_id'])->count();

            $textLesson = TextLesson::create([
                'creator_id' => $user->id,
                'webinar_id' => $data['webinar_id'],
                'chapter_id' => $data['chapter_id'],
                'image' => $data['image'] ?? null,
                'study_time' => $data['study_time'],
                'accessibility' => $data['accessibility'],
                'order' => $lessonsCount + 1,
                'check_previous_parts' => $data['check_previous_parts'],
                'access_after_day' => $data['access_after_day'],
                'status' => (!empty($data['status']) and $data['status'] == 'on') ? TextLesson::$Active : TextLesson::$Inactive,
                'created_at' => time(),
            ]);

            if ($textLesson) {
                TextLessonTranslation::updateOrCreate([
                    'text_lesson_id' => $textLesson->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'summary' => $data['summary'],
                    'content' => $data['content'],
                ]);

                if (!empty($data['attachments'])) {
                    $attachments = $data['attachments'];
                    $this->saveAttachments($textLesson, $attachments);
                }

                WebinarChapterItem::makeItem($textLesson->creator_id, $textLesson->chapter_id, $textLesson->id, WebinarChapterItem::$chapterTextLesson);
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

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required',
            'study_time' => 'required|numeric',
            'image' => 'nullable',
            'accessibility' => 'required|' . Rule::in(File::$accessibility),
            'summary' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
            $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
            $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
        } else {
            $data['check_previous_parts'] = false;
            $data['access_after_day'] = null;
        }

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($webinar) and $webinar->canAccess($user)) {

            $textLesson = TextLesson::where('id', $id)
                ->where(function ($query) use ($user, $webinar) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere('webinar_id', $webinar->id);
                })
                ->first();

            if (!empty($textLesson)) {
                $changeChapter = ($data['chapter_id'] != $textLesson->chapter_id);
                $oldChapterId = $textLesson->chapter_id;

                $textLesson->update([
                    'chapter_id' => $data['chapter_id'],
                    'image' => $data['image'] ?? null,
                    'study_time' => $data['study_time'],
                    'accessibility' => $data['accessibility'],
                    'check_previous_parts' => $data['check_previous_parts'],
                    'access_after_day' => $data['access_after_day'],
                    'status' => (!empty($data['status']) and $data['status'] == 'on') ? TextLesson::$Active : TextLesson::$Inactive,
                    'updated_at' => time(),
                ]);

                if ($changeChapter) {
                    WebinarChapterItem::changeChapter($textLesson->creator_id, $oldChapterId, $textLesson->chapter_id, $textLesson->id, WebinarChapterItem::$chapterTextLesson);
                }

                TextLessonTranslation::updateOrCreate([
                    'text_lesson_id' => $textLesson->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'summary' => $data['summary'],
                    'content' => $data['content'],
                ]);

                $textLesson->attachments()->delete();

                if (!empty($data['attachments'])) {
                    $attachments = $data['attachments'];
                    $this->saveAttachments($textLesson, $attachments);
                }

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        abort(403);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $textLesson = TextLesson::where('id', $id)
            ->first();

        if (!empty($textLesson)) {
            $webinar = Webinar::query()->find($textLesson->webinar_id);

            if ($textLesson->creator_id == $user->id or (!empty($webinar) and $webinar->canAccess($user))) {

                WebinarChapterItem::where('user_id', $textLesson->creator_id)
                    ->where('item_id', $textLesson->id)
                    ->where('type', WebinarChapterItem::$chapterTextLesson)
                    ->delete();

                $textLesson->delete();
            }
        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    private function saveAttachments($textLesson, $attachments)
    {
        if (!empty($attachments)) {

            if (!is_array($attachments)) {
                $attachments = [$attachments];
            }

            foreach ($attachments as $attachment_id) {
                if (!empty($attachment_id)) {
                    TextLessonAttachment::create([
                        'text_lesson_id' => $textLesson->id,
                        'file_id' => $attachment_id,
                        'created_at' => time(),
                    ]);
                }
            }
        }
    }
}


