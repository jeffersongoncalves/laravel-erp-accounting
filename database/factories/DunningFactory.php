<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\Dunning;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<Dunning> */
class DunningFactory extends Factory
{
    protected $model = Dunning::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'party_type' => 'Customer',
            'customer_name' => fake()->company(),
            'posting_date' => fake()->date(),
            'dunning_level' => 1,
            'rate_of_interest' => 0,
            'total_interest' => 0,
            'dunning_amount' => 0,
            'income_account_id' => Account::factory()->ofType(RootType::Income, AccountType::Income),
            'debit_to_account_id' => Account::factory()->ofType(RootType::Asset, AccountType::Receivable),
        ];
    }
}
