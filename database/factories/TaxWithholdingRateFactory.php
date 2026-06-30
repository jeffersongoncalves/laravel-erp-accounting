<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\TaxWithholdingCategory;
use JeffersonGoncalves\Erp\Accounting\Models\TaxWithholdingRate;

/** @extends Factory<TaxWithholdingRate> */
class TaxWithholdingRateFactory extends Factory
{
    protected $model = TaxWithholdingRate::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'tax_withholding_category_id' => TaxWithholdingCategory::factory(),
            'from_date' => '2024-01-01',
            'to_date' => '2024-12-31',
            'tax_rate' => fake()->randomFloat(2, 1, 20),
            'single_threshold' => 0,
            'cumulative_threshold' => 0,
        ];
    }
}
