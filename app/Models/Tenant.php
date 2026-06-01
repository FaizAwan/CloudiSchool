<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public $incrementing = true;
    public $keyType = 'int';

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'domain',
            'tenancy_db_name',
            'data',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Stancl/Tenancy attempts to set a UUID on 'id'.
            // We force it to null so the DB auto-increments it.
            if ($model->id && !is_numeric($model->id)) {
                $model->id = null;
            }
        });
    }
}
