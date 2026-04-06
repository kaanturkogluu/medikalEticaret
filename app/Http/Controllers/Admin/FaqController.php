<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('order_index')->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order_index' => 'nullable|integer',
        ]);

        Faq::create($request->all());

        return redirect()->route('admin.faqs.index')->with('success', 'Soru başarıyla eklendi.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order_index' => 'nullable|integer',
        ]);

        $faq->update($request->all());

        return redirect()->route('admin.faqs.index')->with('success', 'Soru başarıyla güncellendi.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'Soru silindi.');
    }

    public function toggle(Faq $faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);
        return back()->with('success', 'Durum güncellendi.');
    }
}
