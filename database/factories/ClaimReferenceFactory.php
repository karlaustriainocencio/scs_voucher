<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClaimReference>
 */
class ClaimReferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'claim_id' => 1,
            'category_id' => 1,
            'description' => $this->faker->sentence(),
            'expense_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'receipt_path' => null,
            'is_rejected' => false,
            'rejection_reason' => null,
        ];
    }
}
