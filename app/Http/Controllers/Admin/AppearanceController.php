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
}
