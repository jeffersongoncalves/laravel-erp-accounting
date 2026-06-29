<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<Account> */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'account_number' => (string) fake()->unique()->numberBetween(1000, 9999),
            'company_id' => Company::factory(),
            'is_group' => false,
            'root_type' => fake()->randomElement(RootType::cases()),
            'account_type' => fake()->randomElement(AccountType::cases()),
            'disabled' => false,
            'freeze_account' => false,
        ];
    }

    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
        ]);
    }

    public function ofType(RootType $rootType, ?AccountType $accountType = null): static
    {
        return $this->state(fn (array $attributes) => [
            'root_type' => $rootType,
            'account_type' => $accountType,
        ]);
    }
}
