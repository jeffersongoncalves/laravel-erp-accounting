<?php

namespace JeffersonGoncalves\Erp\Accounting\Services;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentReconciliation;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;

/**
 * Applies a payment reconciliation: each allocation reduces the outstanding
 * amount of its target invoice by the allocated amount, clamped at zero.
 *
 * No general-ledger rows are written — the payments being allocated were
 * already posted when their Payment Entries were submitted.
 */
class PaymentReconciliationService
{
    public function reconcile(PaymentReconciliation $rec): void
    {
        foreach ($rec->allocations as $allocation) {
            $model = $this->invoiceModel($allocation->invoice_type);

            if ($model === null) {
                continue;
            }

            $invoice = $model::query()->find($allocation->invoice_id);

            if ($invoice === null) {
                continue;
            }

            $current = (float) $invoice->getAttribute('outstanding_amount');
            $new = max(0.0, $current - (float) $allocation->allocated_amount);

            // Bypass the model lifecycle: a submitted invoice is immutable
            // through Eloquent events, but its outstanding balance must still
            // settle as payments are reconciled against it.
            $model::query()
                ->whereKey($allocation->invoice_id)
                ->update(['outstanding_amount' => $new]);
        }
    }

    /**
     * Resolve the invoice model class for an allocation's invoice type.
     *
     * @return class-string<Model>|null
     */
    protected function invoiceModel(string $invoiceType): ?string
    {
        return match ($invoiceType) {
            'SalesInvoice' => ModelResolver::salesInvoice(),
            'PurchaseInvoice' => ModelResolver::purchaseInvoice(),
            default => null,
        };
    }
}
