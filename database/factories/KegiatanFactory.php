<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Divisi;
use App\Models\Donasi;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kegiatan>
 */
class KegiatanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventPrefixes = ['Annual', 'Global', 'National', 'Regional', 'Community', 'Corporate', 'Professional'];
        $eventTopics = ['Leadership', 'Innovation', 'Development', 'Collaboration', 'Networking', 'Growth', 'Strategy'];
        $eventTypes = ['Summit', 'Forum', 'Conference', 'Workshop', 'Meetup', 'Convention', 'Retreat'];
        // sum total biaya from donasi
        $total_biaya = Donasi::sum('jumlah_donasi') ?: $this->faker->randomFloat(2, 100000, 1000000); // Total biaya antara 100.000 dan 1.000.000 jika tidak ada donasi
        //target biaya more than total biaya
        $target_biaya = $this->faker->randomFloat(2, $total_biaya + 100000, $total_biaya + 2000000); // Target biaya antara total biaya + 100.000 dan total biaya + 2.000.000
        return [
            'nama_kegiatan' => $this->faker->randomElement($eventPrefixes) . ' ' . $this->faker->randomElement($eventTopics) . ' ' . $this->faker->randomElement($eventTypes),
            'tanggal_kegiatan' => $this->faker->date(),
            // Ambil ID divisi yang valid dari tabel divisis
            'divisi_id' => $this->faker->randomElement(Divisi::pluck('id')->toArray()),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif']),
            'total_biaya' => $total_biaya, // Total biaya antara 100.000 dan 1.000.000
            'target_biaya' => $target_biaya, // Target biaya antara total biaya + 100.000 dan total biaya + 2.000.000
        ];
    }
}
