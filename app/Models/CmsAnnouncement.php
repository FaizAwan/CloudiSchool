<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsAnnouncement extends Model
{
    use HasFactory;

    protected $table = 'cms_announcements';

    protected $fillable = [
        'tenant_id',
        'title',
        'content',
        'type',
        'status',
    ];
}
