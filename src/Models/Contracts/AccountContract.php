<?php

namespace JeffersonGoncalves\Erp\Accounting\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface AccountContract
{
    public function parent(): BelongsTo;

    public function children(): HasMany;
}
