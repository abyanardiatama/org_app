<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratPeringatan>
 */
class SuratPeringatanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //no_surat contoh => 112/PR/KMI_UPNVYK/VIII/2023
        $no_surat = str_pad($this->faker->unique()->numberBetween(1, 100), 3, '0', STR_PAD_LEFT) . '/PR/KMI_UPNVYK/' . date('m') . '/' . date('Y');
        // Generate a random date between 2023-01-01 and 2023-12-31
        $tanggal = $this->faker->dateTimeBetween('2025-01-01', now())->format('Y-m-d');
        // Surat Peringatan ke-1, ke-2, ke-3
        $perihal = $this->faker->randomElement([
            'Surat Peringatan 1',
            'Surat Peringatan 2',
            'Surat Peringatan 3',
        ]);
        //penerima from table users random      
        $penerima = User::where('role', '!=', 'external')->inRandomOrder()->first()?->name ?? 'Default Penerima';
        $nim_penerima = $penerima ? User::where('name', $penerima)->first()?->nim : 'Default NIM Penerima';
        return [
            'no_surat' => $no_surat,
            'tanggal_surat' => $tanggal,
            'perihal' => $perihal,
            'penerima' => $penerima,
            'nim_penerima' => $nim_penerima,
            'jml_lampiran' => $this->faker->numberBetween(1, 10),
        ];
    }
}
