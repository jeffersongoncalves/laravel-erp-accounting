<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\Bank;
use JeffersonGoncalves\Erp\Accounting\Models\BankAccount;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<BankAccount> */
class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'account_name' => fake()->unique()->words(2, true),
            'bank_id' => Bank::factory(),
            'account_no' => fake()->bankAccountNumber(),
            'company_id' => Company::factory(),
            'is_default' => false,
            'is_company_account' => true,
        ];
    }
}
