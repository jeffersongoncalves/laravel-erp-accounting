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
 * @property int $sales_invoice_id
 * @property string $item_code
 * @property string|null $item_name
 * @property string|null $description
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property int|null $income_account_id
 * @property int|null $cost_center_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SalesInvoice|null $salesInvoice
 * @property-read Account|null $incomeAccount
 */
class SalesInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_invoice_id',
        'item_code',
        'item_name',
        'description',
        'qty',
        'rate',
        'amount',
        'income_account_id',
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
        static::saving(function (SalesInvoiceItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (SalesInvoiceItem $item) => $item->syncParentTotals());
        static::deleted(fn (SalesInvoiceItem $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'sales_invoice_items';
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salesInvoice(), 'sales_invoice_id');
    }

    public function incomeAccount(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'income_account_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->salesInvoice;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
