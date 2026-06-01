<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class TenantOwned implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // If an authenticated user exists with tenant_id, scope queries
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        if ($tenantId && Schema::hasColumn($model->getTable(), 'tenant_id')) {
            $builder->where($model->getTable().'.tenant_id', $tenantId);
        }
    }

    public static function bootFor(Model $model): void
    {
        $model::creating(function ($m) {
            if (empty($m->tenant_id) && auth()->check() && Schema::hasColumn($m->getTable(), 'tenant_id')) {
                $m->tenant_id = auth()->user()->tenant_id ?? null;
            }
        });
    }
}
