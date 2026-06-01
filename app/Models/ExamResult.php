<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class ExamResult extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    protected $table = 'exam_results';

    protected $fillable = [
        'exam_id',
        'student_id',
        'attempt_id',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'position',
        'remarks',
        'status',
        'graded_by',
        'graded_at'
    ];

    protected $casts = [
        'total_marks' => 'integer',
        'obtained_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'position' => 'integer',
        'graded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(students::class, 'student_id');
    }

    public function attempt()
    {
        return $this->belongsTo(StudentExamAttempt::class, 'attempt_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Scopes
    public function scopePassed($query)
    {
        return $query->where('status', 'pass');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'fail');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeOrderByPosition($query)
    {
        return $query->orderBy('position');
    }

    public function scopeOrderByPercentage($query)
    {
        return $query->orderBy('percentage', 'desc');
    }

    // Helper methods
    public function isPassed()
    {
        return $this->status === 'pass';
    }

    public function isFailed()
    {
        return $this->status === 'fail';
    }

    public function isAbsent()
    {
        return $this->status === 'absent';
    }

    public function getGradeColorAttribute()
    {
        $gradeColors = [
            'A+' => 'success',
            'A' => 'success',
            'B+' => 'info',
            'B' => 'info',
            'C+' => 'warning',
            'C' => 'warning',
            'D' => 'warning',
            'F' => 'danger'
        ];

        return $gradeColors[$this->grade] ?? 'secondary';
    }

    public function getStatusColorAttribute()
    {
        return $this->isPassed() ? 'success' : ($this->isAbsent() ? 'secondary' : 'danger');
    }

    public function getFormattedPositionAttribute()
    {
        if (!$this->position) return 'N/A';
        
        $suffix = 'th';
        $lastDigit = $this->position % 10;
        $lastTwoDigits = $this->position % 100;
        
        if ($lastDigit == 1 && $lastTwoDigits != 11) {
            $suffix = 'st';
        } elseif ($lastDigit == 2 && $lastTwoDigits != 12) {
            $suffix = 'nd';
        } elseif ($lastDigit == 3 && $lastTwoDigits != 13) {
            $suffix = 'rd';
        }
        
        return $this->position . $suffix;
    }

    public function calculatePosition()
    {
        $position = ExamResult::where('exam_id', $this->exam_id)
                              ->where('percentage', '>', $this->percentage)
                              ->count() + 1;
        
        $this->position = $position;
        $this->save();
        
        return $position;
    }

    public static function calculateAllPositions($examId)
    {
        $results = self::where('exam_id', $examId)
                      ->orderBy('percentage', 'desc')
                      ->get();
        
        foreach ($results as $index => $result) {
            $result->position = $index + 1;
            $result->save();
        }
    }

    public function getGradeRemarks()
    {
        $gradingScale = \DB::table('grading_scale')
            ->where('grade', $this->grade)
            ->first();
            
        return $gradingScale ? $gradingScale->remarks : '';
    }

    public function generateRemarks()
    {
        $remarks = [];
        
        if ($this->isPassed()) {
            if ($this->percentage >= 90) {
                $remarks[] = "Outstanding performance!";
            } elseif ($this->percentage >= 80) {
                $remarks[] = "Excellent work!";
            } elseif ($this->percentage >= 70) {
                $remarks[] = "Very good effort!";
            } else {
                $remarks[] = "Good job!";
            }
        } else {
            $remarks[] = "Needs improvement. Please work harder.";
        }
        
        if ($this->position <= 3) {
            $remarks[] = "Congratulations on achieving top position!";
        }
        
        return implode(' ', $remarks);
    }
}
