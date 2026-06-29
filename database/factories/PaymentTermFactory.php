<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentTerm;

/** @extends Factory<PaymentTerm> */
class PaymentTermFactory extends Factory
{
    protected $model = PaymentTerm::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'due_days' => fake()->randomElement([0, 15, 30, 45, 60]),
            'description' => fake()->optional()->sentence(),
            'invoice_portion' => 100,
        ];
    }
}
