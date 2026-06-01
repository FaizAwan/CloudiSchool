<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Exam;
use App\Policies\ExamPolicy;
use App\Models\QuestionBank;
use App\Policies\QuestionBankPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Exam' => 'App\Policies\ExamPolicy',
        'App\Models\QuestionBank' => 'App\Policies\QuestionBankPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
