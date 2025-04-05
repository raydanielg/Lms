<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StoreProductsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->products;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.id'),
            trans('admin/main.title'),
            trans('admin/main.creator'),
            trans('admin/main.type'),
            trans('update.inventory'),
            trans('admin/main.price'),
            trans('update.delivery_fee'),
            trans('admin/main.sales'),
            trans('admin/main.income'),
            trans('admin/main.updated_at'),
            trans('admin/main.created_at'),
            trans('admin/main.status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($product): array
    {
        $getAvailability = $product->getAvailability();

        $status = '';

        switch ($product->status) {
            case(\App\Models\Product::$active):
                $status = trans('admin/main.published');
                break;
            case(\App\Models\Product::$draft):
                $status = trans('admin/main.is_draft');
                break;
            case(\App\Models\Product::$pending):
                $status = trans('admin/main.waiting');
                break;
            case(\App\Models\Product::$inactive):
                $status = trans('public.rejected');
                break;
        }

        return [
            $product->id,
            !empty($product->category) ? $product->category->title : '',
            !empty($product->creator) ? $product->creator->full_name : '',
            trans('update.' . $product->type),
            ($getAvailability == 99999) ? trans('update.unlimited') : $getAvailability,
            !empty($product->price) ? handlePrice($product->price) : '-',
            $product->delivery_fee ? handlePrice($product->delivery_fee) : '-',
            $product->salesCount(),
            handlePrice($product->sales()->sum('total_amount')),
            dateTimeFormat($product->updated_at, 'Y M j | H:i'),
            dateTimeFormat($product->created_at, 'Y M j | H:i'),
            $status,
        ];
    }
}
