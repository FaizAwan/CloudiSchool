<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class Challan extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    /**
     * Get the student associated with the challan
     */
    public function student()
    {
        return $this->belongsTo(students::class, 'student_id');
    }

    /**
     * Get the school associated with the challan
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Scope to get paid challans
     */
    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }

    /**
     * Scope to get unpaid challans
     */
    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }

    /**
     * Scope to filter by session
     */
    public function scopeForSession($query, $session)
    {
        return $query->where('session', $session);
    }
}
