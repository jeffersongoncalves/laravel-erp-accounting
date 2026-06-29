<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\PurchaseInvoice;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<PurchaseInvoice> */
class PurchaseInvoiceFactory extends Factory
{
    protected $model = PurchaseInvoice::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Supplier',
            'supplier_name' => fake()->company(),
            'posting_date' => fake()->date(),
            'company_id' => Company::factory(),
            'currency' => 'USD',
            'conversion_rate' => 1,
            'credit_to_id' => Account::factory()->ofType(RootType::Liability, AccountType::Payable),
            'status' => 'Draft',
        ];
    }
}
