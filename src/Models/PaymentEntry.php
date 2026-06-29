<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Enums\PaymentType;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property PaymentType $payment_type
 * @property Carbon $posting_date
 * @property int|null $company_id
 * @property string|null $party_type
 * @property int|null $party_id
 * @property string|null $party_name
 * @property int $paid_from_id
 * @property int $paid_to_id
 * @property float $paid_amount
 * @property float $received_amount
 * @property string|null $reference_no
 * @property Carbon|null $reference_date
 * @property int|null $mode_of_payment_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $paidFrom
 * @property-read Account $paidTo
 * @property-read ModeOfPayment|null $modeOfPayment
 */
class PaymentEntry extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'payment_type',
        'posting_date',
        'company_id',
        'party_type',
        'party_id',
        'party_name',
        'paid_from_id',
        'paid_to_id',
        'paid_amount',
        'received_amount',
        'reference_no',
        'reference_date',
        'mode_of_payment_id',
        'docstatus',
    ];

    protected $attributes = [
        'docstatus' => 0,
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
        'posting_date' => 'date',
        'paid_amount' => 'float',
        'received_amount' => 'float',
        'reference_date' => 'date',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'payment_entries';
    }

    public function paidFrom(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'paid_from_id');
    }

    public function paidTo(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'paid_to_id');
    }

    public function modeOfPayment(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::modeOfPayment(), 'mode_of_payment_id');
    }

    public function postLedgerEntries(): void
    {
        $amount = (float) $this->paid_amount;

        if ($this->payment_type === PaymentType::Pay) {
            $entries = [
                ['account_id' => $this->paid_from_id, 'debit' => $amount, 'credit' => 0, 'party_type' => $this->party_type, 'party_id' => $this->party_id, 'against' => $this->paidTo->name ?? null],
                ['account_id' => $this->paid_to_id, 'debit' => 0, 'credit' => $amount, 'against' => $this->paidFrom->name ?? null],
            ];
        } else {
            $entries = [
                ['account_id' => $this->paid_to_id, 'debit' => $amount, 'credit' => 0, 'against' => $this->paidFrom->name ?? null],
                ['account_id' => $this->paid_from_id, 'debit' => 0, 'credit' => $amount, 'party_type' => $this->party_type, 'party_id' => $this->party_id, 'against' => $this->paidTo->name ?? null],
            ];
        }

        app(GeneralLedgerService::class)->post($this, $entries);
    }

    public function reverseLedgerEntries(): void
    {
        app(GeneralLedgerService::class)->reverse($this);
    }
}
