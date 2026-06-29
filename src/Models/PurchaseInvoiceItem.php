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
 * @property string $item_code
 * @property string|null $item_name
 * @property string|null $description
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property int|null $expense_account_id
 * @property int|null $cost_center_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PurchaseInvoice|null $purchaseInvoice
 * @property-read Account|null $expenseAccount
 */
class PurchaseInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_invoice_id',
        'item_code',
        'item_name',
        'description',
        'qty',
        'rate',
        'amount',
        'expense_account_id',
        'cost_center_id',
    ];

    protected $attributes = [
        'qty' => 1,
        'rate' => 0,
        'amount' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'rate' => 'float',
        'amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (PurchaseInvoiceItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (PurchaseInvoiceItem $item) => $item->syncParentTotals());
        static::deleted(fn (PurchaseInvoiceItem $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'purchase_invoice_items';
    }

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::purchaseInvoice(), 'purchase_invoice_id');
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'expense_account_id');
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
