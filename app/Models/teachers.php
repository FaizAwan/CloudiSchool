<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class teachers extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    protected $fillable = [
        'teacherName',
        'teacher_name',
        'email',
        'phone',
        'class_id',
        'className',
        'school_id',
        'status',
        'user_id'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    /**
     * Get the school that owns the teacher
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the class that the teacher is assigned to
     */
    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    /**
     * Get the user associated with the teacher
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
