<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class fees extends Model
{
    use HasFactory;

    protected $table = 'fees';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'class_id',
        'class_name',
        'fee_name',
        'amount',
        'month',
        'month_name',
        'session',
        'year',
        'fee_type_id',
        'fee_value',
        'school_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_value' => 'decimal:2',
        'year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }
}
