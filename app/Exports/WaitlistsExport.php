<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WaitlistsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $waitlists;

    public function __construct($waitlists)
    {
        $this->waitlists = $waitlists;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->waitlists;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.course'),
            trans('update.members'),
            trans('update.registered_members'),
            trans('update.last_submission'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($waitlist): array
    {

        return [
            $waitlist->title,
            $waitlist->members,
            $waitlist->registered_members,
            !empty($waitlist->last_submission) ? dateTimeFormat($waitlist->last_submission, 'j M Y H:i') : '-'
        ];
    }
}
