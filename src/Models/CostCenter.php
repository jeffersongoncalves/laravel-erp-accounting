<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_cost_center_id
 * @property int|null $company_id
 * @property bool $is_group
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CostCenter|null $parent
 * @property-read Collection<int, CostCenter> $children
 */
class CostCenter extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_cost_center_id',
        'company_id',
        'is_group',
        'disabled',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'cost_centers';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::costCenter(), 'parent_cost_center_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ModelResolver::costCenter(), 'parent_cost_center_id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeLeaf(Builder $query): Builder
    {
        return $query->where('is_group', false);
    }
}
