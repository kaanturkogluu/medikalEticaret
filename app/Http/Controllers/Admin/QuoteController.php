<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    /**
     * Display a listing of quote requests.
     */
    public function index(Request $request)
    {
        $query = QuoteRequest::with(['items', 'user', 'createdProduct'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('quote_no', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('organization_name', 'like', "%{$search}%");
            });
        }

        $quotes = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => QuoteRequest::count(),
            'pending' => QuoteRequest::where('status', 'pending')->count(),
            'offered' => QuoteRequest::where('status', 'offered')->count(),
            'converted' => QuoteRequest::where('status', 'converted')->count(),
            'completed' => QuoteRequest::where('status', 'completed')->count(),
        ];

        return view('admin.quotes.index', compact('quotes', 'stats'));
    }

    /**
     * Show the detailed view of a quote request.
     */
    public function show(QuoteRequest $quote)
    {
        $quote->load(['items.product', 'user', 'createdProduct']);
        return view('admin.quotes.show', compact('quote'));
    }

    /**
     * Update status and notes.
     */
    public function updateStatus(Request $request, QuoteRequest $quote)
    {
        $request->validate([
            'status' => 'required|string|in:pending,reviewed,offered,converted,completed,rejected',
            'admin_notes' => 'nullable|string|max:3000',
        ]);

        $quote->status = $request->status;
        if ($request->has('admin_notes')) {
            $quote->admin_notes = $request->admin_notes;
        }
        $quote->save();

        return redirect()->route('admin.quotes.show', $quote)->with('success', 'Teklif durumu güncellendi.');
    }

    /**
     * Save offered price to customer.
     */
    public function updateOffer(Request $request, QuoteRequest $quote)
    {
        $request->validate([
            'offered_total' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string|max:3000',
        ]);

        $quote->offered_total = $request->offered_total;
        $quote->status = 'offered';
        if ($request->filled('admin_notes')) {
            $quote->admin_notes = $request->admin_notes;
        }
        $quote->save();

        return redirect()->route('admin.quotes.show', $quote)->with('success', 'Müşteriye teklif edilen özel fiyat kaydedildi.');
    }

    /**
     * Generate custom product & payment link for the customer.
     */
    public function generateProduct(Request $request, QuoteRequest $quote)
    {
        $request->validate([
            'price' => 'required|numeric|min:1',
            'custom_name' => 'nullable|string|max:255',
            'custom_description' => 'nullable|string|max:5000',
        ]);

        $productName = $request->filled('custom_name') 
            ? $request->custom_name 
            : "Özel Teklif Siparişi - {$quote->customer_name} (#{$quote->quote_no})";

        // Build itemized description
        if ($request->filled('custom_description')) {
            $description = $request->custom_description;
        } else {
            $descriptionHtml = "<div class='p-4 bg-emerald-50 rounded-xl border border-emerald-200 mb-4'>";
            $descriptionHtml .= "<h3 class='font-bold text-emerald-900 text-lg mb-2'>#{$quote->quote_no} Numaralı Özel Teklif Paketi</h3>";
            $descriptionHtml .= "<p class='text-sm text-emerald-700 mb-3'>Bu sipariş, <strong>{$quote->customer_name}</strong> adına hazırlanan özel fiyatlı pakettir.</p>";
            $descriptionHtml .= "<h4 class='font-semibold text-slate-800 text-sm mb-2'>Paket İçeriği:</h4><ul class='list-disc pl-5 space-y-1 text-sm text-slate-700'>";
            
            foreach ($quote->items as $item) {
                $descriptionHtml .= "<li><strong>{$item->quantity} Adet</strong> - {$item->product_name}</li>";
            }
            $descriptionHtml .= "</ul></div>";
            $description = $descriptionHtml;
        }

        $slug = Str::slug("ozel-siparis-{$quote->quote_no}-" . Str::lower(Str::random(5)));

        // Create the product in the system
        $product = Product::create([
            'name' => $productName,
            'slug' => $slug,
            'price' => $request->price,
            'stock' => 1,
            'active' => 1,
            'free_shipping' => 1,
            'description' => $description,
            'brand_name' => 'umutMed Özel Sipariş',
            'category_name' => 'Özel Teklif Siparişleri',
        ]);

        // Link first item's image if available
        $firstItemWithImage = $quote->items->whereNotNull('product_image')->first();
        if ($firstItemWithImage && $firstItemWithImage->product_image) {
            $product->productImages()->create([
                'url' => $firstItemWithImage->product_image,
                'path' => $firstItemWithImage->product_image,
                'is_primary' => 1,
            ]);
        }

        $paymentLink = route('product.detail', ['slug' => $product->slug]);

        $quote->created_product_id = $product->id;
        $quote->custom_payment_link = $paymentLink;
        $quote->offered_total = $request->price;
        $quote->status = 'converted';
        $quote->save();

        return redirect()->route('admin.quotes.show', $quote)->with('success', 'Özel sipariş ürünü ve ödeme linki başarıyla oluşturuldu!');
    }

    /**
     * Delete a quote request.
     */
    public function destroy(QuoteRequest $quote)
    {
        $quote->delete();
        return redirect()->route('admin.quotes.index')->with('success', 'Teklif talebi silindi.');
    }
}
