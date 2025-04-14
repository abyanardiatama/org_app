<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\spkb>
 */
class SpkbFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a unique no_spkb number between 1 and 100
        // format => 001/PKB/KMI_UPNVYK/V/2023
        $no_spkb = str_pad($this->faker->unique()->numberBetween(1, 100), 3, '0', STR_PAD_LEFT) . '/PKB/KMI_UPNVYK/V/' . date('Y');
        // Generate a random date between 2023-01-01 and 2023-12-31
        $tanggal = $this->faker->dateTimeBetween('2023-01-01', now())->format('Y-m-d');

        return [
            'no_surat' => $no_spkb,
            'jml_lampiran' => $this->faker->numberBetween(1, 10),
            //user with role 'ketua'
            'ketua_kmi' => User::where('role', 'ketua')->inRandomOrder()->first()?->name ?? 'Default Ketua',
            'nim_ketua_kmi' => User::where('role', 'ketua')->inRandomOrder()->first()?->nim ?? 'Default NIM Ketua',
            //user with role 'sekretaris'
            'sekretaris_kmi' => User::where('role', 'sekretaris')->inRandomOrder()->first()?->name ?? 'Default Sekretaris',
            'nim_sekretaris_kmi' => User::where('role', 'sekretaris')->inRandomOrder()->first()?->nim ?? 'Default NIM Sekretaris',
            //user with role 'kabag_binwa'
            'kabag_binwa' => User::where('role', 'external')->inRandomOrder()->first()?->name ?? 'Default Kabag',
            'nip_kabag_binwa' => User::where('role', 'external')->inRandomOrder()->first()?->nim ?? 'Default NIP Kabag',
            //user with role 'pembina_kmi'
            'pembina_kmi' => User::where('role', 'external')->inRandomOrder()->first()?->name ?? 'Default Pembina',
            'nip_pembina_kmi' => User::where('role', 'external')->inRandomOrder()->first()?->nim ?? 'Default NIP Pembina',
            'tanggal_surat' => $tanggal,
            //take from tanggal_surat
            'periode' => date('Y', strtotime($tanggal)),
            //faker pdf
            'susunan_pengurus' => UploadedFile::fake()->create($this->faker->word . '.pdf', 100),
        ];
    }
}
