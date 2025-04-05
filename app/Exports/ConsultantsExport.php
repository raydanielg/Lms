<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ConsultantsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $consultants;
    protected $currency;

    public function __construct($consultants)
    {
        $this->consultants = $consultants;
        $this->currency = currencySign();
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->consultants;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Appointments Sales count',
            'Appointments Sales amount',
            'Pending Appointments',
            'Wallet Charge',
            'User Group',
            'Register Date',
            'Status',
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($consultant): array
    {
        return [
            $consultant->id,
            $consultant->full_name,
            $consultant->sales_count,
            $consultant->sales_amount,
            $consultant->pendingAppointments,
            $consultant->getAccountingBalance(),
            !empty($consultant->userGroup) ? $consultant->userGroup->group->name : '-',
            dateTimeFormat($consultant->created_at, 'Y/m/j - H:i'),
            ($consultant->disabled) ? 'Unavailable' : 'Available'
        ];
    }
}
