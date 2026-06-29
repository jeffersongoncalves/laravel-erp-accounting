<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, BankAccount> $bankAccounts
 */
class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'banks';
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(ModelResolver::bankAccount(), 'bank_id');
    }
}
