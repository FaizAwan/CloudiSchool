<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class Exam extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    protected $table = 'exams';

    protected $fillable = [
        'school_id',
        'session',
        'exam_name',
        'exam_type_id',
        'class_id',
        'class_name',
        'subject_id',
        'teacher_id',
        'exam_date',
        'exam_time',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'total_questions',
        'mcq_questions',
        'short_questions',
        'long_questions',
        'instructions',
        'status',
        'auto_submit',
        'show_results',
        'randomize_questions',
        'created_by'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'exam_time' => 'datetime',
        'duration_minutes' => 'integer',
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
        'total_questions' => 'integer',
        'mcq_questions' => 'integer',
        'short_questions' => 'integer',
        'long_questions' => 'integer',
        'auto_submit' => 'boolean',
        'show_results' => 'boolean',
        'randomize_questions' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(teachers::class, 'teacher_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id');
    }

    public function attempts()
    {
        return $this->hasMany(StudentExamAttempt::class, 'exam_id');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }

    public function analytics()
    {
        return $this->hasOne(ExamAnalytics::class, 'exam_id');
    }

    public function notifications()
    {
        return $this->hasMany(ParentNotification::class, 'exam_id');
    }

    public function reports()
    {
        return $this->hasMany(ExamReport::class, 'exam_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>=', now()->toDateString())
                    ->where('status', 'published');
    }

    public function scopeToday($query)
    {
        return $query->where('exam_date', now()->toDateString());
    }

    // Helper methods
    public function getPassingPercentageAttribute()
    {
        return ($this->passing_marks / $this->total_marks) * 100;
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function canBeAttempted()
    {
        return $this->isPublished() && 
               $this->exam_date >= now()->toDateString();
    }

    public function getRemainingTimeAttribute()
    {
        if ($this->exam_date && $this->exam_time) {
            $examDateTime = $this->exam_date->format('Y-m-d') . ' ' . $this->exam_time->format('H:i:s');
            $examEnd = \Carbon\Carbon::parse($examDateTime)->addMinutes($this->duration_minutes);
            
            if (now() < $examEnd) {
                return now()->diffInMinutes($examEnd);
            }
        }
        return 0;
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }
}
