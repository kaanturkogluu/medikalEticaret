<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->get();
        return view('admin.appearance.banner.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.appearance.banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'title_color' => 'nullable|string|max:7',
            'subtitle_color' => 'nullable|string|max:7',
            'title_size' => 'nullable|integer|min:10|max:150',
            'subtitle_size' => 'nullable|integer|min:8|max:50',
            'buttons' => 'nullable|array',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        Banner::create([
            'image_path' => $path,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'title_color' => $request->title_color ?? '#FFFFFF',
            'subtitle_color' => $request->subtitle_color ?? '#FFFFFF',
            'title_size' => $request->title_size ?? 60,
            'subtitle_size' => $request->subtitle_size ?? 12,
            'buttons' => $request->buttons,
            'order' => Banner::max('order') + 1,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.appearance.banner.index')->with('success', 'Banner başarıyla eklendi.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.appearance.banner.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'title_color' => 'nullable|string|max:7',
            'subtitle_color' => 'nullable|string|max:7',
            'title_size' => 'nullable|integer|min:10|max:150',
            'subtitle_size' => 'nullable|integer|min:8|max:50',
            'buttons' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image_path);
            $banner->image_path = $request->file('image')->store('banners', 'public');
        }

        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->title_color = $request->title_color ?? '#FFFFFF';
        $banner->subtitle_color = $request->subtitle_color ?? '#FFFFFF';
        $banner->title_size = $request->title_size ?? 60;
        $banner->subtitle_size = $request->subtitle_size ?? 12;
        $banner->buttons = $request->buttons;
        $banner->is_active = $request->has('is_active');
        $banner->save();

        return redirect()->route('admin.appearance.banner.index')->with('success', 'Banner başarıyla güncellendi.');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        return back()->with('success', 'Banner başarıyla silindi.');
    }

    public function toggle(Banner $banner)
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();
        return back()->with('success', 'Banner durumu güncellendi.');
    }
}
