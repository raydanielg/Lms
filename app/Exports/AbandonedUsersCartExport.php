<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbandonedUsersCartExport implements FromCollection, WithHeadings, WithMapping
{
    protected $carts;

    public function __construct($carts)
    {
        $this->carts = $carts;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->carts;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.user'),
            trans('public.user_role'),
            trans('cart.cart_items'),
            trans('admin/main.amount'),
            trans('update.coupons'),
            trans('update.reminders'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($cart): array
    {

        return [
            $cart->user->full_name,
            trans('admin/main.'.$cart->user->role_name),
            $cart->total_items,
            handlePrice($cart->total_amount),
            '--',
            '--',
        ];
    }
}
