<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsNews extends Model
{
    use HasFactory;

    protected $table = 'cms_news';

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'content',
        'image',
        'status',
    ];

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
