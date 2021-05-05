<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('usage_types')->insert([
            'name' => 'Food',
            'slug' => 'food',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Electric',
            'slug' => 'electric',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Water',
            'slug' => 'water',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Light',
            'slug' => 'light',
            'description' => 'light of building ( not in house )',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Cleaning',
            'slug' => 'cleaning',
            'description' => 'wash motor',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Beauty',
            'slug' => 'beauty',
            'description' => 'hair cut',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Home Tools',
            'slug' => 'home_tool',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Personal Tools',
            'slug' => 'personal_tool',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Maintenace',
            'slug' => 'maintenace',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Home Fee',
            'slug' => 'home_fee',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Hard salary',
            'slug' => 'hard_salary',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Soft salary',
            'slug' => 'soft_salary',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Party',
            'slug' => 'party',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Medicine',
            'slug' => 'medicine',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Loan',
            'slug' => 'loan',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Phone',
            'slug' => 'phone',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Internet',
            'slug' => 'internet',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Clothes',
            'slug' => 'clothes',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Wallet to Bank',
            'slug' => 'wallet_to_bank',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Bank to Wallet',
            'slug' => 'bank_to_wallet',
        ]);

        DB::table('usage_types')->insert([
            'name' => 'Others',
            'slug' => 'other',
        ]);
    }
}
