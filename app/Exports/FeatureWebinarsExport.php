<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FeatureWebinarsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $features;

    public function __construct($features)
    {
        $this->features = $features;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->features;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.id'),
            trans('admin/pages/webinars.webinar_title'),
            trans('admin/pages/webinars.webinar_status'),
            trans('public.date'),
            trans('admin/pages/webinars.teacher_name'),
            trans('admin/main.category'),
            trans('admin/pages/webinars.page'),
            trans('admin/main.status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($feature): array
    {
        return [
            $feature->id,
            $feature->webinar->title,
            $feature->webinar->status,
            dateTimeFormat($feature->updated_at, 'Y M j | H:i'),
            $feature->webinar->teacher->full_name,
            $feature->webinar->category->title,
            trans('admin/pages/webinars.page_' . $feature->page),
            ($feature->status == 'publish') ? trans('admin/main.published') : trans('admin/main.pending'),
        ];
    }
}
