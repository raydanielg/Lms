<?php

namespace App\Http\Controllers\Admin\traits;

trait WebinarChangeCreator
{
    private function webinarChangedCreator($webinar)
    {
        // Chapters
        $webinar->chapters()->update([
            'user_id' => $webinar->creator_id
        ]);

        // Sessions
        $webinar->sessions()->update([
            'creator_id' => $webinar->creator_id
        ]);

        // FAQs
        $webinar->faqs()->update([
            'creator_id' => $webinar->creator_id
        ]);

        // Files
        $webinar->files()->update([
            'creator_id' => $webinar->creator_id
        ]);

        // Text Lessons
        $webinar->textLessons()->update([
            'creator_id' => $webinar->creator_id
        ]);

        // Quizzes
        $webinar->quizzes()->update([
            'creator_id' => $webinar->creator_id
        ]);

        // Assignments
        $webinar->assignments()->update([
            'creator_id' => $webinar->creator_id
        ]);

        // Webinar Extra Description
        $webinar->webinarExtraDescription()->update([
            'creator_id' => $webinar->creator_id
        ]);

        // Tickets
        $webinar->tickets()->update([
            'creator_id' => $webinar->creator_id
        ]);

    }
}
