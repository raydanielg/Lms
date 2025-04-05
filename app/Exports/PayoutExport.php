<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayoutExport implements FromCollection, WithHeadings, WithMapping
{
    protected $payouts;

    public function __construct($payouts)
    {
        $this->payouts = $payouts;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->payouts;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.user'),
            trans('admin/main.role'),
            trans('admin/main.payout_amount'),
            trans('admin/main.bank'),
            trans('admin/main.phone'),
            trans('admin/main.last_payout_date'),
            trans('admin/main.status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($payout): array
    {
        $bank = $payout->userSelectedBank->bank;

        $bankTitle = $bank->title . " (";
        foreach ($bank->specifications as $specification) {
            $selectedBankSpecification = $payout->userSelectedBank->specifications->where('user_selected_bank_id', $payout->userSelectedBank->id)->where('user_bank_specification_id', $specification->id)->first();

            $bankTitle .= "{$specification->name}: {$selectedBankSpecification->value}";
        }
        $bankTitle .= ")";


        return [
            $payout->user->full_name,
            $payout->user->role->caption,
            handlePrice($payout->amount),
            $bankTitle,
            $payout->user->mobile,
            dateTimeFormat($payout->created_at, 'Y/m/j-H:i'),
            trans('public.' . $payout->status)
        ];
    }
}
