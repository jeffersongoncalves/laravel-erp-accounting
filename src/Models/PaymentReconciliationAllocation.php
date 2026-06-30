<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;

/**
 * @property int $id
 * @property int $payment_reconciliation_id
 * @property int $payment_entry_id
 * @property string $invoice_type
 * @property int $invoice_id
 * @property float $allocated_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PaymentReconciliation|null $paymentReconciliation
 * @property-read PaymentEntry|null $paymentEntry
 */
class PaymentReconciliationAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_reconciliation_id',
        'payment_entry_id',
        'invoice_type',
        'invoice_id',
        'allocated_amount',
    ];

    protected $attributes = [
        'allocated_amount' => 0,
    ];

    protected $casts = [
        'allocated_amount' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'payment_reconciliation_allocations';
    }

    public function paymentReconciliation(): BelongsTo
    {
        return $this->belongsTo(PaymentReconciliation::class, 'payment_reconciliation_id');
    }

    public function paymentEntry(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::paymentEntry(), 'payment_entry_id');
    }
}
