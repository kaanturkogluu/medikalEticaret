<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['parent'])->withCount('products');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $categories = $query->latest()->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'active' => 'boolean'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    public function edit(Category $category)
    {
        // Exclude the category itself and all its descendants to prevent circular references
        $excludeIds = $this->getDescendantIds($category);
        $excludeIds[] = $category->id;

        $parentCategories = Category::whereNotIn('id', $excludeIds)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    private function getDescendantIds(Category $category): array
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }
        return $ids;
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'active' => 'boolean'
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori başarıyla güncellendi.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Bu kategoriye bağlı ürünler olduğu için silemezsiniz.');
        }

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Bu kategorinin alt kategorileri olduğu için silemezsiniz.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori başarıyla silindi.');
    }

    public function toggleActive(Category $category)
    {
        $category->update(['active' => !$category->active]);
        return back()->with('success', 'Kategori durumu güncellendi.');
    }
}
