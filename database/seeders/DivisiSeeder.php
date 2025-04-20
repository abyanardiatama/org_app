<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Divisi::factory(10)->create();
        $divisi = [
            'Pengurus Inti' => 1,
            'BK KKMI' => 2,
            'Kaderisasi' => 3,
            'Syiar' => 4,
            'Dana Usaha' => 5,
            'Kemuslimahan' => 6,
            'Media' => 7,
        ];

        foreach ($divisi as $name => $id) {
            Divisi::create([
                'id' => $id,
                'nama_divisi' => $name,
                'image' => 'https://picsum.photos/300/200'
            ]);
        }
    }
}
