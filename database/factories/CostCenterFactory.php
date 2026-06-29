<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\CostCenter;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<CostCenter> */
class CostCenterFactory extends Factory
{
    protected $model = CostCenter::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'company_id' => Company::factory(),
            'is_group' => false,
            'disabled' => false,
        ];
    }

    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
        ]);
    }
}
