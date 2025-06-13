<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RAB>
 */
class RABFactory extends Factory
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
            'divisi' => $this->faker->randomElement(['Keuangan', 'SDM', 'Marketing', 'Teknologi Informasi', 'Operasional']),
            'nama_kegiatan' => $this->faker->randomElement($eventPrefixes) . ' ' . $this->faker->randomElement($eventTopics) . ' ' . $this->faker->randomElement($eventTypes),
            'tanggal_kegiatan' => $this->faker->date(),
            'status' => $this->faker->randomElement(['sudah diproses', 'belum diproses']),
            // jumlah in 100.000 - 1.000.000
            'jumlah' => $this->faker->numberBetween(100000, 1000000),
        ];
    }
}
