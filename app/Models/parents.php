<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class parents extends Model
{
    use HasFactory;

    protected $fillable = [
        'parentName',
        'fatherName',
        'motherName',
        'phone',
        'email',
        'address',
        'occupation',
        'is_commandercityschool_employee',
        'status',
        'school_id'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    /**
     * Get all students for this parent
     */
    public function students()
    {
        return $this->hasMany(students::class, 'parent_id');
    }

    /**
     * Get the school associated with the parent
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Scope to get active parents
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if parent is a school employee
     */
    public function isSchoolEmployee()
    {
        return $this->is_commandercityschool_employee === 'Yes';
    }
}
