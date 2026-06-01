<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class students extends Model
{
    use HasFactory;

    protected $fillable = [
        'studentName',
        'class_id',
        'status',
        'parent_id',
        'grno',
        'school_id',
        'session',
        'gender',
        'date_of_birth',
        'address',
        'phone',
        'email',
        'user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    /**
     * Get the parent associated with the student
     */
    public function parent()
    {
        return $this->belongsTo(parents::class, 'parent_id');
    }

    /**
     * Get the school associated with the student
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the class associated with the student
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the user associated with the student
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student's name
     */
    public function getNameAttribute()
    {
        return $this->studentName;
    }

    /**
     * Scope to get active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the class name through relationship
     */
    public function getClassNameAttribute()
    {
        return $this->class ? $this->class->className : null;
    }
}
