<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;

/**
 * @property int $id
 * @property Carbon $date
 * @property int $bank_account_id
 * @property float $deposit
 * @property float $withdrawal
 * @property string|null $description
 * @property string|null $reference_number
 * @property string $status
 * @property float $allocated_amount
 * @property float $unallocated_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BankAccount $bankAccount
 */
class BankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'bank_account_id',
        'deposit',
        'withdrawal',
        'description',
        'reference_number',
        'status',
        'allocated_amount',
        'unallocated_amount',
    ];

    protected $attributes = [
        'deposit' => 0,
        'withdrawal' => 0,
        'status' => 'Pending',
        'allocated_amount' => 0,
        'unallocated_amount' => 0,
    ];

    protected $casts = [
        'date' => 'date',
        'deposit' => 'float',
        'withdrawal' => 'float',
        'allocated_amount' => 'float',
        'unallocated_amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (BankTransaction $transaction): void {
            $transaction->unallocated_amount = $transaction->transactionAmount() - (float) $transaction->allocated_amount;
        });
    }

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'bank_transactions';
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::bankAccount(), 'bank_account_id');
    }

    /** The signed magnitude of the statement line (deposit or withdrawal). */
    public function transactionAmount(): float
    {
        return (float) $this->deposit + (float) $this->withdrawal;
    }

    public function allocate(float $amount): void
    {
        $this->allocated_amount = (float) $this->allocated_amount + $amount;

        if ($this->unallocated_amount <= 0.0) {
            $this->status = 'Reconciled';
        }

        $this->save();
    }
}
