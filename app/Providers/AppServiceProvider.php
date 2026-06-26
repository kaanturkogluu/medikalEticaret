<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Carbon\Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');
        
        \Illuminate\Pagination\Paginator::useTailwind();

        view()->composer(['layouts.app', 'home'], function ($view) {
            // Sadece leaf (alt kategorisi olmayan) kategorilerden, aktif & stoklu ürünü olanları getir
            $categories = \App\Models\Category::where('active', true)
                ->whereDoesntHave('children')
                ->whereHas('products', function ($q) {
                    $q->where('active', true)->where('stock', '>', 0);
                })
                ->withCount(['products' => function ($q) {
                    $q->where('active', true)->where('stock', '>', 0);
                }])
                ->with('parent')
                ->orderBy('name')
                ->get();

            $navbarCategories = \App\Models\Category::where('is_navbar', true)
                ->where('active', true)
                ->orderBy('row_order')
                ->get();

            $view->with(compact('categories', 'navbarCategories'));
        });
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Mail\Events\MessageSent::class, function (\Illuminate\Mail\Events\MessageSent $event) {
            try {
                $message = $event->message;
                $subject = $message->getSubject();
                $toAddresses = [];
                foreach ($message->getTo() as $address) {
                    $toAddresses[] = $address->getAddress();
                }
                
                $type = 'Diğer';
                $lowerSubject = mb_strtolower($subject);
                if (str_contains($lowerSubject, 'hoşgeldin') || str_contains($lowerSubject, 'kayıt') || str_contains($lowerSubject, 'doğrulama')) {
                    $type = 'Hoşgeldin';
                } elseif (str_contains($lowerSubject, 'iptal')) {
                    $type = 'Sipariş İptal';
                } elseif (str_contains($lowerSubject, 'kargo') || str_contains($lowerSubject, 'gönderildi')) {
                    $type = 'Kargo';
                } elseif (str_contains($lowerSubject, 'sipariş')) {
                    $type = 'Sipariş';
                }

                \App\Models\EmailLog::create([
                    'to_email' => implode(', ', $toAddresses),
                    'subject' => $subject,
                    'type' => $type,
                    'body' => $message->getHtmlBody() ?: $message->getTextBody(),
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Email Log Error: ' . $e->getMessage());
            }
        });
    }
}
