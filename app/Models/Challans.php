<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class Challans extends Model
{
    use HasFactory;

    protected $table = 'challans';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'student_name',
        'class_name',
        'month',
        'year',
        'challan_number',
        'amount',
        'status',
        'paid',
        'school_id',
        'session',
        'due_date',
        'month_name',
        'grno',
        'total_fee',
        'issued_date',
        'totalMonth',
        'fromYear',
        'fromMonth',
        'toYear',
        'toMonth',
        'exams',
        'total',
        'idf',
        'tution_fee',
        'csf',
        'rdfcdf',
        'security_fund',
        'admission',
        'breakage',
        'misc',
        'clc',
        'it',
        'slc',
        'debit',
        'type'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'tution_fee' => 'decimal:2',
        'exams' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'boolean',
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
