<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use App\Models\QuoteRequestItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    /**
     * Display the Quote Cart / Request page.
     */
    public function index()
    {
        return view('quote_cart');
    }

    /**
     * Submit a new Quote Request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            'customer_email' => 'nullable|email|max:255',
            'organization_name' => 'nullable|string|max:255',
            'type' => 'required|string|in:bulk_order,donation,corporate,general',
            'customer_note' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
        ], [
            'customer_name.required' => 'Lütfen adınızı ve soyadınızı giriniz.',
            'customer_phone.required' => 'Lütfen telefon numaranızı giriniz.',
            'items.required' => 'Teklif sepetinizde en az 1 ürün bulunmalıdır.',
            'items.min' => 'Teklif sepetinizde en az 1 ürün bulunmalıdır.',
        ]);

        $year = date('Y');
        $random = strtoupper(Str::random(5));
        $count = QuoteRequest::whereYear('created_at', $year)->count() + 1;
        $quoteNo = sprintf('TK-%s-%04d', $year, $count);

        $estimatedTotal = 0;
        foreach ($request->items as $item) {
            $price = floatval($item['price'] ?? 0);
            $qty = intval($item['qty'] ?? 1);
            $estimatedTotal += ($price * $qty);
        }

        $quote = QuoteRequest::create([
            'quote_no' => $quoteNo,
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'organization_name' => $request->organization_name,
            'type' => $request->type,
            'customer_note' => $request->customer_note,
            'estimated_total' => $estimatedTotal,
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            $price = floatval($item['price'] ?? 0);
            $qty = intval($item['qty'] ?? 1);
            
            QuoteRequestItem::create([
                'quote_request_id' => $quote->id,
                'product_id' => $item['id'] ?? null,
                'product_name' => $item['name'],
                'product_sku' => $item['sku'] ?? null,
                'product_image' => $item['image'] ?? null,
                'unit_price' => $price,
                'quantity' => $qty,
                'total_price' => ($price * $qty),
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'quote_no' => $quoteNo,
                'message' => 'Teklif talebiniz başarıyla oluşturuldu.',
                'redirect_url' => route('quote.success', ['quote_no' => $quoteNo]),
            ]);
        }

        return redirect()->route('quote.success', ['quote_no' => $quoteNo])
            ->with('success', 'Teklif talebiniz başarıyla alındı.');
    }

    /**
     * Display the Quote Success & Tracking page.
     */
    public function success(string $quote_no)
    {
        $quote = QuoteRequest::with('items')->where('quote_no', $quote_no)->firstOrFail();
        return view('quote_success', compact('quote'));
    }
}
