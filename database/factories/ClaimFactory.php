<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Claim>
 */
class ClaimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_number' => 'SCS' . now()->format('ym') . '-' . str_pad($this->faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'payee_type' => 'App\Models\Employee',
            'payee_id' => 1,
            'total_amount' => $this->faker->randomFloat(2, 100, 5000),
            'status' => 'draft',
            'company' => 'SCS',
        ];
    }
}
