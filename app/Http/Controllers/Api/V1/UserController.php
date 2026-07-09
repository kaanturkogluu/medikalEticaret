<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\UserAddress;
use App\Http\Resources\V1\OrderResource;
use App\Http\Resources\V1\UserAddressResource;

class UserController extends Controller
{
    public function orders(Request $request)
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
            
        return OrderResource::collection($orders);
    }

    public function addresses(Request $request)
    {
        $addresses = UserAddress::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->get();
            
        return UserAddressResource::collection($addresses);
    }
}
