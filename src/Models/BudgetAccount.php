<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;

/**
 * @property int $id
 * @property int $budget_id
 * @property int $account_id
 * @property float $budget_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Budget $budget
 * @property-read Account $account
 */
class BudgetAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'account_id',
        'budget_amount',
    ];

    protected $casts = [
        'budget_amount' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'budget_accounts';
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::budget(), 'budget_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'account_id');
    }
}
