<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'tenant_id',
        'school_id',
        'subject_name',
        'subject_code',
        'class_id',
        'term',
        'term_marks',
        'total_marks',
        'passing_marks',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'total_marks' => 'float',
        'passing_marks' => 'float',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    // Relationships
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'subject_id');
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassExamSchedule::class, 'subject_id');
    }

    public function questionBank()
    {
        return $this->hasMany(QuestionBank::class, 'subject_id');
    }

    public function studentProgress()
    {
        return $this->hasMany(StudentProgress::class, 'subject_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    // Helper methods
    public function getPassingPercentageAttribute()
    {
        if ($this->total_marks > 0) {
            return ($this->passing_marks / $this->total_marks) * 100;
        }
        return 0;
    }
}
