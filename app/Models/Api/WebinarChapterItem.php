<?php

namespace App\Models\Api;

use App\Http\Resources\FileResource;
use App\Http\Resources\SessionResource;
use App\Http\Resources\TextLessonResource;
use App\Http\Resources\WebinarAssignmentResource;
use App\Models\File;
use App\Models\WebinarChapterItem as Model;

class WebinarChapterItem extends Model
{
    public function getItemResource()
    {
        if (empty($this->item)) {
            return  [];
        }

        $type = $this->type;
        if ($type == self::$chapterFile) {
            return [
                'id' => $this->item->id,
                'title' => $this->item->title,
                'file_type' => $this->item->file_type,
                'storage' => $this->item->storage,
                'volume' => (empty($this->item->volume) or $this->item->volume === "0 bytes" or in_array($this->item->storage, File::$ignoreVolumeFileSources)) ? null : $this->item->volume,
                'downloadable' => $this->item->downloadable,
                'access_after_day' => $this->item->access_after_day,
                'check_previous_parts' => $this->item->check_previous_parts,
                //  'auth_has_read'=>$this->item->auth_has_read

            ];
            //    return new FileResource($this->file);
        } elseif ($type == self::$chapterSession) {
            return [
                'id' => $this->item->id,
                'title' => $this->item->title,
                'date' => $this->item->date,
                'auth_has_read' => $this->item->auth_has_read,
                'access_after_day' => $this->item->access_after_day,
                'check_previous_parts' => $this->item->check_previous_parts,
            ];
            //  return new SessionResource($this->session);
        } elseif ($type == self::$chapterTextLesson) {
            return [
                'id' => $this->item->id,
                'title' => $this->item->title,
                'summary' => $this->item->summary,
                'access_after_day' => $this->item->access_after_day,
                'check_previous_parts' => $this->item->check_previous_parts,
                'status' => $this->item->status,
            ];
            return new TextLessonResource($this->textLesson);
        } elseif ($type == self::$chapterQuiz) {
            return [
                'id' => $this->item->id,
                'title' => $this->item->title,
                'time' => $this->item->time,
                'question_count' => $this->item->quizQuestions->count(),
                'auth_status' => $this->auth_status,
                'passed' => $this->item->AuthPassedQuiz,
                //   'created_at' => $this->item->created_at,
            ];
            // return $this->quiz();
        } elseif ($type == self::$chapterAssignment) {
            return [
                'id' => $this->item->id,
                'title' => $this->item->title,
                'status' => $this->item->status,
                'assignmentStatus' => $this->item->assignmentStatus,
                'access_after_day' => $this->item->access_after_day,
                'check_previous_parts' => $this->item->check_previous_parts,
            ];
            return new WebinarAssignmentResource($this->assignment);
        }
        return [];
    }

    public function item()
    {
        $type = $this->type;
        if ($type == self::$chapterFile) {
            return $this->file();
        } elseif ($type == self::$chapterSession) {
            return $this->session();
        } elseif ($type == self::$chapterTextLesson) {
            return $this->textLesson();
        } elseif ($type == self::$chapterQuiz) {
            return $this->quiz();
        } elseif ($type == self::$chapterAssignment) {
            return $this->assignment();
        }
        return null;
    }


    public function session()
    {
        return $this->belongsTo('App\Models\Api\Session', 'item_id', 'id');
    }

    public function file()
    {
        return $this->belongsTo('App\Models\Api\File', 'item_id', 'id');
    }

    public function textLesson()
    {
        return $this->belongsTo('App\Models\Api\TextLesson', 'item_id', 'id');
    }

    public function assignment()
    {
        return $this->belongsTo('App\Models\Api\WebinarAssignment', 'item_id', 'id');
    }

    public function quiz()
    {
        return $this->belongsTo('App\Models\Api\Quiz', 'item_id', 'id');
    }

}
