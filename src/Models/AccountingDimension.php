<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $label
 * @property string $reference_document
 * @property bool $is_mandatory
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AccountingDimensionValue> $values
 */
class AccountingDimension extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'reference_document',
        'is_mandatory',
        'disabled',
    ];

    protected $attributes = [
        'is_mandatory' => false,
        'disabled' => false,
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'accounting_dimensions';
    }

    public function values(): HasMany
    {
        return $this->hasMany(AccountingDimensionValue::class, 'accounting_dimension_id');
    }
}
