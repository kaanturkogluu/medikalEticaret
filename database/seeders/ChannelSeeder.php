<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\ChannelCredential;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    public function run(): void
    {
        $marketplaces = [
            [
                'name' => 'Trendyol',
                'slug' => 'trendyol',
                'active' => true,
                'credentials' => [
                    'api_key' => '86P6h0p82v8yVp2Y84J7',
                    'api_secret' => 'X95493JvI7v699U69H7',
                    'supplier_id' => '680904'
                ]
            ],
            [
                'name' => 'Hepsiburada',
                'slug' => 'hepsiburada',
                'active' => false,
                'credentials' => [
                    'api_key' => null,
                    'api_secret' => null,
                    'supplier_id' => null
                ]
            ],
            [
                'name' => 'N11',
                'slug' => 'n11',
                'active' => false,
                'credentials' => [
                    'api_key' => null,
                    'api_secret' => null,
                    'supplier_id' => null
                ]
            ]
        ];

        foreach ($marketplaces as $m) {
            $channel = Channel::updateOrCreate(
                ['slug' => $m['slug']],
                ['name' => $m['name'], 'active' => $m['active']]
            );

            ChannelCredential::updateOrCreate(
                ['channel_id' => $channel->id],
                $m['credentials']
            );
        }
    }
}
