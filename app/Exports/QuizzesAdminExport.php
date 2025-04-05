<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class QuizzesAdminExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sheets;

    public function __construct($sheets)
    {
        $this->sheets = $sheets;
    }

    public function collection()
    {
        return $this->sheets;
    }

    public function headings(): array
    {
        return [
            '#',
            trans('admin/pages/quiz.title'),
            trans('admin/pages/quiz.instructor'),
            trans('admin/pages/quiz.question_count'),
            trans('admin/pages/quiz.students_count'),
            trans('admin/pages/quiz.average_grade'),
            trans('admin/pages/quiz.certificate'),
            trans('admin/main.status'),
        ];
    }

    public function map($quiz): array
    {
        $quiz_name = $quiz->title;
        if (!empty($quiz->webinar)) {
            $quiz_name .= ' (' . $quiz->webinar ? $quiz->webinar->title : '-' . ') ';
        }

        $certificate = ($quiz->certificate) ? trans('admin/main.yes') : trans('admin/main.no');
        $status = ($quiz->status == 'active') ? trans('admin/main.active') : trans('admin/main.inactive');

        return [
            $quiz->id,
            $quiz_name,
            $quiz->teacher->full_name,
            $quiz->quizQuestions->count(),
            $quiz->quizResults->pluck('user_id')->count(),
            $quiz->quizResults->avg('user_grade'),
            $certificate,
            $status
        ];
    }

}
