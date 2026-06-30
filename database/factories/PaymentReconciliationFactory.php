<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentReconciliation;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<PaymentReconciliation> */
class PaymentReconciliationFactory extends Factory
{
    protected $model = PaymentReconciliation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'party_type' => 'Customer',
            'receivable_payable_account_id' => Account::factory()->ofType(RootType::Asset, AccountType::Receivable),
        ];
    }
}
