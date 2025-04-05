<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReferralUsersExport implements FromCollection, WithHeadings, WithMapping
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
            trans('admin/main.user'),
            trans('admin/main.role'),
            trans('admin/main.user_group'),
            trans('admin/main.referral_code'),
            trans('admin/main.amount'),
            trans('admin/main.commission'),
            trans('admin/main.status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($referral): array
    {
        $currency = $this->currency;

        $userType = '';
        if ($referral->affiliateUser->isUser()) {
            $userType = 'Student';
        } elseif ($referral->affiliateUser->isTeacher()) {
            $userType = 'Teacher';
        } elseif ($referral->affiliateUser->isOrganization()) {
            $userType = 'Organization';
        }


        return [
            $referral->affiliateUser->full_name,
            $userType,
            !empty($referral->affiliateUser->getUserGroup()) ? $referral->affiliateUser->getUserGroup()->name : '-',
            !empty($referral->affiliateUser->affiliateCode) ? $referral->affiliateUser->affiliateCode->code : '',
            $currency . $referral->getTotalAffiliateRegistrationAmounts(),
            $currency . $referral->getTotalAffiliateCommissions(),
            $referral->affiliateUser->affiliate ? trans('admin/main.yes') : trans('admin/main.no'),
        ];
    }
}
