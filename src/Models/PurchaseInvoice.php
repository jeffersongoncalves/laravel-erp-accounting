<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
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
 * @property string $party_type
 * @property int|null $party_id
 * @property string $supplier_name
 * @property Carbon $posting_date
 * @property Carbon|null $due_date
 * @property int|null $company_id
 * @property string $currency
 * @property float $conversion_rate
 * @property int $credit_to_id
 * @property float $net_total
 * @property float $total_taxes
 * @property float $grand_total
 * @property float $outstanding_amount
 * @property string $status
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $creditTo
 * @property-read Collection<int, PurchaseInvoiceItem> $items
 * @property-read Collection<int, PurchaseInvoiceTax> $taxes
 */
class PurchaseInvoice extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'party_type',
        'party_id',
        'supplier_name',
        'posting_date',
        'due_date',
        'company_id',
        'currency',
        'conversion_rate',
        'credit_to_id',
        'net_total',
        'total_taxes',
        'grand_total',
        'outstanding_amount',
        'status',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Supplier',
        'currency' => 'USD',
        'conversion_rate' => 1,
        'net_total' => 0,
        'total_taxes' => 0,
        'grand_total' => 0,
        'outstanding_amount' => 0,
        'status' => 'Draft',
        'docstatus' => 0,
    ];

    protected $casts = [
        'posting_date' => 'date',
        'due_date' => 'date',
        'conversion_rate' => 'float',
        'net_total' => 'float',
        'total_taxes' => 'float',
        'grand_total' => 'float',
        'outstanding_amount' => 'float',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (PurchaseInvoice $invoice): void {
            if ($invoice->docstatus === DocStatus::Draft) {
                $invoice->calculateTotals();
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'purchase_invoices';
    }

    public function creditTo(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'credit_to_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::purchaseInvoiceItem(), 'purchase_invoice_id');
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(ModelResolver::purchaseInvoiceTax(), 'purchase_invoice_id');
    }

    public function calculateTotals(): void
    {
        $netTotal = $this->exists ? (float) $this->items()->sum('amount') : 0.0;
        $totalTaxes = $this->exists ? (float) $this->taxes()->sum('tax_amount') : 0.0;

        $this->net_total = $netTotal;
        $this->total_taxes = $totalTaxes;
        $this->grand_total = $netTotal + $totalTaxes;
        $this->outstanding_amount = $this->grand_total;
    }

    public function postLedgerEntries(): void
    {
        $entries = [[
            'account_id' => $this->credit_to_id,
            'debit' => 0,
            'credit' => $this->grand_total,
            'party_type' => $this->party_type,
            'party_id' => $this->party_id,
            'against' => $this->supplier_name,
        ]];

        foreach ($this->items as $item) {
            $entries[] = [
                'account_id' => $item->expense_account_id,
                'debit' => $item->amount,
                'credit' => 0,
                'cost_center_id' => $item->cost_center_id,
                'against' => $this->supplier_name,
            ];
        }

        foreach ($this->taxes as $tax) {
            $entries[] = [
                'account_id' => $tax->account_id,
                'debit' => $tax->tax_amount,
                'credit' => 0,
                'against' => $this->supplier_name,
            ];
        }

        app(GeneralLedgerService::class)->post($this, $entries);
    }

    public function reverseLedgerEntries(): void
    {
        app(GeneralLedgerService::class)->reverse($this);
    }
}
