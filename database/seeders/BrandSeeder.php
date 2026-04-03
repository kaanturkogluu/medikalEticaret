<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Brand::truncate();

        $data = array (
  0 => 
  array (
    'id' => 1,
    'name' => 'Canped',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'Galena',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'MEDİKAL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  3 => 
  array (
    'id' => 4,
    'name' => 'PROSAFE',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  4 => 
  array (
    'id' => 5,
    'name' => 'Umay Med',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  5 => 
  array (
    'id' => 6,
    'name' => 'GOLFİ',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  6 => 
  array (
    'id' => 7,
    'name' => 'Freely',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  7 => 
  array (
    'id' => 8,
    'name' => 'FİZYOPOL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  8 => 
  array (
    'id' => 9,
    'name' => 'ÇİZGİ MEDİKAL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  9 => 
  array (
    'id' => 10,
    'name' => 'giggles',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:06.000000Z',
    'updated_at' => '2026-04-03T17:06:06.000000Z',
  ),
  10 => 
  array (
    'id' => 11,
    'name' => 'Comfort Plus',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  11 => 
  array (
    'id' => 12,
    'name' => 'ES MEDİKAL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  12 => 
  array (
    'id' => 13,
    'name' => 'jetty',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  13 => 
  array (
    'id' => 14,
    'name' => 'MediAnatolia',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  14 => 
  array (
    'id' => 15,
    'name' => 'Masmel',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  15 => 
  array (
    'id' => 16,
    'name' => 'Bestmed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  16 => 
  array (
    'id' => 17,
    'name' => 'ALBEDO',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  17 => 
  array (
    'id' => 18,
    'name' => 'MedikalSağlık',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  18 => 
  array (
    'id' => 19,
    'name' => 'Ayset',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  19 => 
  array (
    'id' => 20,
    'name' => 'Soulfix',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  20 => 
  array (
    'id' => 21,
    'name' => 'ESM',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  21 => 
  array (
    'id' => 22,
    'name' => 'can medikal',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  22 => 
  array (
    'id' => 23,
    'name' => 'AREMED',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  23 => 
  array (
    'id' => 24,
    'name' => 'REVOLINE EXCLUSIVE',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  24 => 
  array (
    'id' => 25,
    'name' => 'Smart',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:07.000000Z',
    'updated_at' => '2026-04-03T17:06:07.000000Z',
  ),
  25 => 
  array (
    'id' => 26,
    'name' => 'RÜSCH',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:08.000000Z',
    'updated_at' => '2026-04-03T17:06:08.000000Z',
  ),
  26 => 
  array (
    'id' => 27,
    'name' => 'Rodrigo',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:08.000000Z',
    'updated_at' => '2026-04-03T17:06:08.000000Z',
  ),
  27 => 
  array (
    'id' => 28,
    'name' => 'Marka',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:08.000000Z',
    'updated_at' => '2026-04-03T17:06:08.000000Z',
  ),
  28 => 
  array (
    'id' => 29,
    'name' => 'sema karakoç',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:08.000000Z',
    'updated_at' => '2026-04-03T17:06:08.000000Z',
  ),
  29 => 
  array (
    'id' => 30,
    'name' => 'EDALKILIÇ',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  30 => 
  array (
    'id' => 31,
    'name' => 'Yılmaz Fide',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  31 => 
  array (
    'id' => 32,
    'name' => 'Mavipazar',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  32 => 
  array (
    'id' => 33,
    'name' => 'umutmed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  33 => 
  array (
    'id' => 34,
    'name' => 'Csr',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  34 => 
  array (
    'id' => 35,
    'name' => 'WOLLEX',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  35 => 
  array (
    'id' => 36,
    'name' => 'Respirox',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  36 => 
  array (
    'id' => 37,
    'name' => 'VERA MEDİKAL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  37 => 
  array (
    'id' => 38,
    'name' => 'CANPET',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:09.000000Z',
    'updated_at' => '2026-04-03T17:06:09.000000Z',
  ),
  38 => 
  array (
    'id' => 39,
    'name' => 'feifei',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:37.000000Z',
    'updated_at' => '2026-04-03T17:06:37.000000Z',
  ),
  39 => 
  array (
    'id' => 40,
    'name' => 'Canbebe',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:37.000000Z',
    'updated_at' => '2026-04-03T17:06:37.000000Z',
  ),
  40 => 
  array (
    'id' => 41,
    'name' => 'Nextpage',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:37.000000Z',
    'updated_at' => '2026-04-03T17:06:37.000000Z',
  ),
  41 => 
  array (
    'id' => 42,
    'name' => 'Moly',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:37.000000Z',
    'updated_at' => '2026-04-03T17:06:37.000000Z',
  ),
  42 => 
  array (
    'id' => 43,
    'name' => 'BAŞARI MEDİKAL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:38.000000Z',
    'updated_at' => '2026-04-03T17:06:38.000000Z',
  ),
  43 => 
  array (
    'id' => 44,
    'name' => 'WALESSAN',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:38.000000Z',
    'updated_at' => '2026-04-03T17:06:38.000000Z',
  ),
  44 => 
  array (
    'id' => 45,
    'name' => 'ABS',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:38.000000Z',
    'updated_at' => '2026-04-03T17:06:38.000000Z',
  ),
  45 => 
  array (
    'id' => 46,
    'name' => 'WE CHEM',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:38.000000Z',
    'updated_at' => '2026-04-03T17:06:38.000000Z',
  ),
  46 => 
  array (
    'id' => 47,
    'name' => 'VZN',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:39.000000Z',
    'updated_at' => '2026-04-03T17:06:39.000000Z',
  ),
  47 => 
  array (
    'id' => 48,
    'name' => 'Güneysan',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:39.000000Z',
    'updated_at' => '2026-04-03T17:06:39.000000Z',
  ),
  48 => 
  array (
    'id' => 49,
    'name' => 'seral',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:39.000000Z',
    'updated_at' => '2026-04-03T17:06:39.000000Z',
  ),
  49 => 
  array (
    'id' => 50,
    'name' => 'Dr.Comfort',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:40.000000Z',
    'updated_at' => '2026-04-03T17:06:40.000000Z',
  ),
  50 => 
  array (
    'id' => 51,
    'name' => 'LORİNA',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:06:59.000000Z',
    'updated_at' => '2026-04-03T17:06:59.000000Z',
  ),
  51 => 
  array (
    'id' => 52,
    'name' => 'JENDER',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:00.000000Z',
    'updated_at' => '2026-04-03T17:07:00.000000Z',
  ),
  52 => 
  array (
    'id' => 53,
    'name' => 'Bıçakçılar',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:01.000000Z',
    'updated_at' => '2026-04-03T17:07:01.000000Z',
  ),
  53 => 
  array (
    'id' => 54,
    'name' => 'SistemTemizlik',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:02.000000Z',
    'updated_at' => '2026-04-03T17:07:02.000000Z',
  ),
  54 => 
  array (
    'id' => 55,
    'name' => 'Ceymop',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:02.000000Z',
    'updated_at' => '2026-04-03T17:07:02.000000Z',
  ),
  55 => 
  array (
    'id' => 56,
    'name' => 'Ceyhanlar',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:02.000000Z',
    'updated_at' => '2026-04-03T17:07:02.000000Z',
  ),
  56 => 
  array (
    'id' => 57,
    'name' => 'lummed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:02.000000Z',
    'updated_at' => '2026-04-03T17:07:02.000000Z',
  ),
  57 => 
  array (
    'id' => 58,
    'name' => 'TELKAR',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:02.000000Z',
    'updated_at' => '2026-04-03T17:07:02.000000Z',
  ),
  58 => 
  array (
    'id' => 59,
    'name' => 'Afacan Plastik',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:03.000000Z',
    'updated_at' => '2026-04-03T17:07:03.000000Z',
  ),
  59 => 
  array (
    'id' => 60,
    'name' => 'Ayrobi',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:03.000000Z',
    'updated_at' => '2026-04-03T17:07:03.000000Z',
  ),
  60 => 
  array (
    'id' => 61,
    'name' => 'arıtürk',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:03.000000Z',
    'updated_at' => '2026-04-03T17:07:03.000000Z',
  ),
  61 => 
  array (
    'id' => 62,
    'name' => 'Aqua',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:03.000000Z',
    'updated_at' => '2026-04-03T17:07:03.000000Z',
  ),
  62 => 
  array (
    'id' => 63,
    'name' => 'Tarko',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:03.000000Z',
    'updated_at' => '2026-04-03T17:07:03.000000Z',
  ),
  63 => 
  array (
    'id' => 64,
    'name' => 'Freshlife',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:03.000000Z',
    'updated_at' => '2026-04-03T17:07:03.000000Z',
  ),
  64 => 
  array (
    'id' => 65,
    'name' => 'Oriflame',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:03.000000Z',
    'updated_at' => '2026-04-03T17:07:03.000000Z',
  ),
  65 => 
  array (
    'id' => 66,
    'name' => 'TURMED',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:03.000000Z',
    'updated_at' => '2026-04-03T17:07:03.000000Z',
  ),
  66 => 
  array (
    'id' => 67,
    'name' => 'WhitWheels',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:04.000000Z',
    'updated_at' => '2026-04-03T17:07:04.000000Z',
  ),
  67 => 
  array (
    'id' => 68,
    'name' => 'adelsan',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:04.000000Z',
    'updated_at' => '2026-04-03T17:07:04.000000Z',
  ),
  68 => 
  array (
    'id' => 69,
    'name' => 'Masmel Sağlık',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:04.000000Z',
    'updated_at' => '2026-04-03T17:07:04.000000Z',
  ),
  69 => 
  array (
    'id' => 70,
    'name' => 'innecioğlu',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:04.000000Z',
    'updated_at' => '2026-04-03T17:07:04.000000Z',
  ),
  70 => 
  array (
    'id' => 71,
    'name' => 'NEXTMED',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:04.000000Z',
    'updated_at' => '2026-04-03T17:07:04.000000Z',
  ),
  71 => 
  array (
    'id' => 72,
    'name' => 'Rooly',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:05.000000Z',
    'updated_at' => '2026-04-03T17:07:05.000000Z',
  ),
  72 => 
  array (
    'id' => 73,
    'name' => 'Philips',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:05.000000Z',
    'updated_at' => '2026-04-03T17:07:05.000000Z',
  ),
  73 => 
  array (
    'id' => 74,
    'name' => 'Pulsemed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:05.000000Z',
    'updated_at' => '2026-04-03T17:07:05.000000Z',
  ),
  74 => 
  array (
    'id' => 75,
    'name' => 'WİNDELHOSEN',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:05.000000Z',
    'updated_at' => '2026-04-03T17:07:05.000000Z',
  ),
  75 => 
  array (
    'id' => 76,
    'name' => 'Onteks',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:05.000000Z',
    'updated_at' => '2026-04-03T17:07:05.000000Z',
  ),
  76 => 
  array (
    'id' => 77,
    'name' => 'gncshop',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:06.000000Z',
    'updated_at' => '2026-04-03T17:07:06.000000Z',
  ),
  77 => 
  array (
    'id' => 78,
    'name' => 'yeni',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:06.000000Z',
    'updated_at' => '2026-04-03T17:07:06.000000Z',
  ),
  78 => 
  array (
    'id' => 79,
    'name' => 'FUHASSAN',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:06.000000Z',
    'updated_at' => '2026-04-03T17:07:06.000000Z',
  ),
  79 => 
  array (
    'id' => 80,
    'name' => 'Covidien',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:07.000000Z',
    'updated_at' => '2026-04-03T17:07:07.000000Z',
  ),
  80 => 
  array (
    'id' => 81,
    'name' => 'ÖZNUR',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:08.000000Z',
    'updated_at' => '2026-04-03T17:07:08.000000Z',
  ),
  81 => 
  array (
    'id' => 82,
    'name' => '3M',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:09.000000Z',
    'updated_at' => '2026-04-03T17:07:09.000000Z',
  ),
  82 => 
  array (
    'id' => 83,
    'name' => 'Optima',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:09.000000Z',
    'updated_at' => '2026-04-03T17:07:09.000000Z',
  ),
  83 => 
  array (
    'id' => 84,
    'name' => 'İpek',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:09.000000Z',
    'updated_at' => '2026-04-03T17:07:09.000000Z',
  ),
  84 => 
  array (
    'id' => 85,
    'name' => 'Omron',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:09.000000Z',
    'updated_at' => '2026-04-03T17:07:09.000000Z',
  ),
  85 => 
  array (
    'id' => 86,
    'name' => 'Halk',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:10.000000Z',
    'updated_at' => '2026-04-03T17:07:10.000000Z',
  ),
  86 => 
  array (
    'id' => 87,
    'name' => 'jazz',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:11.000000Z',
    'updated_at' => '2026-04-03T17:07:11.000000Z',
  ),
  87 => 
  array (
    'id' => 88,
    'name' => 'Nimo',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:11.000000Z',
    'updated_at' => '2026-04-03T17:07:11.000000Z',
  ),
  88 => 
  array (
    'id' => 89,
    'name' => 'Nimomed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:11.000000Z',
    'updated_at' => '2026-04-03T17:07:11.000000Z',
  ),
  89 => 
  array (
    'id' => 90,
    'name' => 'Wabi Sabi Trjflo',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:12.000000Z',
    'updated_at' => '2026-04-03T17:07:12.000000Z',
  ),
  90 => 
  array (
    'id' => 91,
    'name' => 'STEPS',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:12.000000Z',
    'updated_at' => '2026-04-03T17:07:12.000000Z',
  ),
  91 => 
  array (
    'id' => 92,
    'name' => 'İlmeks',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:12.000000Z',
    'updated_at' => '2026-04-03T17:07:12.000000Z',
  ),
  92 => 
  array (
    'id' => 93,
    'name' => 'Easyflow',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:12.000000Z',
    'updated_at' => '2026-04-03T17:07:12.000000Z',
  ),
  93 => 
  array (
    'id' => 94,
    'name' => 'Hobbi',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:12.000000Z',
    'updated_at' => '2026-04-03T17:07:12.000000Z',
  ),
  94 => 
  array (
    'id' => 95,
    'name' => 'UPS',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:12.000000Z',
    'updated_at' => '2026-04-03T17:07:12.000000Z',
  ),
  95 => 
  array (
    'id' => 96,
    'name' => 'Has-Pet',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:13.000000Z',
    'updated_at' => '2026-04-03T17:07:13.000000Z',
  ),
  96 => 
  array (
    'id' => 97,
    'name' => 'Parti Feneri',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:24.000000Z',
    'updated_at' => '2026-04-03T17:07:24.000000Z',
  ),
  97 => 
  array (
    'id' => 98,
    'name' => 'EMIR',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:24.000000Z',
    'updated_at' => '2026-04-03T17:07:24.000000Z',
  ),
  98 => 
  array (
    'id' => 99,
    'name' => 'Selpak Professional',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:25.000000Z',
    'updated_at' => '2026-04-03T17:07:25.000000Z',
  ),
  99 => 
  array (
    'id' => 100,
    'name' => 'nebtime',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:25.000000Z',
    'updated_at' => '2026-04-03T17:07:25.000000Z',
  ),
  100 => 
  array (
    'id' => 101,
    'name' => 'Loobex',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:25.000000Z',
    'updated_at' => '2026-04-03T17:07:25.000000Z',
  ),
  101 => 
  array (
    'id' => 102,
    'name' => 'woodhub',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:25.000000Z',
    'updated_at' => '2026-04-03T17:07:25.000000Z',
  ),
  102 => 
  array (
    'id' => 103,
    'name' => 'Erka',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:25.000000Z',
    'updated_at' => '2026-04-03T17:07:25.000000Z',
  ),
  103 => 
  array (
    'id' => 104,
    'name' => 'kocmed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:27.000000Z',
    'updated_at' => '2026-04-03T17:07:27.000000Z',
  ),
  104 => 
  array (
    'id' => 105,
    'name' => 'ACTUAL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:27.000000Z',
    'updated_at' => '2026-04-03T17:07:27.000000Z',
  ),
  105 => 
  array (
    'id' => 106,
    'name' => 'Homstar',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:28.000000Z',
    'updated_at' => '2026-04-03T17:07:28.000000Z',
  ),
  106 => 
  array (
    'id' => 107,
    'name' => 'ipek medikal',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:28.000000Z',
    'updated_at' => '2026-04-03T17:07:28.000000Z',
  ),
  107 => 
  array (
    'id' => 108,
    'name' => 'Hünkar',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:28.000000Z',
    'updated_at' => '2026-04-03T17:07:28.000000Z',
  ),
  108 => 
  array (
    'id' => 109,
    'name' => 'fisher paykel',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:28.000000Z',
    'updated_at' => '2026-04-03T17:07:28.000000Z',
  ),
  109 => 
  array (
    'id' => 110,
    'name' => 'Hikoneb',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:28.000000Z',
    'updated_at' => '2026-04-03T17:07:28.000000Z',
  ),
  110 => 
  array (
    'id' => 111,
    'name' => 'WOLEX',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:30.000000Z',
    'updated_at' => '2026-04-03T17:07:30.000000Z',
  ),
  111 => 
  array (
    'id' => 112,
    'name' => 'Samsung',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:30.000000Z',
    'updated_at' => '2026-04-03T17:07:30.000000Z',
  ),
  112 => 
  array (
    'id' => 113,
    'name' => 'deliay',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:31.000000Z',
    'updated_at' => '2026-04-03T17:07:31.000000Z',
  ),
  113 => 
  array (
    'id' => 114,
    'name' => 'ZeyMer',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:32.000000Z',
    'updated_at' => '2026-04-03T17:07:32.000000Z',
  ),
  114 => 
  array (
    'id' => 115,
    'name' => 'epicsan',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:32.000000Z',
    'updated_at' => '2026-04-03T17:07:32.000000Z',
  ),
  115 => 
  array (
    'id' => 116,
    'name' => 'Libero',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:32.000000Z',
    'updated_at' => '2026-04-03T17:07:32.000000Z',
  ),
  116 => 
  array (
    'id' => 117,
    'name' => 'BESTWAY',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:33.000000Z',
    'updated_at' => '2026-04-03T17:07:33.000000Z',
  ),
  117 => 
  array (
    'id' => 118,
    'name' => 'MEDWELT',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:33.000000Z',
    'updated_at' => '2026-04-03T17:07:33.000000Z',
  ),
  118 => 
  array (
    'id' => 119,
    'name' => 'Umut',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:33.000000Z',
    'updated_at' => '2026-04-03T17:07:33.000000Z',
  ),
  119 => 
  array (
    'id' => 120,
    'name' => 'İthal',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:33.000000Z',
    'updated_at' => '2026-04-03T17:07:33.000000Z',
  ),
  120 => 
  array (
    'id' => 121,
    'name' => 'Witra',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:34.000000Z',
    'updated_at' => '2026-04-03T17:07:34.000000Z',
  ),
  121 => 
  array (
    'id' => 122,
    'name' => 'Teno',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:34.000000Z',
    'updated_at' => '2026-04-03T17:07:34.000000Z',
  ),
  122 => 
  array (
    'id' => 123,
    'name' => 'KSM',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:34.000000Z',
    'updated_at' => '2026-04-03T17:07:34.000000Z',
  ),
  123 => 
  array (
    'id' => 124,
    'name' => 'SDM Medikal',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:34.000000Z',
    'updated_at' => '2026-04-03T17:07:34.000000Z',
  ),
  124 => 
  array (
    'id' => 125,
    'name' => 'HCT MEDİKAL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:34.000000Z',
    'updated_at' => '2026-04-03T17:07:34.000000Z',
  ),
  125 => 
  array (
    'id' => 126,
    'name' => 'Goodcare',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:34.000000Z',
    'updated_at' => '2026-04-03T17:07:34.000000Z',
  ),
  126 => 
  array (
    'id' => 127,
    'name' => 'Resmed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:36.000000Z',
    'updated_at' => '2026-04-03T17:07:36.000000Z',
  ),
  127 => 
  array (
    'id' => 128,
    'name' => 'GEZ OXYNEW',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:36.000000Z',
    'updated_at' => '2026-04-03T17:07:36.000000Z',
  ),
  128 => 
  array (
    'id' => 129,
    'name' => 'BRP',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:36.000000Z',
    'updated_at' => '2026-04-03T17:07:36.000000Z',
  ),
  129 => 
  array (
    'id' => 130,
    'name' => 'G-LIFE',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:37.000000Z',
    'updated_at' => '2026-04-03T17:07:37.000000Z',
  ),
  130 => 
  array (
    'id' => 131,
    'name' => 'ARTİMED',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:37.000000Z',
    'updated_at' => '2026-04-03T17:07:37.000000Z',
  ),
  131 => 
  array (
    'id' => 132,
    'name' => 'TEKERLEKLi iSKEMLE',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:39.000000Z',
    'updated_at' => '2026-04-03T17:07:39.000000Z',
  ),
  132 => 
  array (
    'id' => 133,
    'name' => 'POYLİN',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:39.000000Z',
    'updated_at' => '2026-04-03T17:07:39.000000Z',
  ),
  133 => 
  array (
    'id' => 134,
    'name' => 'MEDİKALÖZTÜRK',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:39.000000Z',
    'updated_at' => '2026-04-03T17:07:39.000000Z',
  ),
  134 => 
  array (
    'id' => 135,
    'name' => 'tpcw',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:39.000000Z',
    'updated_at' => '2026-04-03T17:07:39.000000Z',
  ),
  135 => 
  array (
    'id' => 136,
    'name' => 'SAĞLIKDENT MEDİKAL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:40.000000Z',
    'updated_at' => '2026-04-03T17:07:40.000000Z',
  ),
  136 => 
  array (
    'id' => 137,
    'name' => 'SOUND SLEEP',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:40.000000Z',
    'updated_at' => '2026-04-03T17:07:40.000000Z',
  ),
  137 => 
  array (
    'id' => 138,
    'name' => 'SmileStore',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:40.000000Z',
    'updated_at' => '2026-04-03T17:07:40.000000Z',
  ),
  138 => 
  array (
    'id' => 139,
    'name' => 'ORECARE',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:42.000000Z',
    'updated_at' => '2026-04-03T17:07:42.000000Z',
  ),
  139 => 
  array (
    'id' => 140,
    'name' => 'omer',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:44.000000Z',
    'updated_at' => '2026-04-03T17:07:44.000000Z',
  ),
  140 => 
  array (
    'id' => 141,
    'name' => 'Gez',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:44.000000Z',
    'updated_at' => '2026-04-03T17:07:44.000000Z',
  ),
  141 => 
  array (
    'id' => 142,
    'name' => 'MİLASMED',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:44.000000Z',
    'updated_at' => '2026-04-03T17:07:44.000000Z',
  ),
  142 => 
  array (
    'id' => 143,
    'name' => 'Beybi',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:44.000000Z',
    'updated_at' => '2026-04-03T17:07:44.000000Z',
  ),
  143 => 
  array (
    'id' => 144,
    'name' => 'Çaykur',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:45.000000Z',
    'updated_at' => '2026-04-03T17:07:45.000000Z',
  ),
  144 => 
  array (
    'id' => 145,
    'name' => 'Bingo',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:45.000000Z',
    'updated_at' => '2026-04-03T17:07:45.000000Z',
  ),
  145 => 
  array (
    'id' => 146,
    'name' => 'LOCO',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:47.000000Z',
    'updated_at' => '2026-04-03T17:07:47.000000Z',
  ),
  146 => 
  array (
    'id' => 147,
    'name' => 'Neutrogena',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:47.000000Z',
    'updated_at' => '2026-04-03T17:07:47.000000Z',
  ),
  147 => 
  array (
    'id' => 148,
    'name' => 'Airiz',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:47.000000Z',
    'updated_at' => '2026-04-03T17:07:47.000000Z',
  ),
  148 => 
  array (
    'id' => 149,
    'name' => 'Tiens',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:47.000000Z',
    'updated_at' => '2026-04-03T17:07:47.000000Z',
  ),
  149 => 
  array (
    'id' => 150,
    'name' => 'Dodo',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:54.000000Z',
    'updated_at' => '2026-04-03T17:07:54.000000Z',
  ),
  150 => 
  array (
    'id' => 151,
    'name' => 'FEELLİFE',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:54.000000Z',
    'updated_at' => '2026-04-03T17:07:54.000000Z',
  ),
  151 => 
  array (
    'id' => 152,
    'name' => 'Nikadu',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:54.000000Z',
    'updated_at' => '2026-04-03T17:07:54.000000Z',
  ),
  152 => 
  array (
    'id' => 153,
    'name' => 'PlusMed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:55.000000Z',
    'updated_at' => '2026-04-03T17:07:55.000000Z',
  ),
  153 => 
  array (
    'id' => 154,
    'name' => 'Littmann',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:55.000000Z',
    'updated_at' => '2026-04-03T17:07:55.000000Z',
  ),
  154 => 
  array (
    'id' => 155,
    'name' => 'MEDİKALCİM',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:55.000000Z',
    'updated_at' => '2026-04-03T17:07:55.000000Z',
  ),
  155 => 
  array (
    'id' => 156,
    'name' => 'Boomerang',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:07:55.000000Z',
    'updated_at' => '2026-04-03T17:07:55.000000Z',
  ),
  156 => 
  array (
    'id' => 157,
    'name' => 'Astra Market',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:06.000000Z',
    'updated_at' => '2026-04-03T17:08:06.000000Z',
  ),
  157 => 
  array (
    'id' => 158,
    'name' => 'CVS',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:06.000000Z',
    'updated_at' => '2026-04-03T17:08:06.000000Z',
  ),
  158 => 
  array (
    'id' => 159,
    'name' => 'ÇİŞESON',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:06.000000Z',
    'updated_at' => '2026-04-03T17:08:06.000000Z',
  ),
  159 => 
  array (
    'id' => 160,
    'name' => 'Sleepy',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:06.000000Z',
    'updated_at' => '2026-04-03T17:08:06.000000Z',
  ),
  160 => 
  array (
    'id' => 161,
    'name' => 'mediflex',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:06.000000Z',
    'updated_at' => '2026-04-03T17:08:06.000000Z',
  ),
  161 => 
  array (
    'id' => 162,
    'name' => 'Belove',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:06.000000Z',
    'updated_at' => '2026-04-03T17:08:06.000000Z',
  ),
  162 => 
  array (
    'id' => 163,
    'name' => 'kalant',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:06.000000Z',
    'updated_at' => '2026-04-03T17:08:06.000000Z',
  ),
  163 => 
  array (
    'id' => 164,
    'name' => 'Anna',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:07.000000Z',
    'updated_at' => '2026-04-03T17:08:07.000000Z',
  ),
  164 => 
  array (
    'id' => 165,
    'name' => 'ENDOSTALL',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:07.000000Z',
    'updated_at' => '2026-04-03T17:08:07.000000Z',
  ),
  165 => 
  array (
    'id' => 166,
    'name' => 'Türkmedical',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:07.000000Z',
    'updated_at' => '2026-04-03T17:08:07.000000Z',
  ),
  166 => 
  array (
    'id' => 167,
    'name' => 'Puremed',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:07.000000Z',
    'updated_at' => '2026-04-03T17:08:07.000000Z',
  ),
  167 => 
  array (
    'id' => 168,
    'name' => 'Maylo',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:07.000000Z',
    'updated_at' => '2026-04-03T17:08:07.000000Z',
  ),
  168 => 
  array (
    'id' => 169,
    'name' => 'Coloplast',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:07.000000Z',
    'updated_at' => '2026-04-03T17:08:07.000000Z',
  ),
  169 => 
  array (
    'id' => 170,
    'name' => 'Sanitabant',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:07.000000Z',
    'updated_at' => '2026-04-03T17:08:07.000000Z',
  ),
  170 => 
  array (
    'id' => 171,
    'name' => 'LifeTime',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:07.000000Z',
    'updated_at' => '2026-04-03T17:08:07.000000Z',
  ),
  171 => 
  array (
    'id' => 172,
    'name' => 'Gluco Dr',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:22.000000Z',
    'updated_at' => '2026-04-03T17:08:22.000000Z',
  ),
  172 => 
  array (
    'id' => 173,
    'name' => 'VİSOMAT',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:23.000000Z',
    'updated_at' => '2026-04-03T17:08:23.000000Z',
  ),
  173 => 
  array (
    'id' => 174,
    'name' => 'GMT',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:23.000000Z',
    'updated_at' => '2026-04-03T17:08:23.000000Z',
  ),
  174 => 
  array (
    'id' => 175,
    'name' => 'Nordmende',
    'logo' => NULL,
    'active' => 1,
    'created_at' => '2026-04-03T17:08:23.000000Z',
    'updated_at' => '2026-04-03T17:08:23.000000Z',
  ),
);

        foreach ($data as $item) {
            Brand::create($item);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}