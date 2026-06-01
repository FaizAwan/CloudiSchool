<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'day',
        'period_id',
        'class',
        'subject'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    /**
     * Get the teacher associated with the timetable entry
     */
    public function teacher()
    {
        return $this->belongsTo(teachers::class);
    }
}
