<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\AccountingDimension;

/** @extends Factory<AccountingDimension> */
class AccountingDimensionFactory extends Factory
{
    protected $model = AccountingDimension::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'label' => fake()->unique()->word(),
            'reference_document' => fake()->randomElement(['Cost Center', 'Project', 'Department']),
            'is_mandatory' => false,
            'disabled' => false,
        ];
    }
}
