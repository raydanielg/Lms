<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CashbackHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->transactions;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.user'),
            trans('update.total_purchase'),
            trans('update.total_cashback'),
            trans('update.last_cashback'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($transaction): array
    {
        return [
            $transaction->user->full_name,
            handlePrice($transaction->purchase_amount),
            handlePrice($transaction->total_cashback),
            dateTimeFormat($transaction->last_cashback, 'j M Y'),
        ];
    }
}
