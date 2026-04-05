<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public function index()
    {
        return view('admin.appearance.index');
    }

    public function contact()
    {
        $settings = [
            'address' => Setting::getValue('contact_address', 'Atatürk Mah. No:1, Hatay'),
            'phone' => Setting::getValue('contact_phone', '0530 000 00 00'),
            'whatsapp' => Setting::getValue('contact_whatsapp', '905300000000'),
            'email' => Setting::getValue('contact_email', 'iletisim@umutmedikal.com'),
            'maps' => Setting::getValue('contact_maps_embed', ''),
        ];
        return view('admin.appearance.contact', compact('settings'));
    }

    public function updateContact(Request $request)
    {
        Setting::setValue('contact_address', $request->address);
        Setting::setValue('contact_phone', $request->phone);
        Setting::setValue('contact_whatsapp', $request->whatsapp);
        Setting::setValue('contact_email', $request->email);
        Setting::setValue('contact_maps_embed', $request->maps);
        
        return back()->with('success', 'İletişim bilgileri başarıyla güncellendi.');
    }

    public function marketplaces()
    {
        $defaultMarketplaces = [
            ['name' => 'TRENDYOL', 'url' => 'https://trendyol.com', 'logo' => 'https://www.google.com/s2/favicons?domain=trendyol.com&sz=128', 'color' => '#f27a1a'],
            ['name' => 'N11', 'url' => 'https://n11.com', 'logo' => 'https://www.google.com/s2/favicons?domain=n11.com&sz=128', 'color' => '#e11e24'],
            ['name' => 'HEPSİBURADA', 'url' => 'https://hepsiburada.com', 'logo' => 'https://www.google.com/s2/favicons?domain=hepsiburada.com&sz=128', 'color' => '#ff6000'],
            ['name' => 'AMAZON', 'url' => 'https://amazon.com.tr', 'logo' => 'https://www.google.com/s2/favicons?domain=amazon.com.tr&sz=128', 'color' => '#000000'],
        ];

        $marketplaces = json_decode(Setting::getValue('marketplaces', json_encode($defaultMarketplaces)), true);
        $marqueeText = Setting::getValue('marquee_text', "Açılışa Özel Tüm Ürünlerde %20'ye Varan İndirimler! • Saat 16:00'a Kadar Verilen Siparişlerde Aynı Gün Kargo! • Ücretsiz Kargo Fırsatını Kaçırmayın!");

        return view('admin.appearance.marketplaces', compact('marketplaces', 'marqueeText'));
    }

    public function updateMarketplaces(Request $request)
    {
        Setting::setValue('marketplaces', json_encode($request->marketplaces));
        Setting::setValue('marquee_text', $request->marquee_text);

        return back()->with('success', 'Pazaryeri ve kayan yazı ayarları güncellendi.');
    }

    public function social()
    {
        $settings = [
            'facebook' => Setting::getValue('social_facebook', '#'),
            'instagram' => Setting::getValue('social_instagram', '#'),
            'twitter' => Setting::getValue('social_twitter', '#'),
            'linkedin' => Setting::getValue('social_linkedin', '#'),
            'google_play' => Setting::getValue('app_google_play', '#'),
            'apple_store' => Setting::getValue('app_apple_store', '#'),
            'whatsapp_active' => Setting::getValue('whatsapp_support_active', true),
            'social_active' => Setting::getValue('social_media_active', true),
            'app_stores_active' => Setting::getValue('app_stores_active', true),
        ];

        return view('admin.appearance.social', compact('settings'));
    }

    public function updateSocial(Request $request)
    {
        Setting::setValue('social_facebook', $request->facebook);
        Setting::setValue('social_instagram', $request->instagram);
        Setting::setValue('social_twitter', $request->twitter);
        Setting::setValue('social_linkedin', $request->linkedin);
        Setting::setValue('app_google_play', $request->google_play);
        Setting::setValue('app_apple_store', $request->apple_store);
        Setting::setValue('whatsapp_support_active', $request->has('whatsapp_active'));
        Setting::setValue('social_media_active', $request->has('social_active'));
        Setting::setValue('app_stores_active', $request->has('app_stores_active'));

        return back()->with('success', 'Sosyal medya ve destek ayarları güncellendi.');
    }

    public function general()
    {
        $defaultFooter = [
            ["title" => "umutMed", "links" => [["text" => "Hakkımızda", "url" => "#"], ["text" => "Kariyer", "url" => "#"], ["text" => "İletişim", "url" => "/iletisim"], ["text" => "Sürdürülebilirlik", "url" => "#"]]],
            ["title" => "Kampanyalar", "links" => [["text" => "Aktif Kampanyalar", "url" => "#"], ["text" => "Elite Üyelik", "url" => "#"], ["text" => "Hediye Fikirleri", "url" => "#"], ["text" => "umutMed Blog", "url" => "#"]]],
            ["title" => "Yardım", "links" => [["text" => "Sıkça Sorulan Sorular", "url" => "#"], ["text" => "İade Politikası", "url" => "#"], ["text" => "Ödeme Seçenekleri", "url" => "#"], ["text" => "Kullanım Koşulları", "url" => "#"]]]
        ];

        $settings = [
            'primary_color' => Setting::getValue('site_primary_color', '#f27a1a'),
            'site_title' => Setting::getValue('site_title', 'umutMed Market'),
            'footer_qr' => Setting::getValue('site_footer_qr', ''),
            'footer_columns' => json_decode(Setting::getValue('site_footer_columns', json_encode($defaultFooter)), true)
        ];

        return view('admin.appearance.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        Setting::setValue('site_primary_color', $request->primary_color);
        Setting::setValue('site_title', $request->site_title);
        Setting::setValue('site_footer_qr', $request->footer_qr);
        Setting::setValue('site_footer_columns', json_encode($request->footer_columns));

        return back()->with('success', 'Genel görünüm ayarları güncellendi.');
    }

    public function tabSwitch()
    {
        $settings = [
            'active' => Setting::getValue('tab_switch_active', true),
            'away_title' => Setting::getValue('tab_switch_away_title', 'Bizi Unutma! 😢'),
            'back_title' => Setting::getValue('tab_switch_back_title', 'Hoş Geldin! 😍'),
        ];

        return view('admin.appearance.tab_switch', compact('settings'));
    }

    public function updateTabSwitch(Request $request)
    {
        Setting::setValue('tab_switch_active', $request->has('active'));
        Setting::setValue('tab_switch_away_title', $request->away_title);
        Setting::setValue('tab_switch_back_title', $request->back_title);

        return back()->with('success', 'Sekme başlık ayarları güncellendi.');
    }
}
