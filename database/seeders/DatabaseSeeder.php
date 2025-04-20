<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\RAB;
use App\Models\spkb;
use App\Models\Transaksi;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Divisi::factory(8)->create();
        // User::factory(11)->create();
        //user seeder
        $this->call([
            DivisiSeeder::class,
            UserSeeder::class,
        ]);
        Kegiatan::factory(11)->create();
        Presensi::factory(11)->create();
        RAB::factory(11)->create();
        Transaksi::factory(11)->create();
        spkb::factory(11)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
