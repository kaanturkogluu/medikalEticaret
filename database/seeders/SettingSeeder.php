<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $footerColumns = [
            [
                "title" => "umutMed",
                "links" => [
                    ["text" => "Hakkımızda", "url" => "/sayfa/hakkimizda"],
                    ["text" => "Kariyer", "url" => "#"],
                    ["text" => "İletişim", "url" => "/iletisim"],
                    ["text" => "Sürdürülebilirlik", "url" => "#"]
                ]
            ],
            [
                "title" => "Kampanyalar",
                "links" => [
                    ["text" => "Aktif Kampanyalar", "url" => "#"],
                    ["text" => "Elite Üyelik", "url" => "#"],
                    ["text" => "Hediye Fikirleri", "url" => "#"],
                    ["text" => "umutMed Blog", "url" => "#"]
                ]
            ],
            [
                "title" => "Yardım",
                "links" => [
                    ["text" => "Sıkça Sorulan Sorular", "url" => "#"],
                    ["text" => "İade Politikası", "url" => "/sayfa/iade-iptal-politikasi"],
                    ["text" => "Ödeme Seçenekleri", "url" => "/sayfa/odeme-politikasi"],
                    ["text" => "Kullanım Koşulları", "url" => "/sayfa/kullanim-kosullari"]
                ]
            ]
        ];

        Setting::updateOrCreate(['key' => 'site_footer_columns'], ['value' => json_encode($footerColumns)]);
        Setting::updateOrCreate(['key' => 'site_primary_color'], ['value' => '#f27a1a']);
        Setting::updateOrCreate(['key' => 'site_title'], ['value' => 'umutMed Market']);
        Setting::updateOrCreate(['key' => 'site_footer_qr'], ['value' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://umutmed.com']);
        
        // Social settings
        Setting::updateOrCreate(['key' => 'social_facebook'], ['value' => 'https://facebook.com/umutmed']);
        Setting::updateOrCreate(['key' => 'social_instagram'], ['value' => 'https://instagram.com/umutmed']);
        Setting::updateOrCreate(['key' => 'social_twitter'], ['value' => 'https://twitter.com/umutmed']);
        Setting::updateOrCreate(['key' => 'social_linkedin'], ['value' => 'https://linkedin.com/company/umutmed']);
        Setting::updateOrCreate(['key' => 'social_media_active'], ['value' => '1']);
        
        // App stores
        Setting::updateOrCreate(['key' => 'app_google_play'], ['value' => '#']);
        Setting::updateOrCreate(['key' => 'app_apple_store'], ['value' => '#']);
        Setting::updateOrCreate(['key' => 'app_stores_active'], ['value' => '1']);
        
        // Support
        Setting::updateOrCreate(['key' => 'whatsapp_support_active'], ['value' => '1']);
        Setting::updateOrCreate(['key' => 'support_phone'], ['value' => '0850 123 45 67']);
        Setting::updateOrCreate(['key' => 'support_email'], ['value' => 'destek@umutmed.com']);
    }
}
