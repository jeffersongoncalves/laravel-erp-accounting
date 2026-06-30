<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $accounting_dimension_id
 * @property string $value
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AccountingDimension|null $dimension
 */
class AccountingDimensionValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_dimension_id',
        'value',
        'description',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'accounting_dimension_values';
    }

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(AccountingDimension::class, 'accounting_dimension_id');
    }
}
