<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<SalesInvoice> */
class SalesInvoiceFactory extends Factory
{
    protected $model = SalesInvoice::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Customer',
            'customer_name' => fake()->company(),
            'posting_date' => fake()->date(),
            'company_id' => Company::factory(),
            'currency' => 'USD',
            'conversion_rate' => 1,
            'debit_to_id' => Account::factory()->ofType(RootType::Asset, AccountType::Receivable),
            'status' => 'Draft',
        ];
    }
}
