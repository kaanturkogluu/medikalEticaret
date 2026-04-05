<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class RandomizeProductViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:randomize-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm ürünlere 0-1000 arası rastgele görüntülenme değeri atar.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::all();
        
        $this->info('Ürün görüntülenme değerleri randomize ediliyor...');
        $bar = $this->output->createProgressBar($products->count());

        $bar->start();

        foreach ($products as $product) {
            $product->update([
                'views' => rand(0, 1000)
            ]);
            $bar->advance();
        }

        $bar->finish();
        
        $this->newLine();
        $this->info('Tüm ürünlerin görüntülenme değerleri başarıyla güncellendi.');
    }
}
