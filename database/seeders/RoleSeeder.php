<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'Main Role',
                'slug' => 'main-role',
                'type' => 'cast',
                'status' => 'active',
            ],
            [
                'name' => 'Support Role',
                'slug' => 'support-role',
                'type' => 'cast',
                'status' => 'active',
            ],
            [
                'name' => 'Guest Role',
                'slug' => 'guest-role',
                'type' => 'cast',
                'status' => 'active',
            ],
            [
                'name' => 'Director',
                'slug' => 'director',
                'type' => 'crew',
                'status' => 'active',
            ],
            [
                'name' => 'Screenwriter',
                'slug' => 'screenwriter',
                'type' => 'crew',
                'status' => 'active',
            ],
            [
                'name' => 'Cameraman',
                'slug' => 'cameraman',
                'type' => 'crew',
                'status' => 'active',
            ],
        ]);
    }
}
