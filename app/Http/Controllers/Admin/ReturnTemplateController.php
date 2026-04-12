<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnTemplate;
use Illuminate\Http\Request;

class ReturnTemplateController extends Controller
{
    public function index()
    {
        $templates = ReturnTemplate::latest()->paginate(20);
        return view('admin.return_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.return_templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        ReturnTemplate::create($validated);

        return redirect()->route('admin.return-templates.index')->with('success', 'İade şablonu başarıyla oluşturuldu.');
    }

    public function edit(ReturnTemplate $returnTemplate)
    {
        return view('admin.return_templates.edit', ['template' => $returnTemplate]);
    }

    public function update(Request $request, ReturnTemplate $returnTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $returnTemplate->update($validated);

        return redirect()->route('admin.return-templates.index')->with('success', 'İade şablonu başarıyla güncellendi.');
    }

    public function destroy(ReturnTemplate $returnTemplate)
    {
        $returnTemplate->delete();
        return back()->with('success', 'İade şablonu silindi.');
    }
}
