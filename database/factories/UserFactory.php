<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => fake()->randomElement(['admin', 'bendahara', 'sekretaris', 'ketua', 'anggota', 'external', 'bsomtq', 'phkmi']),
            'divisi_id' => fake()->numberBetween(1, 7),
            'prodi' => fake()->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Teknik Komputer']),
            'fakultas' => fake()->randomElement(['Teknik', 'Ekonomi', 'Hukum']),
            'angkatan' => fake()->year(),
            'amanah' => fake()->randomElement(['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara']),
            'no_hp' => fake()->phoneNumber(),
            'nim' => fn (array $attributes) => $attributes['role'] !== 'external' ? fake()->numerify('##########') : null,
            'nip' => fn (array $attributes) => $attributes['role'] === 'external' ? fake()->numerify('##########') : null,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'gender' => fake()->randomElement(['L', 'P']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
