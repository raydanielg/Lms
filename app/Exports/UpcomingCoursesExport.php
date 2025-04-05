<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UpcomingCoursesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $upcomingCourses;

    public function __construct($upcomingCourses)
    {
        $this->upcomingCourses = $upcomingCourses;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->upcomingCourses;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.id'),
            trans('admin/main.title'),
            trans('admin/main.instructor'),
            trans('admin/main.type'),
            trans('admin/main.price'),
            trans('update.followers'),
            trans('admin/main.start_date'),
            trans('admin/main.created_at'),
            trans('admin/main.status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($upcomingCourse): array
    {
        return [
            $upcomingCourse->id,
            $upcomingCourse->title,
            $upcomingCourse->teacher->full_name,
            $upcomingCourse->type,
            $upcomingCourse->price,
            $upcomingCourse->followers_count,
            dateTimeFormat($upcomingCourse->publish_date, 'Y M j | H:i'),
            dateTimeFormat($upcomingCourse->created_at, 'j M Y | H:i'),
            $upcomingCourse->status,
        ];
    }
}
