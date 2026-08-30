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
     * Update quote request status and admin notes.
     */
    public function updateStatus(Request $request, QuoteRequest $quote)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,offered,converted,completed,rejected',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $quote->status;
        $quote->status = $request->status;
        $quote->admin_notes = $request->admin_notes;
        $quote->save();

        // Send notification email if status changed and customer has email
        if ($quote->customer_email && $oldStatus !== $quote->status) {
            try {
                \Illuminate\Support\Facades\Mail::to($quote->customer_email)->queue(new \App\Mail\QuoteStatusUpdatedMail($quote));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Quote status email error: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.quotes.show', $quote)->with('success', 'Teklif talebi durumu başarıyla güncellendi.');
    }

    /**
     * Update offer total price.
     */
    public function updateOffer(Request $request, QuoteRequest $quote)
    {
        $request->validate([
            'offered_total' => 'required|numeric|min:0',
        ]);

        $quote->offered_total = $request->offered_total;
        if ($quote->status === 'pending' || $quote->status === 'reviewed') {
            $quote->status = 'offered';
        }
        $quote->save();

        if ($quote->customer_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($quote->customer_email)->queue(new \App\Mail\QuoteStatusUpdatedMail($quote));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Quote offer email error: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.quotes.show', $quote)->with('success', 'Teklif fiyatı başarıyla kaydedildi.');
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

        $packageItems = $quote->items->map(function ($item) {
            return [
                'name' => $item->product_name,
                'quantity' => $item->quantity,
                'image' => $item->product_image,
                'sku' => $item->product_sku,
            ];
        })->toArray();

        $productName = $request->filled('custom_name') 
            ? $request->custom_name 
            : "Özel Teklif Siparişi - {$quote->customer_name} (#{$quote->quote_no})";

        $marketplaceData = [
            'quote_package' => $packageItems,
            'quote_no' => $quote->quote_no,
            'customer_name' => $quote->customer_name,
        ];

        // Build concise, clean itemized description without redundant noise
        if ($request->filled('custom_description')) {
            $description = $request->custom_description;
        } else {
            $descriptionHtml = "<div class='p-5 bg-slate-50 rounded-2xl border border-slate-200 mb-6'>";
            $descriptionHtml .= "<div class='flex items-center gap-3 mb-4 pb-3 border-b border-slate-200'>";
            $descriptionHtml .= "<h3 class='font-black text-slate-900 text-base'>#{$quote->quote_no} Numaralı Özel Teklif Paketi</h3>";
            $descriptionHtml .= "</div>";
            $descriptionHtml .= "<h4 class='text-xs font-black text-slate-700 uppercase tracking-wider mb-3'>Paket İçeriğindeki Ürünler:</h4>";
            $descriptionHtml .= "<div class='space-y-2'>";
            
            foreach ($quote->items as $item) {
                $imgTag = $item->product_image ? "<img src='{$item->product_image}' class='w-10 h-10 object-contain rounded-lg border border-slate-200 bg-white p-0.5' alt=''>" : "";
                $descriptionHtml .= "<div class='p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between gap-3'>";
                $descriptionHtml .= "<div class='flex items-center gap-3'>{$imgTag}<span class='text-xs font-bold text-slate-900'>{$item->product_name}</span></div>";
                $descriptionHtml .= "<span class='px-3 py-1 bg-amber-50 text-amber-900 border border-amber-200 rounded-lg text-xs font-black shrink-0'>{$item->quantity} Adet</span>";
                $descriptionHtml .= "</div>";
            }
            $descriptionHtml .= "</div></div>";
            $description = $descriptionHtml;
        }

        // Check if an existing custom product was already created for this quote
        if ($quote->created_product_id && ($existingProduct = Product::find($quote->created_product_id))) {
            $product = $existingProduct;
            $product->update([
                'name' => $productName,
                'price' => $request->price,
                'stock' => 1,
                'active' => 1,
                'free_shipping' => 1,
                'description' => $description,
                'raw_marketplace_data' => $marketplaceData,
            ]);
        } else {
            $sku = 'OZEL-' . preg_replace('/[^A-Za-z0-9]/', '', $quote->quote_no) . '-' . strtoupper(Str::random(4));
            $slug = Str::slug("ozel-siparis-{$quote->quote_no}-" . Str::lower(Str::random(5)));

            $product = Product::create([
                'sku' => $sku,
                'name' => $productName,
                'slug' => $slug,
                'price' => $request->price,
                'stock' => 1,
                'active' => 1,
                'free_shipping' => 1,
                'description' => $description,
                'brand_name' => 'umutMed Özel Sipariş',
                'category_name' => 'Özel Teklif Siparişleri',
                'raw_marketplace_data' => $marketplaceData,
            ]);

            // Attach product images from quote items
            $addedImages = [];
            foreach ($quote->items as $idx => $item) {
                if ($item->product_image && !in_array($item->product_image, $addedImages)) {
                    $product->productImages()->create([
                        'url' => $item->product_image,
                        'path' => $item->product_image,
                        'is_primary' => count($addedImages) === 0 ? 1 : 0,
                    ]);
                    $addedImages[] = $item->product_image;
                }
            }
        }

        $paymentLink = route('product.show', ['product' => $product->slug]);

        $quote->created_product_id = $product->id;
        $quote->custom_payment_link = $paymentLink;
        $quote->offered_total = $request->price;
        $quote->status = 'converted';
        $quote->save();

        // Send notification email to customer
        if ($quote->customer_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($quote->customer_email)->queue(new \App\Mail\QuoteStatusUpdatedMail($quote));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Quote converted email error: ' . $e->getMessage());
            }
        }

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
