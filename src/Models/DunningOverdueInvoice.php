<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;

/**
 * @property int $id
 * @property int $dunning_id
 * @property int $sales_invoice_id
 * @property int $overdue_days
 * @property float $outstanding
 * @property float $interest
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Dunning|null $dunning
 * @property-read SalesInvoice|null $salesInvoice
 */
class DunningOverdueInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'dunning_id',
        'sales_invoice_id',
        'overdue_days',
        'outstanding',
        'interest',
    ];

    protected $attributes = [
        'overdue_days' => 0,
        'outstanding' => 0,
        'interest' => 0,
    ];

    protected $casts = [
        'overdue_days' => 'integer',
        'outstanding' => 'float',
        'interest' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'dunning_overdue_invoices';
    }

    public function dunning(): BelongsTo
    {
        return $this->belongsTo(Dunning::class, 'dunning_id');
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salesInvoice(), 'sales_invoice_id');
    }
}
