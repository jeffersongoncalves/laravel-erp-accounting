<?php

namespace JeffersonGoncalves\Erp\Accounting\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeffersonGoncalves\Erp\Accounting\Models\Contracts\AccountContract;
use JeffersonGoncalves\Erp\Accounting\Models\Contracts\GlEntryContract;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model&AccountContract> */
    public static function account(): string
    {
        return static::resolve('account', AccountContract::class);
    }

    /** @return class-string<Model&GlEntryContract> */
    public static function glEntry(): string
    {
        return static::resolve('gl_entry', GlEntryContract::class);
    }

    /** @return class-string<Model> */
    public static function costCenter(): string
    {
        return static::resolve('cost_center');
    }

    /** @return class-string<Model> */
    public static function paymentTerm(): string
    {
        return static::resolve('payment_term');
    }

    /** @return class-string<Model> */
    public static function modeOfPayment(): string
    {
        return static::resolve('mode_of_payment');
    }

    /** @return class-string<Model> */
    public static function taxTemplate(): string
    {
        return static::resolve('tax_template');
    }

    /** @return class-string<Model> */
    public static function taxTemplateTax(): string
    {
        return static::resolve('tax_template_tax');
    }

    /** @return class-string<Model> */
    public static function bank(): string
    {
        return static::resolve('bank');
    }

    /** @return class-string<Model> */
    public static function bankAccount(): string
    {
        return static::resolve('bank_account');
    }

    /** @return class-string<Model> */
    public static function budget(): string
    {
        return static::resolve('budget');
    }

    /** @return class-string<Model> */
    public static function budgetAccount(): string
    {
        return static::resolve('budget_account');
    }

    /** @return class-string<Model> */
    public static function journalEntry(): string
    {
        return static::resolve('journal_entry');
    }

    /** @return class-string<Model> */
    public static function journalEntryAccount(): string
    {
        return static::resolve('journal_entry_account');
    }

    /** @return class-string<Model> */
    public static function paymentEntry(): string
    {
        return static::resolve('payment_entry');
    }

    /** @return class-string<Model> */
    public static function salesInvoice(): string
    {
        return static::resolve('sales_invoice');
    }

    /** @return class-string<Model> */
    public static function salesInvoiceItem(): string
    {
        return static::resolve('sales_invoice_item');
    }

    /** @return class-string<Model> */
    public static function salesInvoiceTax(): string
    {
        return static::resolve('sales_invoice_tax');
    }

    /** @return class-string<Model> */
    public static function purchaseInvoice(): string
    {
        return static::resolve('purchase_invoice');
    }

    /** @return class-string<Model> */
    public static function purchaseInvoiceItem(): string
    {
        return static::resolve('purchase_invoice_item');
    }

    /** @return class-string<Model> */
    public static function purchaseInvoiceTax(): string
    {
        return static::resolve('purchase_invoice_tax');
    }

    /** @return class-string<Model> */
    public static function periodClosingVoucher(): string
    {
        return static::resolve('period_closing_voucher');
    }

    /** @return class-string<Model> */
    public static function bankTransaction(): string
    {
        return static::resolve('bank_transaction');
    }

    /**
     * @param  class-string|null  $contract
     * @return class-string
     *
     * @throws InvalidArgumentException
     */
    protected static function resolve(string $key, ?string $contract = null): string
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        /** @var class-string|null $model */
        $model = config("erp-accounting.models.{$key}");

        if (! $model || ! class_exists($model)) {
            throw new InvalidArgumentException(
                "Model class for [{$key}] does not exist: {$model}"
            );
        }

        if ($contract !== null && ! is_a($model, $contract, true)) {
            throw new InvalidArgumentException(
                "Model [{$model}] must implement [{$contract}]."
            );
        }

        return static::$cache[$key] = $model;
    }

    public static function flushCache(): void
    {
        static::$cache = [];
    }
}
