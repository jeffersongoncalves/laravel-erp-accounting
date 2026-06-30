<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\Dunning;
use JeffersonGoncalves\Erp\Accounting\Models\DunningOverdueInvoice;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;

/** @extends Factory<DunningOverdueInvoice> */
class DunningOverdueInvoiceFactory extends Factory
{
    protected $model = DunningOverdueInvoice::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'dunning_id' => Dunning::factory(),
            'sales_invoice_id' => SalesInvoice::factory(),
            'overdue_days' => fake()->numberBetween(1, 120),
            'outstanding' => fake()->randomFloat(2, 10, 1000),
            'interest' => fake()->randomFloat(2, 1, 100),
        ];
    }
}
