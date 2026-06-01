<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'student_id',
        'class_id',
        'date',
        'session',
        'status',
        'remarks',
        'school_id',
        'teacher_id',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    /**
     * Get the student associated with the attendance
     */
    public function student()
    {
        return $this->belongsTo(students::class, 'student_id');
    }

    /**
     * Get the class associated with the attendance
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the teacher who marked the attendance
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the user who created the attendance record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope to filter by class
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope to filter by session
     */
    public function scopeForSession($query, $session)
    {
        return $query->where('session', $session);
    }
}
