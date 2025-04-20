<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Divisi;

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

        return [
            'nama_kegiatan' => $this->faker->randomElement($eventPrefixes) . ' ' . $this->faker->randomElement($eventTopics) . ' ' . $this->faker->randomElement($eventTypes),
            'tanggal_kegiatan' => $this->faker->date(),
            // Ambil ID divisi yang valid dari tabel divisis
            'divisi_id' => $this->faker->randomElement(Divisi::pluck('id')->toArray()),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif']),
        ];
    }
}
