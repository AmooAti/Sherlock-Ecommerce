<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname'    => fake()->firstName,
            'lastname'     => fake()->lastName,
            'email'        => fake()->unique()->safeEmail(),
            'password'     => Hash::make('password'),
            'phone_number' => fake()->phoneNumber,
        ];
    }
}
