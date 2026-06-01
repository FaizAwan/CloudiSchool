<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $table = 'teacher_attendances';

    protected $fillable = [
        'teacher_id',
        'date',
        'status',
        'remarks',
        'school_id',
        'marked_by'
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
     * Get the teacher associated with the attendance
     */
    public function teacher()
    {
        return $this->belongsTo(teachers::class, 'teacher_id');
    }

    /**
     * Get the user who marked the attendance
     */
    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Scope to filter by date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope to filter by teacher
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
}
