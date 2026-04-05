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
            [
                'title' => 'Kullanım Koşulları',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">1. Kabul Edilme</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Bu internet sitesine girerek veya üye olarak aşağıdaki şartları kabul etmiş sayılırsınız. Şartları kabul etmiyorsanız lütfen siteyi kullanmayınız.</p>
                    
                    <h3 class="text-xl font-bold mb-4 italic uppercase">2. Fikri Mülkiyet</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic underline decoration-brand-500 decoration-2">Sitedeki tüm logo, görsel ve metinler tarafımıza aittir ve izinsiz kopyalanamaz.</p>
                    
                    <h3 class="text-xl font-bold mb-4 italic uppercase">3. Sorumluluk Sınırı</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Sitedeki bilgilerin doğruluğu için azami gayret gösterilse de, teknik hatalardan kaynaklı durumlarda firmamız sorumlu tutulamaz.</p>
                '
            ],
            [
                'title' => 'Mesafeli Satış Sözleşmesi',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">MADDE 1 - TARAFLAR</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Bu sözleşme bir tarafta SATICI (Firmamız) ile diğer tarafta ALICI (Müşteri) arasında dijital ortamda akdedilmiştir.</p>
                    
                    <h3 class="text-xl font-bold mb-4 italic uppercase">MADDE 2 - KONU</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">İşbu sözleşmenin konusu, ALICI\'nın SATICI\'ya ait internet sitesinden elektronik ortamda siparişini yaptığı ürünün satışı ve teslimi ile ilgili 6502 sayılı Tüketicinin Korunması Hakkında Kanun hükümlerince tarafların hak ve yükümlülüklerinin saptanmasıdır.</p>
                    
                    <h3 class="text-xl font-bold mb-4 italic uppercase">MADDE 3 - TESLİMAT</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Ürün, ALICI\'nın belirttiği adrese en geç 30 gün içerisinde kargo yoluyla teslim edilir.</p>
                '
            ],
            [
                'title' => 'Gizlilik Politikası',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">Veri Güvenliği İlkesi</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Müşterilerimizin gizliliği bizim için en öncelikli konudur. E-posta ve kişisel bilgileriniz asla üçüncü şahıslarla reklam veya satış amacıyla paylaşılmaz.</p>
                    
                    <h3 class="text-xl font-bold mb-4 italic uppercase">Hangi Verileri Topluyoruz?</h3>
                    <p class="mb-2 text-slate-600 italic">● Ad, Soyad</p>
                    <p class="mb-2 text-slate-600 italic">● İletişim Bilgileri</p>
                    <p class="mb-2 text-slate-600 italic">● Sipariş Geçmişi</p>
                    
                    <p class="mt-4 text-slate-600 italic border-l-4 border-brand-500 pl-4">Ödeme bilgileriniz (Kredi Kartı vb.) doğrudan banka altyapısında işlenir; biz bu verileri saklamayız.</p>
                '
            ],
            [
                'title' => 'KVKK Aydınlatma Metni',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">6698 Sayılı Kanun Bilgilendirmesi</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Kişisel Verilerin Korunması Kanunu (KVKK) uyarınca, verilerinizin hangi amaçla hangi birimlerce işlendiği hakkında sizi bilgilendirmek isteriz.</p>
                    
                    <h3 class="text-xl font-bold mb-4 italic uppercase">Haklarınız</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Kullanıcı dilediği zaman verilerinin silinmesini veya güncellenmesini talep edebilir. Başvurularınızı destek merkezimize iletebilirsiniz.</p>
                '
            ],
            [
                'title' => 'Çerez Politikası',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">Çerezler (Cookies) Hakkında</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Sitemizi daha verimli kullanabilmeniz ve size özel içerikler sunabilmemiz için çerezleri kullanmaktayız.</p>
                    <p class="text-slate-600 italic italic">Tarayıcı ayarlarınızdan çerezleri engelleyebilirsiniz, ancak bu durumda sitemizin bazı özellikleri çalışmayabilir.</p>
                '
            ],
            [
                'title' => 'İade & İptal Politikası',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">14 Günlük Cayma Hakkı</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic border-b border-brand-500 pb-2 inline-block">Online alışverişlerinizde, ürünü teslim aldığınız tarihten itibaren 14 gün içinde hiçbir gerekçe göstermeksizin iade edebilirsiniz.</p>
                    
                    <h3 class="text-xl font-bold mb-4 italic uppercase">İade Şartları</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">İade edilecek ürünün orijinal ambalajında, hasarsız ve tekrar satılabilir olması gerekmektedir. Hijyenik ürünlerde (açılmış paket vb.) iade kabul edilememektedir.</p>
                '
            ],
            [
                'title' => 'Tıbbi Sorumluluk Reddi',
                'content' => '
                    <div class="bg-red-50 p-8 rounded-3xl border border-red-100 italic">
                        <h3 class="text-xl font-bold mb-4 uppercase text-red-700">⚠️ Önemli Uyarı</h3>
                        <p class="mb-4 text-red-600 leading-relaxed">Bu internet sitesinde yer alan bilgiler, ürün açıklamaları ve makaleler, teşhis veya tedavi önerisi niteliği taşımaz. Sitedeki hiçbir içerik, doktor tavsiyesinin yerine geçmez.</p>
                        <p class="text-red-600 leading-relaxed">Herhangi bir tıbbi uygulama, takviye gıda veya cihaz kullanımı öncesi mutlaka uzman bir hekime danışınız.</p>
                    </div>
                '
            ],
            [
                'title' => 'Teslimat Politikası',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">Hızlı Teslimat Güvencesi</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Siparişleriniz, stokta olması durumunda 24 saat içinde işleme alınır. Türkiye genelinde kargo süresi ortalama 1-4 iş günüdür.</p>
                    <p class="text-slate-600 italic">Belirli bir tutarın üzerindeki alışverişlerde kargo ücretsizdir.</p>
                '
            ],
            [
                'title' => 'Ödeme Politikası',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">Güvenli Ödeme Kanalları</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic italic">Sitemizde SSL sertifikası (256-bit) ile yüksek güvenlikli ödeme altyapısı kullanılmaktadır. Ödemelerinizi kredi kartı, banka kartı veya havale yoluyla güvenle gerçekleştirebilirsiniz.</p>
                '
            ],
            [
                'title' => 'Açık Rıza Metni',
                'content' => '
                    <h3 class="text-xl font-bold mb-4 italic uppercase">Bilgilendirme ve Onay</h3>
                    <p class="mb-4 text-slate-600 leading-relaxed italic">Kişisel verilerimin KVKK kapsamında işlenmesini, pazarlama faaliyetlerinde kullanılmasını ve tarafıma ticari elektronik ileti (SMS, E-posta) gönderilmesini kabul ediyorum.</p>
                '
            ]
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(
                ['slug' => Str::slug($p['title'])],
                [
                    'title' => $p['title'],
                    'content' => trim($p['content']),
                    'is_active' => true,
                ]
            );
        }
    }
}
