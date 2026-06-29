<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Contracts\AccountContract;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $account_number
 * @property int|null $parent_account_id
 * @property int|null $company_id
 * @property bool $is_group
 * @property RootType|null $root_type
 * @property AccountType|null $account_type
 * @property string|null $account_currency
 * @property bool $disabled
 * @property bool $freeze_account
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account|null $parent
 * @property-read Collection<int, Account> $children
 */
class Account extends Model implements AccountContract
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'account_number',
        'parent_account_id',
        'company_id',
        'is_group',
        'root_type',
        'account_type',
        'account_currency',
        'disabled',
        'freeze_account',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'disabled' => 'boolean',
        'freeze_account' => 'boolean',
        'root_type' => RootType::class,
        'account_type' => AccountType::class,
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'accounts';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'parent_account_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ModelResolver::account(), 'parent_account_id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeLeaf(Builder $query): Builder
    {
        return $query->where('is_group', false);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeGroups(Builder $query): Builder
    {
        return $query->where('is_group', true);
    }
}
