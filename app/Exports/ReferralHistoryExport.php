<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReferralHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $referrals;
    protected $currency;

    public function __construct($referrals)
    {
        $this->referrals = $referrals;
        $this->currency = currencySign();
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->referrals;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.affiliate_user'),
            trans('admin/main.referred_user'),
            trans('admin/main.total_affiliate_amount'),
            trans('admin/main.total_affiliate_commission'),
            trans('admin/main.total_referred_amount'),
            trans('admin/main.date'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($referral): array
    {
        $currency = $this->currency;

        return [
            $referral->affiliateUser->full_name,
            $referral->referredUser->full_name,
            $currency . $referral->getAffiliateRegistrationAmountsOfEachReferral(),
            $currency . $referral->getTotalAffiliateCommissionOfEachReferral(),
            $currency . $referral->getReferredAmount(),
            dateTimeFormat($referral->created_at, 'Y M j | H:i'),
        ];
    }
}
