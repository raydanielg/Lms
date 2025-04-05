<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CertificatesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $certificates;

    public function __construct($certificates)
    {
        $this->certificates = $certificates;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->certificates;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.id'),
            trans('admin/pages/quiz.title'),
            trans('admin/pages/webinars.webinar'),
            trans('quiz.student'),
            trans('admin/pages/quiz.instructor'),
            trans('admin/pages/quiz.grades'),
            trans('public.date_time'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($certificate): array
    {
        return [
            $certificate->id,
            $certificate->quiz->title,
            $certificate->quiz->webinar->title,
            $certificate->student ? $certificate->student->full_name : trans('update.deleted_user'),
            $certificate->quiz->teacher->full_name,
            $certificate->quizzesResult ? $certificate->quizzesResult->user_grade : '-',
            dateTimeFormat($certificate->created_at, 'j F Y'),
        ];
    }
}
