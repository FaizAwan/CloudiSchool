<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'interval',
        'max_students',
        'max_staff',
        'max_storage_size',
        'features',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array', // Assuming we store it as JSON
    ];
}
