<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'className',
        'school_id',
        'session',
        'status',
        'user_id'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    /**
     * Get all students in this class
     */
    public function students()
    {
        return $this->hasMany(students::class, 'class_id');
    }

    /**
     * Get the school associated with the class
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all teachers assigned to this class
     */
    public function teachers()
    {
        return $this->hasMany(teachers::class, 'class_id');
    }

    /**
     * Scope to get active classes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by session
     */
    public function scopeForSession($query, $session)
    {
        return $query->where('session', $session);
    }
}
