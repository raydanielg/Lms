<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WaitlistItemsExport implements FromCollection, WithHeadings, WithMapping
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
            trans('admin/main.name'),
            trans('auth.email'),
            trans('public.phone'),
            trans('update.registration_status'),
            trans('update.submission_date'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($waitlist): array
    {

        return [
            !empty($waitlist->user) ? $waitlist->user->full_name : $waitlist->full_name,
            !empty($waitlist->user) ? $waitlist->user->email : $waitlist->email,
            !empty($waitlist->user) ? $waitlist->user->mobile : $waitlist->phone,
            !empty($waitlist->user) ? trans('update.registered') : trans('update.unregistered'),
            dateTimeFormat($waitlist->created_at, 'j M Y H:i')
        ];
    }
}
