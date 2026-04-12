<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Province;
use App\Models\District;
use App\Models\Neighborhood;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import provinces, districts and neighborhoods from JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path('turkiye-il-ilce-mahalle-koy.json');

        if (!File::exists($filePath)) {
            $this->error("JSON file not found at: {$filePath}");
            return;
        }

        $this->info("Reading JSON file...");
        $json = File::get($filePath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON format: " . json_last_error_msg());
            return;
        }

        $this->info("Importing data...");

        DB::beginTransaction();
        try {
            foreach ($data as $provinceName => $districts) {
                if (empty($provinceName)) continue;
                
                $this->comment("Importing province: {$provinceName}");
                
                $province = Province::firstOrCreate(['name' => $provinceName]);

                foreach ($districts as $districtName => $neighborhoods) {
                    if (empty($districtName)) continue;

                    $district = District::firstOrCreate([
                        'province_id' => $province->id,
                        'name' => $districtName
                    ]);

                    $neighborhoodData = [];
                    foreach ($neighborhoods as $neighborhoodName) {
                        if (empty($neighborhoodName)) continue;
                        
                        $neighborhoodData[] = [
                            'district_id' => $district->id,
                            'name' => $neighborhoodName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if (!empty($neighborhoodData)) {
                        // Bulk insert for speed
                        Neighborhood::insert($neighborhoodData);
                    }
                }
            }

            DB::commit();
            $this->info("Import completed successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("An error occurred during import: " . $e->getMessage());
        }
    }
}
