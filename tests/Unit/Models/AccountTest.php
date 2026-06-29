<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;

it('uses the configured table prefix', function () {
    expect((new Account)->getTable())->toBe('erp_accounts');
});

it('casts root and account types to enums', function () {
    $account = Account::factory()->create([
        'root_type' => RootType::Asset,
        'account_type' => AccountType::Receivable,
    ]);

    expect($account->refresh()->root_type)->toBe(RootType::Asset)
        ->and($account->account_type)->toBe(AccountType::Receivable);
});

it('builds a parent and children tree', function () {
    $parent = Account::factory()->group()->create();
    $child = Account::factory()->create(['parent_account_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id)
        ->and($parent->children)->toHaveCount(1)
        ->and($parent->is_group)->toBeTrue();
});

it('scopes leaf accounts', function () {
    Account::factory()->group()->create();
    Account::factory()->create();

    expect(Account::query()->leaf()->count())->toBe(1);
});
