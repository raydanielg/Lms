<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GiftHistoriesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $gifts;

    public function __construct($gifts)
    {
        $this->gifts = $gifts;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->gifts;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.title'),
            trans('admin/main.sender'),
            trans('update.sender_mobile'),
            trans('update.sender_email'),
            trans('update.receipt'),
            trans('update.receipt_email'),
            trans('update.receipt_status'),
            trans('update.gift_message'),
            trans('admin/main.amount'),
            trans('update.submit_date'),
            trans('update.receive_date'),
            trans('update.gift_status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($gift): array
    {
        $status = trans('update.sent');
        if (!empty($gift->date) and $gift->date > time()) {
            $status = trans('admin/main.pending');
        } elseif ($gift->status == 'cancel') {
            $status = trans('admin/main.pending');
        }

        return [
            $gift->getItemTitle(),
            $gift->user->full_name,
            $gift->user->mobile,
            $gift->user->email,
            !empty($gift->receipt) ? $gift->receipt->full_name : $gift->name,
            $gift->email,
            !empty($gift->receipt) ? trans('update.registered') : trans('update.unregistered'),
            $gift->description,
            (!empty($gift->sale) and $gift->sale->total_amount > 0) ? handlePrice($gift->sale->total_amount) : trans('admin/main.free'),
            dateTimeFormat($gift->created_at, 'j M Y H:i'),
            !empty($gift->date) ? dateTimeFormat($gift->date, 'j M Y H:i') : trans('update.instantly'),
            $status
        ];
    }
}
