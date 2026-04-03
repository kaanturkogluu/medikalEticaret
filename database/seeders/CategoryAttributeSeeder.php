<?php

namespace Database\Seeders;

use App\Models\CategoryAttribute;
use Illuminate\Database\Seeder;

class CategoryAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            ['category_id' => 4, 'name' => 'beden',          'is_variant' => true,  'required' => true],
            ['category_id' => 4, 'name' => 'paket ici adet',  'is_variant' => true,  'required' => false],
            ['category_id' => 4, 'name' => 'cinsiyet',        'is_variant' => false, 'required' => false],
            ['category_id' => 5, 'name' => 'beden',           'is_variant' => true,  'required' => true],
            ['category_id' => 5, 'name' => 'emicilik',        'is_variant' => true,  'required' => false],
            ['category_id' => 123, 'name' => 'renk',          'is_variant' => true,  'required' => true],
            ['category_id' => 123, 'name' => 'beden',         'is_variant' => true,  'required' => true],
        ];

        foreach ($definitions as $def) {
            CategoryAttribute::updateOrCreate(
                ['category_id' => $def['category_id'], 'name' => $def['name']],
                ['is_variant' => $def['is_variant'], 'required' => $def['required']]
            );
        }

        $this->command->info('CategoryAttributeSeeder: ' . count($definitions) . ' records seeded.');
    }
}
