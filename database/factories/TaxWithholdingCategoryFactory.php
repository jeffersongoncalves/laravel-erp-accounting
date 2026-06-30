<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\TaxWithholdingCategory;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<TaxWithholdingCategory> */
class TaxWithholdingCategoryFactory extends Factory
{
    protected $model = TaxWithholdingCategory::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'company_id' => Company::factory(),
        ];
    }
}
