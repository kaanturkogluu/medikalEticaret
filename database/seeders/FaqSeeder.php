<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Siparişim kaç günde elime ulaşır?',
                'answer' => "Hafta içi saat 16:00'a kadar verdiğiniz siparişler aynı gün kargoya teslim edilmektedir. Teslimat süresi bulunduğunuz ilin mesafesine bağlı olarak 1 ile 3 iş günü arasında değişmektedir.",
                'order_index' => 1
            ],
            [
                'question' => 'Kargo ücreti ne kadar?',
                'answer' => "Marketimiz üzerinden yapacağınız 700 TL ve üzeri alışverişlerinizde kargo ücretsizdir. 700 TL altındaki siparişlerinizde ise 89 TL standart kargo ücreti uygulanmaktadır.",
                'order_index' => 2
            ],
            [
                'question' => 'İade ve değişim şartları nelerdir?',
                'answer' => "Satın aldığınız ürünü, orijinal ambalajı açılmamış, bozulmamış ve tarafınızca kullanılmamış olması şartıyla 14 gün içerisinde iade edebilirsiniz. İade talebi oluştururken ürünün mevcut durumunu gösteren net bir fotoğrafını sisteme yüklemeniz ve orijinal faturası ile birlikte göndermeniz zorunludur. Hijyenik veya tek kullanımlık ürünlerde ambalajı açılmış ürünlerin iadesi yasal mevzuat gereği kabul edilememektedir.",
                'order_index' => 3
            ],
            [
                'question' => 'Hangi ödeme yöntemlerini kullanabilirim?',
                'answer' => "Kredi kartı, banka kartı ve havale/EFT ile ödeme yapabilirsiniz. Kredi kartlarına vade farkı olmadan 3 taksit imkanımız bulunmaktadır.",
                'order_index' => 4
            ],
            [
                'question' => 'Siparişimi nasıl takip edebilirim?',
                'answer' => "Siparişiniz kargoya verildiğinde size SMS ve e-posta ile bir takip numarası iletilecektir. Ayrıca 'Hesabım > Siparişlerim' sayfasından kargo durumunuzu anlık olarak kontrol edebilirsiniz.",
                'order_index' => 5
            ]
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
