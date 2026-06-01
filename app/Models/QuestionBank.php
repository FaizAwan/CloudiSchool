<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class QuestionBank extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    protected $table = 'question_bank';

    protected $fillable = [
        'school_id',
        'subject_id',
        'class_level',
        'question_text',
        'question_type',
        'difficulty_level',
        'marks',
        'default_marks',
        'correct_answer',
        'topic',
        'chapter',
        'explanation',
        'tags',
        'created_by',
        'usage_count',
        'status'
    ];

    protected $casts = [
        'marks' => 'integer',
        'default_marks' => 'integer',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mcqOptions()
    {
        return $this->hasMany(QuestionBankOption::class, 'question_id');
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

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForClass($query, $classLevel)
    {
        return $query->where('class_level', $classLevel);
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    // Helper methods
    public function isMcq()
    {
        return $this->question_type === 'mcq';
    }

    public function isShort()
    {
        return $this->question_type === 'short';
    }

    public function isLong()
    {
        return $this->question_type === 'long';
    }

    public function isTrueFalse()
    {
        return $this->question_type === 'true_false';
    }

    public function isFillBlank()
    {
        return $this->question_type === 'fill_blank';
    }

    public function getCorrectOption()
    {
        if ($this->isMcq()) {
            return $this->mcqOptions()->where('is_correct', true)->first();
        }
        return null;
    }

    public function getDifficultyLevelColorAttribute()
    {
        $colors = [
            'easy' => 'success',
            'medium' => 'warning',
            'hard' => 'danger'
        ];
        
        return $colors[$this->difficulty_level] ?? 'secondary';
    }
}
