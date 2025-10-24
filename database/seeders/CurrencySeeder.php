<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $rows = [
            ['code' => 'USD', 'name' => 'US Dollar', 'created_at' => $now,
                'updated_at' => $now],
            ['code' => 'EUR', 'name' => 'Euro', 'created_at' => $now,
                'updated_at' => $now],
            ['code' => 'GBP', 'name' => 'British Pound', 'created_at' =>
                $now, 'updated_at' => $now],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'created_at' => $now,
                'updated_at' => $now],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'created_at' => $now,
                'updated_at' => $now],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'created_at' => $now,
                'updated_at' => $now],
        ];
        DB::table('currencies')->upsert($rows, ['code']);
    }
}
