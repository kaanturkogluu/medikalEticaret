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
}
