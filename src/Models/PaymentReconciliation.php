<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Services\PaymentReconciliationService;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property int $id
 * @property int|null $company_id
 * @property string $party_type
 * @property int|null $party_id
 * @property int $receivable_payable_account_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $receivablePayableAccount
 * @property-read Collection<int, PaymentReconciliationAllocation> $allocations
 */
class PaymentReconciliation extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'company_id',
        'party_type',
        'party_id',
        'receivable_payable_account_id',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'docstatus' => 0,
    ];

    protected $casts = [
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'payment_reconciliations';
    }

    public function receivablePayableAccount(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'receivable_payable_account_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentReconciliationAllocation::class, 'payment_reconciliation_id');
    }

    public function postLedgerEntries(): void
    {
        app(PaymentReconciliationService::class)->reconcile($this);
    }

    public function reverseLedgerEntries(): void
    {
        // Reconciliation posts no general-ledger rows of its own: the payments
        // it allocates were already posted when their Payment Entries were
        // submitted, so there is nothing to reverse here.
    }
}
