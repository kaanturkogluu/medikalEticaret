<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('coupons:clean-expired', function () {
    $deleted = \App\Models\Coupon::where('is_used', false)
        ->whereNotNull('expires_at')
        ->where('expires_at', '<', now())
        ->delete();
    $this->info("Deleted {$deleted} expired coupons.");
})->purpose('Clean up expired and unused coupons')->daily();
