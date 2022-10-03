<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->insert([
            [
                'name' => 'South Korea',
                'slug' => 'south-korea',
                'status' => 'active',
            ],
            [
                'name' => 'China',
                'slug' => 'china',
                'status' => 'active',
            ],
            [
                'name' => 'Hong Kong',
                'slug' => 'hongkong',
                'status' => 'active',
            ],
            [
                'name' => 'India',
                'slug' => 'india',
                'status' => 'active',
            ],
            [
                'name' => 'Philippines',
                'slug' => 'philippines',
                'status' => 'active',
            ],
            [
                'name' => 'Japan',
                'slug' => 'japan',
                'status' => 'active',
            ],
            [
                'name' => 'Taiwan',
                'slug' => 'taiwan',
                'status' => 'active',
            ],
            [
                'name' => 'Thailand',
                'slug' => 'thailand',
                'status' => 'active',
            ],
            [
                'name' => 'Indonesia',
                'slug' => 'indonesia',
                'status' => 'active',
            ],
            [
                'name' => 'Malaysia',
                'slug' => 'malaysia',
                'status' => 'active',
            ],
            [
                'name' => 'Iran',
                'slug' => 'iran',
                'status' => 'active',
            ],
            [
                'name' => 'Turkey',
                'slug' => 'turkey',
                'status' => 'active',
            ],
        ]);
    }
}
