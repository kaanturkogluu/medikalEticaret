<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $marketplaces = Channel::with('credential')->get();
        return view('admin.marketplaces', compact('marketplaces'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Channel $channel): View
    {
        $channel->load('credential');
        return view('admin.marketplaces.edit', compact('channel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Channel $channel): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:channels,slug,' . $channel->id,
            'active' => 'required|boolean',
            'color' => 'nullable|string|max:7', // Hex color
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'supplier_id' => 'nullable|string',
        ]);

        $channel->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'active' => $validated['active'],
            'color' => $validated['color'] ?? '#f8fafc',
        ]);

        if ($channel->credential) {
            $channel->credential->update([
                'api_key' => $validated['api_key'],
                'api_secret' => $validated['api_secret'],
                'supplier_id' => $validated['supplier_id'],
            ]);
        } else {
            $channel->credential()->create([
                'api_key' => $validated['api_key'],
                'api_secret' => $validated['api_secret'],
                'supplier_id' => $validated['supplier_id'],
            ]);
        }

        return redirect()->route('admin.marketplaces')
            ->with('success', 'Marketplace settings updated successfully.');
    }

    /**
     * Test the connection to the marketplace.
     */
    public function test(\App\Models\Channel $channel, \App\Integrations\Marketplace\MarketplaceManager $manager)
    {
        try {
            $adapter = $manager->getAdapter($channel);
            $success = $adapter->testConnection();

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Bağlantı Başarılı!' : 'Bağlantı Hatası: Lütfen API bilgilerini kontrol edin.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function n11Orders(\App\Integrations\Marketplace\MarketplaceManager $manager)
    {
        try {
            $channel = Channel::where('slug', 'n11')->firstOrFail();
            $adapter = $manager->getAdapter($channel);
            $orders = $adapter->fetchOrders();

            return view('admin.n11_orders_test', compact('orders'));
        } catch (\Exception $e) {
            return "Hata: " . $e->getMessage();
        }
    }
}
