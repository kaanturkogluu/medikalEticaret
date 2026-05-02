<?php

namespace Database\Seeders;

use App\Models\ShippingCompany;
use Illuminate\Database\Seeder;

class ShippingCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Aras Kargo',
                'tracking_url' => 'https://www.araskargo.com.tr/kargo-takip/[TRACKING_CODE]',
            ],
            [
                'name' => 'Yurtiçi Kargo',
                'tracking_url' => 'https://www.yurticikargo.com/tr/online-servisler/kargo-takip?code=[TRACKING_CODE]',
            ],
            [
                'name' => 'MNG Kargo',
                'tracking_url' => 'https://www.mngkargo.com.tr/kargotakip?query=[TRACKING_CODE]',
            ],
            [
                'name' => 'Sürat Kargo',
                'tracking_url' => 'https://www.suratkargo.com.tr/KargoTakip/?takipno=[TRACKING_CODE]',
            ],
            [
                'name' => 'PTT Kargo',
                'tracking_url' => 'https://gonderitakip.ptt.gov.tr/Track/Verify?id=[TRACKING_CODE]',
            ],
            [
                'name' => 'Trendyol Express',
                'tracking_url' => 'https://kargotakip.trendyol.com/?tracking_number=[TRACKING_CODE]',
            ],
            [
                'name' => 'Hepsijet',
                'tracking_url' => 'https://www.hepsijet.com/gonderi-takibi/[TRACKING_CODE]',
            ],
        ];

        foreach ($companies as $company) {
            ShippingCompany::updateOrCreate(['name' => $company['name']], $company);
        }
    }
}
