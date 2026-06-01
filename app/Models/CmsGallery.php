<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsGallery extends Model
{
    use HasFactory;

    protected $table = 'cms_gallery';

    protected $fillable = [
        'tenant_id',
        'title',
        'image',
        'category',
    ];
}
