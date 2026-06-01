<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class StudentExamAttempt extends Model
{
    use HasFactory;

    protected $table = 'student_exam_attempts';

    protected $fillable = [
        'exam_id',
        'student_id',
        'attempt_number',
        'start_time',
        'end_time',
        'duration_taken',
        'total_questions',
        'attempted_questions',
        'correct_answers',
        'wrong_answers',
        'total_marks_obtained',
        'percentage',
        'grade',
        'status',
        'ip_address',
        'browser_info'
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_taken' => 'integer',
        'total_questions' => 'integer',
        'attempted_questions' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'total_marks_obtained' => 'decimal:2',
        'percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(students::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class, 'attempt_id');
    }

    public function result()
    {
        return $this->hasOne(ExamResult::class, 'attempt_id');
    }

    // Scopes
    public function scopeStarted($query)
    {
        return $query->where('status', 'started');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeAutoSubmitted($query)
    {
        return $query->where('status', 'auto_submitted');
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    // Helper methods
    public function isStarted()
    {
        return $this->status === 'started';
    }

    public function isSubmitted()
    {
        return in_array($this->status, ['submitted', 'auto_submitted']);
    }

    public function isGraded()
    {
        return $this->status === 'graded';
    }

    public function isAutoSubmitted()
    {
        return $this->status === 'auto_submitted';
    }

    public function getRemainingTimeAttribute()
    {
        if ($this->isStarted() && $this->start_time && $this->exam->duration_minutes) {
            $endTime = $this->start_time->addMinutes($this->exam->duration_minutes);
            if (now() < $endTime) {
                return now()->diffInMinutes($endTime);
            }
        }
        return 0;
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_taken / 60);
        $minutes = $this->duration_taken % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    public function getUnattemptedQuestionsAttribute()
    {
        return $this->total_questions - $this->attempted_questions;
    }

    public function getAccuracyPercentageAttribute()
    {
        if ($this->attempted_questions > 0) {
            return ($this->correct_answers / $this->attempted_questions) * 100;
        }
        return 0;
    }

    public function isPassed()
    {
        return $this->percentage >= $this->exam->passing_percentage;
    }

    public function calculateGrade()
    {
        // Get grading scale from database
        $gradingScale = \DB::table('grading_scale')
            ->where('min_percentage', '<=', $this->percentage)
            ->where('max_percentage', '>=', $this->percentage)
            ->first();
            
        return $gradingScale ? $gradingScale->grade : 'F';
    }
}
