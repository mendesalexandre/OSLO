<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome'     => fake('pt_BR')->name(),
            'email'    => fake()->unique()->safeEmail(),
            'senha'    => 'password', // hashada pelo cast 'hashed'
            'is_ativo' => true,
        ];
    }

    public function inativo(): static
    {
        return $this->state(fn () => ['is_ativo' => false]);
    }
}
