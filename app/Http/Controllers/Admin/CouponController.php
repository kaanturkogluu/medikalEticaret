<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::with('categories');

        // Status Filter (default: unused)
        if ($request->has('status')) {
            if ($request->status === 'used') {
                $query->where('is_used', true);
            } elseif ($request->status === 'unused') {
                $query->where('is_used', false);
            }
            // 'all' doesn't add a where clause
        } else {
            // Default: Unused
            $query->where('is_used', false);
        }

        // Type Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $coupons = $query->latest()->paginate(20)->withQueryString();
        $categories = \App\Models\Category::has('products')->get();
        
        return view('admin.coupons.index', compact('coupons', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'count' => 'nullable|integer|min:1|max:50',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $count = $request->input('count', 1);

        for ($i = 0; $i < $count; $i++) {
            $code = $this->generateUniqueCode();
            $coupon = Coupon::create([
                'code' => $code,
                'type' => $request->type,
                'value' => $request->value,
            ]);

            if ($request->filled('category_ids')) {
                $coupon->categories()->sync($request->category_ids);
            }
        }

        return redirect()->back()->with('success', "$count adet kupon başarıyla oluşturuldu.");
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->is_used) {
            return redirect()->back()->with('error', 'Kullanılmış kupon silinemez.');
        }

        $coupon->delete();
        return redirect()->back()->with('success', 'Kupon başarıyla silindi.');
    }

    public function print(Request $request)
    {
        $ids = explode(',', $request->ids);
        $coupons = Coupon::whereIn('id', $ids)->take(3)->get();
        
        if ($coupons->isEmpty()) {
            return redirect()->back()->with('error', 'Lütfen en az bir kupon seçin.');
        }

        return view('admin.coupons.print', compact('coupons'));
    }

    protected function generateUniqueCode(): string
    {
        do {
            // Generate 6 random uppercase letters
            $code = strtoupper(Str::random(6));
            // Check if code contains only letters as per user request (Str::random can include numbers)
            // But usually alphanumeric is better. User specifically said "6 harflerden" (6 letters).
            // Let's stick to letters.
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= chr(rand(65, 90)); // A-Z
            }
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }
}
