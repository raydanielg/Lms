<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserLoginHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sessions;

    public function __construct($sessions)
    {
        $this->sessions = $sessions;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->sessions;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'ID',
            trans('admin/main.user'),
            trans('admin/main.mobile'),
            trans('admin/main.email'),
            trans('update.os'),
            trans('update.browser'),
            trans('update.device'),
            trans('update.ip_address'),
            trans('update.country'),
            trans('update.city'),
            trans('update.lat,long'),
            trans('update.session_start'),
            trans('update.session_end'),
            trans('public.duration'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($session): array
    {

        return [
            $session->user->id,
            $session->user->full_name,
            !empty($session->user->mobile) ? $session->user->mobile : '-',
            !empty($session->user->email) ? $session->user->email : '-',
            $session->os ?? '-',
            $session->browser ?? '-',
            $session->device ?? '-',
            $session->ip ?? '-',
            $session->country ?? '-',
            $session->city ?? '-',
            $session->location ?? '-',
            dateTimeFormat($session->session_start_at, 'j M Y H:i'),
            !empty($session->session_end_at) ? dateTimeFormat($session->session_end_at, 'j M Y H:i') : '-',
            $session->getDuration(),
        ];
    }
}
