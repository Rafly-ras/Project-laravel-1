<?php

namespace App\Traits;

use LogicException;

trait ImmutableLedger
{
    /**
     * Boot the trait to prevent updates and deletions.
     */
    protected static function bootImmutableLedger()
    {
        static::updating(function ($model) {
            throw new LogicException("Cannot update an immutable ledger entry.");
        });

        static::deleting(function ($model) {
            throw new LogicException("Cannot delete an immutable ledger entry.");
        });
    }
}
