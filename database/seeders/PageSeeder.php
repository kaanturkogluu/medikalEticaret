<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            'Kullanım Koşulları',
            'Mesafeli Satış Sözleşmesi',
            'Gizlilik Politikası',
            'KVKK Aydınlatma Metni',
            'Çerez Politikası',
            'İade & İptal Politikası',
            'Tıbbi Sorumluluk Reddi',
            'Teslimat Politikası',
            'Ödeme Politikası',
            'Açık Rıza Metni'
        ];

        foreach ($pages as $title) {
            Page::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'content' => $title . " içeriği yakında eklenecektir. Lütfen düzenleyerek içeriği güncelleyin.",
                    'is_active' => true,
                ]
            );
        }
    }
}
