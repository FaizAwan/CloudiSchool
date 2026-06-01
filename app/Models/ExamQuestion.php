<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class ExamQuestion extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    protected $table = 'exam_questions';

    protected $fillable = [
        'exam_id',
        'question_bank_id',
        'question_number',
        'question_type',
        'question_text',
        'question_image',
        'marks',
        'difficulty_level',
        'explanation',
        'status'
    ];

    protected $casts = [
        'question_number' => 'integer',
        'marks' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function mcqOptions()
    {
        return $this->hasMany(McqOption::class, 'question_id');
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class, 'question_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopeOrderByNumber($query)
    {
        return $query->orderBy('question_number');
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

    public function hasImage()
    {
        return !empty($this->question_image);
    }

    public function getImageUrl()
    {
        if ($this->hasImage()) {
            return asset('storage/exam_questions/' . $this->question_image);
        }
        return null;
    }
}
