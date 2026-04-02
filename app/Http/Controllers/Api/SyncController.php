<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class SyncController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function syncOrders(): JsonResponse
    {
        $this->orderService->fetchAllChannelOrders();

        return response()->json([
            'message' => 'Orders fetched and synced successfully.'
        ]);
    }
}
