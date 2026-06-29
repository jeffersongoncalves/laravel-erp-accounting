<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property int $id
 * @property int $purchase_invoice_id
 * @property int $account_id
 * @property float $rate
 * @property float $tax_amount
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PurchaseInvoice|null $purchaseInvoice
 * @property-read Account $account
 */
class PurchaseInvoiceTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_invoice_id',
        'account_id',
        'rate',
        'tax_amount',
        'description',
    ];

    protected $attributes = [
        'rate' => 0,
        'tax_amount' => 0,
    ];

    protected $casts = [
        'rate' => 'float',
        'tax_amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::saved(fn (PurchaseInvoiceTax $tax) => $tax->syncParentTotals());
        static::deleted(fn (PurchaseInvoiceTax $tax) => $tax->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'purchase_invoice_taxes';
    }

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::purchaseInvoice(), 'purchase_invoice_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'account_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->purchaseInvoice;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
