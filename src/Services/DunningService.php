<?php

namespace JeffersonGoncalves\Erp\Accounting\Services;

use JeffersonGoncalves\Erp\Accounting\Models\Dunning;

/**
 * Posts and reverses the interest charge of a Dunning document.
 *
 * On submit, when there is interest to charge, a balanced pair of entries is
 * written: the receivable is debited and the interest income account credited
 * by the total interest. A zero-interest dunning posts nothing.
 */
class DunningService
{
    public function post(Dunning $dunning): void
    {
        $interest = (float) $dunning->total_interest;

        if ($interest <= 0) {
            return;
        }

        $entries = [
            [
                'account_id' => $dunning->debit_to_account_id,
                'debit' => $interest,
                'credit' => 0,
                'party_type' => $dunning->party_type,
                'party_id' => $dunning->party_id,
                'against' => $dunning->customer_name,
            ],
            [
                'account_id' => $dunning->income_account_id,
                'debit' => 0,
                'credit' => $interest,
                'against' => $dunning->customer_name,
            ],
        ];

        app(GeneralLedgerService::class)->post($dunning, $entries);
    }

    public function reverse(Dunning $dunning): void
    {
        app(GeneralLedgerService::class)->reverse($dunning);
    }
}
