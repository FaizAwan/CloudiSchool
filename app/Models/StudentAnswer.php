<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $table = 'student_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'question_type',
        'selected_option',
        'answer_text',
        'is_correct',
        'marks_obtained',
        'teacher_remarks',
        'answered_at',
        'graded_at',
        'graded_by'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'marks_obtained' => 'decimal:2',
        'answered_at' => 'datetime',
        'graded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    // Relationships
    public function attempt()
    {
        return $this->belongsTo(StudentExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Scopes
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('graded_at');
    }

    public function scopeUngraded($query)
    {
        return $query->whereNull('graded_at');
    }

    public function scopeMcq($query)
    {
        return $query->where('question_type', 'mcq');
    }

    public function scopeShort($query)
    {
        return $query->where('question_type', 'short');
    }

    public function scopeLong($query)
    {
        return $query->where('question_type', 'long');
    }

    public function scopeForAttempt($query, $attemptId)
    {
        return $query->where('attempt_id', $attemptId);
    }

    // Helper methods
    public function isCorrect()
    {
        return $this->is_correct === true;
    }

    public function isIncorrect()
    {
        return $this->is_correct === false;
    }

    public function isGraded()
    {
        return !is_null($this->graded_at);
    }

    public function needsGrading()
    {
        return is_null($this->graded_at) && 
               in_array($this->question_type, ['short', 'long']);
    }

    public function getSelectedOptionText()
    {
        if ($this->question_type === 'mcq' && $this->selected_option) {
            $option = $this->question->mcqOptions()
                         ->where('option_letter', $this->selected_option)
                         ->first();
            return $option ? $option->option_text : null;
        }
        return null;
    }

    public function getCorrectOptionText()
    {
        if ($this->question_type === 'mcq') {
            $correctOption = $this->question->mcqOptions()
                               ->where('is_correct', true)
                               ->first();
            return $correctOption ? $correctOption->option_text : null;
        }
        return null;
    }

    public function autoGradeMcq()
    {
        if ($this->question_type === 'mcq' && $this->selected_option) {
            $correctOption = $this->question->mcqOptions()
                               ->where('is_correct', true)
                               ->first();
            
            if ($correctOption) {
                $this->is_correct = ($this->selected_option === $correctOption->option_letter);
                $this->marks_obtained = $this->is_correct ? $this->question->marks : 0;
                $this->graded_at = now();
                $this->save();
                
                return true;
            }
        }
        return false;
    }

    public function getFormattedAnswerAttribute()
    {
        switch ($this->question_type) {
            case 'mcq':
                return $this->selected_option ? 
                       "Option {$this->selected_option}: " . $this->getSelectedOptionText() : 
                       'No answer selected';
            
            case 'true_false':
                return $this->selected_option === 'A' ? 'True' : 'False';
            
            case 'short':
            case 'long':
            case 'fill_blank':
                return $this->answer_text ?: 'No answer provided';
            
            default:
                return 'Unknown answer type';
        }
    }
}
