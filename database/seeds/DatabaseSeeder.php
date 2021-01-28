<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // to create default admin user
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'name' => 'iamadmin',
            'email' => 'iam@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('1337456789')
        ]);
    }
}
