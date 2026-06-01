<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantOwned;

class ExamType extends Model
{
    use HasFactory;

    protected $table = 'exam_types';

    protected $fillable = [
        'school_id',
        'exam_type_name',
        'description',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantOwned);
        TenantOwned::bootFor(new static);
    }

    // Relationships
    public function exams()
    {
        return $this->hasMany(Exam::class, 'exam_type_id');
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassExamSchedule::class, 'exam_type_id');
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
}
