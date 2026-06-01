<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class QuestionBankOption extends Model
{
    use HasFactory;

    protected $table = 'question_bank_options';

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);

        static::creating(function (self $model) {
            // Auto-assign option_letter (A, B, C, ...) if not provided
            if (empty($model->option_letter) && !empty($model->question_id)) {
                $existingCount = static::where('question_id', $model->question_id)->count();
                $letterIndex = $existingCount; // 0-based
                $model->option_letter = chr(65 + ($letterIndex % 26));
            }
        });
    }

    protected $fillable = [
        'question_id',
        'option_letter',
        'option_text',
        'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
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
}
