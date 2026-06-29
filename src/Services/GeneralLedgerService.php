<?php

namespace JeffersonGoncalves\Erp\Accounting\Services;

use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;

/**
 * The double-entry posting engine.
 *
 * Every submittable document that touches money posts a balanced set of
 * entries to the general ledger through {@see post()} and unwinds them on
 * cancellation through {@see reverse()}. Ledger rows are immutable: a
 * cancellation never edits the monetary impact of an existing row, it flags
 * the originals and writes mirror rows with debit and credit swapped so the
 * net effect of the voucher becomes zero.
 */
class GeneralLedgerService
{
    /**
     * Post a balanced set of general-ledger entries for a voucher.
     *
     * @param  SubmittableDocument&Model  $voucher
     * @param  list<array<string, mixed>>  $entries
     *
     * @throws DomainException when total debit does not equal total credit.
     */
    public function post(SubmittableDocument $voucher, array $entries): void
    {
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($entries as $entry) {
            $totalDebit += (float) ($entry['debit'] ?? 0);
            $totalCredit += (float) ($entry['credit'] ?? 0);
        }

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new DomainException('Debit and credit not balanced');
        }

        $glModel = ModelResolver::glEntry();

        foreach ($entries as $entry) {
            $glModel::query()->create([
                'posting_date' => $voucher->getAttribute('posting_date'),
                'account_id' => $entry['account_id'],
                'debit' => $entry['debit'] ?? 0,
                'credit' => $entry['credit'] ?? 0,
                'voucherable_type' => $voucher->getMorphClass(),
                'voucherable_id' => $voucher->getKey(),
                'against_account' => $entry['against'] ?? null,
                'party_type' => $entry['party_type'] ?? null,
                'party_id' => $entry['party_id'] ?? null,
                'cost_center_id' => $entry['cost_center_id'] ?? null,
                'company_id' => $voucher->getAttribute('company_id'),
                'remarks' => $entry['remarks'] ?? null,
                'is_cancelled' => false,
            ]);
        }
    }

    /**
     * Reverse every active ledger entry of a voucher.
     *
     * Mirror rows are written with debit and credit swapped and both the
     * originals and the mirrors are marked cancelled, leaving a net zero.
     *
     * @param  SubmittableDocument&Model  $voucher
     */
    public function reverse(SubmittableDocument $voucher): void
    {
        $glModel = ModelResolver::glEntry();

        /** @var Collection<int, Model> $entries */
        $entries = $glModel::query()
            ->where('voucherable_type', $voucher->getMorphClass())
            ->where('voucherable_id', $voucher->getKey())
            ->where('is_cancelled', false)
            ->get();

        foreach ($entries as $entry) {
            $glModel::query()->create([
                'posting_date' => $entry->getAttribute('posting_date'),
                'account_id' => $entry->getAttribute('account_id'),
                'debit' => $entry->getAttribute('credit'),
                'credit' => $entry->getAttribute('debit'),
                'voucherable_type' => $entry->getAttribute('voucherable_type'),
                'voucherable_id' => $entry->getAttribute('voucherable_id'),
                'against_account' => $entry->getAttribute('against_account'),
                'party_type' => $entry->getAttribute('party_type'),
                'party_id' => $entry->getAttribute('party_id'),
                'cost_center_id' => $entry->getAttribute('cost_center_id'),
                'company_id' => $entry->getAttribute('company_id'),
                'remarks' => 'Reversal',
                'is_cancelled' => true,
            ]);

            $entry->setAttribute('is_cancelled', true);
            $entry->save();
        }
    }

    /**
     * The running balance (debit minus credit) of an account, optionally up to
     * a given posting date. Cancelled entries are excluded.
     */
    public function accountBalance(Account $account, ?Carbon $upto = null): float
    {
        $glModel = ModelResolver::glEntry();

        $query = $glModel::query()
            ->where('account_id', $account->getKey())
            ->where('is_cancelled', false);

        if ($upto !== null) {
            $query->whereDate('posting_date', '<=', $upto);
        }

        return (float) $query->sum('debit') - (float) $query->sum('credit');
    }
}
