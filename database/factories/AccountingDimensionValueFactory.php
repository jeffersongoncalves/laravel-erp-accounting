<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\AccountingDimension;
use JeffersonGoncalves\Erp\Accounting\Models\AccountingDimensionValue;

/** @extends Factory<AccountingDimensionValue> */
class AccountingDimensionValueFactory extends Factory
{
    protected $model = AccountingDimensionValue::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'accounting_dimension_id' => AccountingDimension::factory(),
            'value' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }
}
