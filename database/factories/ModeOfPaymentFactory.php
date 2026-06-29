<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\ModeOfPayment;

/** @extends Factory<ModeOfPayment> */
class ModeOfPaymentFactory extends Factory
{
    protected $model = ModeOfPayment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'type' => fake()->randomElement(['Cash', 'Bank', 'General']),
            'default_account_id' => null,
        ];
    }
}
