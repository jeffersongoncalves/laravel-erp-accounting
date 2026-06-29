<?php

namespace JeffersonGoncalves\Erp\Accounting\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface GlEntryContract
{
    public function account(): BelongsTo;

    public function voucherable(): MorphTo;
}
