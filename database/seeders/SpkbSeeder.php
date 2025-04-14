<?php

namespace Database\Seeders;

use App\Models\spkb;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SpkbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        spkb::factory(10)->create();
    }
}
