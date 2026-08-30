<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use App\Models\QuoteRequestItem;
use App\Models\Setting;
use App\Mail\QuoteReceivedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        // Generate unique quote number (TK-YYYY-XXXXX)
        $year = date('Y');
        do {
            $quoteNo = 'TK-' . $year . '-' . strtoupper(Str::random(5));
        } while (QuoteRequest::where('quote_no', $quoteNo)->exists());

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

        // Queue notification emails
        if ($quote->customer_email) {
            try {
                Mail::to($quote->customer_email)->queue(new QuoteReceivedMail($quote));
            } catch (\Throwable $e) {
                Log::error('Quote customer email queue error: ' . $e->getMessage());
            }
        }

        // Admin notification email
        $adminEmail = Setting::getValue('site_contact_email', config('mail.from.address'));
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->queue(new QuoteReceivedMail($quote));
            } catch (\Throwable $e) {
                Log::error('Quote admin email queue error: ' . $e->getMessage());
            }
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
     * Display the Quote Success confirmation page.
     */
    public function success($quote_no)
    {
        $quote = QuoteRequest::with('items')->where('quote_no', $quote_no)->firstOrFail();
        return view('quote_success', compact('quote'));
    }

    /**
     * Display the Quote Tracking / Query page (Teklif Sorgulama).
     */
    public function track(Request $request)
    {
        $searchedNo = trim($request->input('quote_no', ''));
        $quote = null;
        $errorMessage = null;

        if ($searchedNo !== '') {
            $quote = QuoteRequest::with('items')->where('quote_no', $searchedNo)->first();
            if (!$quote) {
                // Try case-insensitive lookup or strip spaces
                $cleanNo = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '', $searchedNo));
                $quote = QuoteRequest::with('items')->where('quote_no', $cleanNo)->first();
            }

            if (!$quote) {
                $errorMessage = 'Belirtilen "' . htmlspecialchars($searchedNo) . '" takip numarasına ait teklif talebi bulunamadı. Lütfen takip numaranızı kontrol ediniz.';
            }
        }

        return view('quote_track', compact('quote', 'searchedNo', 'errorMessage'));
    }

    /**
     * Logged-in User Quotes History.
     */
    public function userQuotes()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $quotes = QuoteRequest::with('items')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('customer_email', $user->email);
                if (!empty($user->phone)) {
                    $query->orWhere('customer_phone', $user->phone);
                }
            })
            ->latest()
            ->paginate(10);

        return view('user.quotes', compact('quotes'));
    }
}
