<?php

namespace Database\Seeders;

use App\Models\RAB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RABSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RAB::factory(10)->create();
    }
}
