<?php

namespace JeffersonGoncalves\Erp\Accounting\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Models\TaxWithholdingCategory;
use JeffersonGoncalves\Erp\Accounting\Models\TaxWithholdingRate;

/**
 * Pure tax-withholding (TDS) computation.
 *
 * Resolves the rate effective on a given date for a withholding category and
 * computes the amount to withhold once the single transaction threshold is
 * exceeded. No general-ledger postings are produced here.
 */
class TaxWithholdingService
{
    /**
     * The withholding rate effective on the given date, or null when none of
     * the category's rate ranges cover it. An open-ended range (null to_date)
     * is treated as effective indefinitely.
     */
    public function rateOn(TaxWithholdingCategory $cat, CarbonInterface|string $date): ?TaxWithholdingRate
    {
        $on = Carbon::parse($date);

        /** @var TaxWithholdingRate|null $rate */
        $rate = $cat->rates()
            ->whereDate('from_date', '<=', $on)
            ->where(function ($query) use ($on): void {
                $query->whereNull('to_date')
                    ->orWhereDate('to_date', '>=', $on);
            })
            ->orderByDesc('from_date')
            ->first();

        return $rate;
    }

    /**
     * The amount to withhold from a transaction: base * rate / 100 when the
     * base exceeds the rate's single threshold, otherwise zero. Returns zero
     * when no rate is effective on the date.
     */
    public function computeWithholding(float $base, TaxWithholdingCategory $cat, string $date): float
    {
        $rate = $this->rateOn($cat, $date);

        if ($rate === null) {
            return 0.0;
        }

        if ($base <= $rate->single_threshold) {
            return 0.0;
        }

        return $base * $rate->tax_rate / 100;
    }
}
