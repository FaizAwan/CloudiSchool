<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsEvent extends Model
{
    use HasFactory;

    protected $table = 'cms_events';

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'content',
        'event_date',
        'location',
        'image',
        'status',
    ];

    protected $dates = ['event_date'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . rand(100, 999);
            }
        });
    }
}
