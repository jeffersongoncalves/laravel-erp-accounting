<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\GlEntry;
use JeffersonGoncalves\Erp\Accounting\Models\JournalEntry;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->cash = Account::factory()->create(['company_id' => $this->company->id]);
    $this->sales = Account::factory()->create(['company_id' => $this->company->id]);
});

function balancedJournalEntry(Company $company, Account $debit, Account $credit, float $amount = 100): JournalEntry
{
    $entry = JournalEntry::factory()->create(['company_id' => $company->id]);
    $entry->accounts()->create(['account_id' => $debit->id, 'debit' => $amount, 'credit' => 0]);
    $entry->accounts()->create(['account_id' => $credit->id, 'debit' => 0, 'credit' => $amount]);

    return $entry->refresh();
}

it('recalculates totals from its child rows', function () {
    $entry = balancedJournalEntry($this->company, $this->cash, $this->sales, 250);

    expect($entry->total_debit)->toBe(250.0)
        ->and($entry->total_credit)->toBe(250.0);
});

it('posts one balanced gl entry per row on submit', function () {
    $entry = balancedJournalEntry($this->company, $this->cash, $this->sales, 100);

    $entry->submit();

    expect($entry->docstatus)->toBe(DocStatus::Submitted)
        ->and(GlEntry::query()->count())->toBe(2)
        ->and((float) GlEntry::query()->sum('debit'))->toBe(100.0)
        ->and((float) GlEntry::query()->sum('credit'))->toBe(100.0);
});

it('throws when debit and credit are not balanced', function () {
    $entry = JournalEntry::factory()->create(['company_id' => $this->company->id]);
    $entry->accounts()->create(['account_id' => $this->cash->id, 'debit' => 100, 'credit' => 0]);
    $entry->accounts()->create(['account_id' => $this->sales->id, 'debit' => 0, 'credit' => 60]);

    expect(fn () => $entry->refresh()->submit())
        ->toThrow(DomainException::class, 'Debit and credit not balanced');
});

it('reverses to a net zero on cancel', function () {
    $entry = balancedJournalEntry($this->company, $this->cash, $this->sales, 100);
    $entry->submit();

    $entry->cancel();

    expect($entry->docstatus)->toBe(DocStatus::Cancelled)
        ->and(GlEntry::query()->count())->toBe(4)
        ->and(GlEntry::query()->where('is_cancelled', false)->count())->toBe(0)
        ->and((float) GlEntry::query()->sum('debit'))->toBe((float) GlEntry::query()->sum('credit'));
});
