<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\TaxTemplate;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<TaxTemplate> */
class TaxTemplateFactory extends Factory
{
    protected $model = TaxTemplate::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $title = fake()->unique()->words(2, true);

        return [
            'company_id' => Company::factory(),
            'name' => $title,
            'title' => $title,
            'is_sales' => true,
        ];
    }

    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sales' => false,
        ]);
    }
}
