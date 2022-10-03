<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rate_types')->insert([
            [
                'name' => 'Rated G',
                'slug' => 'rated-g',
                'definition' => 'General audiences',
                'detail' => 'All ages admitted.',
                'status' => 'active',
            ],
            [
                'name' => 'Rated PG',
                'slug' => 'rated-pg',
                'definition' => 'Parental guidance suggested',
                'detail' => 'Some material may not be suitable for children.',
                'status' => 'active',
            ],
            [
                'name' => 'Rated PG-13',
                'slug' => 'rated-pg-13',
                'definition' => 'Parents strongly cautioned',
                'detail' => 'Some material may be inappropriate for children under 13',
                'status' => 'active',
            ],
            [
                'name' => 'Rated R',
                'slug' => 'rated-r',
                'definition' => 'Restricted',
                'detail' => 'Under 17 requires accompanying parent or adult guardian',
                'status' => 'active',
            ],
            [
                'name' => 'Rated NC-17',
                'slug' => 'rated-nc-17',
                'definition' => 'Adults Only',
                'detail' => 'No one 17 and under admitted.',
                'status' => 'active',
            ],
        ]);
    }
}
