<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int $due_days
 * @property string|null $description
 * @property float $invoice_portion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PaymentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'due_days',
        'description',
        'invoice_portion',
    ];

    protected $casts = [
        'due_days' => 'integer',
        'invoice_portion' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'payment_terms';
    }
}
