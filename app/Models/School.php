<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Laravel\Cashier\Billable;

class School extends Model
{
    use CentralConnection, Billable;

    protected $fillable = [
        'schoolName',
        'slug',
        'schoolCity',
        'address',
        'schoolAdminName',
        'schoolAdminEmail',
        'schoolAdminPassword',
        'status',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'domain',
        'database_name',
        'tenant_id',
        'whatsapp_number',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'school_logo',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'landing_template',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($school) {
            if (empty($school->slug)) {
                $school->slug = \Illuminate\Support\Str::slug($school->schoolName);
            }
        });
        static::updating(function ($school) {
            if (empty($school->slug)) {
                $school->slug = \Illuminate\Support\Str::slug($school->schoolName);
            }
        });
    }

    protected $hidden = [
        'schoolAdminPassword',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Get users belonging to this school
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
