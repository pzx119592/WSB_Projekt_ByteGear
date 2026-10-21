<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Dodanie danych początkowych sklepu.
     */
    public function run(): void
    {
        $this->call(ShopSeeder::class);
    }
}
