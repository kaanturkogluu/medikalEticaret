<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_order_id' => $this->external_order_id ?? $this->id,
            'total_price' => (float) $this->total_price,
            'shipping_price' => (float) $this->shipping_price,
            'discount_amount' => (float) $this->discount_amount,
            'currency' => $this->currency,
            'order_status' => $this->order_status,
            'status_label' => $this->status_label,
            'payment_method' => $this->payment_method,
            'tracking_code' => $this->tracking_code,
            'earned_points' => (int) $this->earned_points,
            'used_points' => (int) $this->used_points,
            'order_date' => $this->order_date,
            'address_info' => $this->address_info,
            'invoice_info' => $this->invoice_info,
            'items' => $this->whenLoaded('items', function() {
                return $this->items->map(fn($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'total_price' => (float) ($item->quantity * $item->price),
                ]);
            }),
        ];
    }
}
