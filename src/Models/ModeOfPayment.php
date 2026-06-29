<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int|null $default_account_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account|null $defaultAccount
 */
class ModeOfPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'default_account_id',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'modes_of_payment';
    }

    public function defaultAccount(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'default_account_id');
    }
}
