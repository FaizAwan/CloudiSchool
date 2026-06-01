<?php

declare(strict_types = 1)
;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\School;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/* //-------------------------------------------------------------------------- // Tenant Routes //-------------------------------------------------------------------------- // // Here you can register the tenant routes for your application. // These routes are loaded by the TenantRouteServiceProvider. // // Feel free to customize them however you want. Good luck! // */

$tenancyInit = app()->environment('local') ?InitializeTenancyByPath::class : InitializeTenancyByDomain::class;
$tenantMiddlewares = ['web', $tenancyInit];
if (!app()->environment('local')) {
    $tenantMiddlewares[] = PreventAccessFromCentralDomains::class;
}

Route::middleware($tenantMiddlewares)
    ->prefix(app()->environment('local') ? '/t/{tenant}' : '')
    ->group(function () {
        // Tenant auth routes with name prefix to avoid name collisions with central routes
        Route::group(['as' => 'tenant.'], function () {
            Auth::routes();
        }
        );

        Route::get('/dashboard', function () {
            $dbName = DB::connection()->getDatabaseName();

            // Build central billing portal URL for this tenant (if known)
            $billingUrl = null;
            try {
                $tenantId = (string)tenant('id');
                $school = School::where('tenant_id', $tenantId)->first();
                if ($school) {
                    $central = rtrim(config('app.url'), '/');
                    $billingUrl = $central . '/saas-admin/tenants/' . $school->id . '/billing-portal';
                }
            }
            catch (\Throwable $e) {
            }

            try {
                $tenantId = (string)tenant('id');
                Cache::put('tenant_online_' . $tenantId, now(), now()->addMinutes(5));
            }
            catch (\Throwable $e) {
            }

            return view('tenant.dashboard', [
            'tenantId' => tenant('id'),
            'dbName' => $dbName,
            'billingUrl' => $billingUrl,
            ]);
        }
        )->name('tenant.dashboard');

        // CMS MANAGEMENT
        Route::group(['prefix' => 'cms', 'as' => 'cms.'], function () {
            // News
            Route::get('/news', [App\Http\Controllers\CmsController::class , 'newsIndex'])->name('news.index');
            Route::get('/news/create', [App\Http\Controllers\CmsController::class , 'newsCreate'])->name('news.create');
            Route::post('/news/store', [App\Http\Controllers\CmsController::class , 'newsStore'])->name('news.store');
            Route::get('/news/edit/{id}', [App\Http\Controllers\CmsController::class , 'newsEdit'])->name('news.edit');
            Route::post('/news/update/{id}', [App\Http\Controllers\CmsController::class , 'newsUpdate'])->name('news.update');
            Route::delete('/news/delete/{id}', [App\Http\Controllers\CmsController::class , 'newsDestroy'])->name('news.destroy');

            // Events
            Route::get('/events', [App\Http\Controllers\CmsController::class , 'eventsIndex'])->name('events.index');
            Route::get('/events/create', [App\Http\Controllers\CmsController::class , 'eventsCreate'])->name('events.create');
            Route::post('/events/store', [App\Http\Controllers\CmsController::class , 'eventsStore'])->name('events.store');
            Route::get('/events/edit/{id}', [App\Http\Controllers\CmsController::class , 'eventsEdit'])->name('events.edit');
            Route::post('/events/update/{id}', [App\Http\Controllers\CmsController::class , 'eventsUpdate'])->name('events.update');
            Route::delete('/events/delete/{id}', [App\Http\Controllers\CmsController::class , 'eventsDestroy'])->name('events.destroy');

            // Gallery
            Route::get('/gallery', [App\Http\Controllers\CmsController::class , 'galleryIndex'])->name('gallery.index');
            Route::get('/gallery/create', [App\Http\Controllers\CmsController::class , 'galleryCreate'])->name('gallery.create');
            Route::post('/gallery/store', [App\Http\Controllers\CmsController::class , 'galleryStore'])->name('gallery.store');
            Route::get('/gallery/edit/{id}', [App\Http\Controllers\CmsController::class , 'galleryEdit'])->name('gallery.edit');
            Route::post('/gallery/update/{id}', [App\Http\Controllers\CmsController::class , 'galleryUpdate'])->name('gallery.update');
            Route::delete('/gallery/delete/{id}', [App\Http\Controllers\CmsController::class , 'galleryDestroy'])->name('gallery.destroy');

            // Announcements
            Route::get('/announcements', [App\Http\Controllers\CmsController::class , 'announcementsIndex'])->name('announcements.index');
            Route::get('/announcements/create', [App\Http\Controllers\CmsController::class , 'announcementsCreate'])->name('announcements.create');
            Route::post('/announcements/store', [App\Http\Controllers\CmsController::class , 'announcementsStore'])->name('announcements.store');
            Route::get('/announcements/edit/{id}', [App\Http\Controllers\CmsController::class , 'announcementsEdit'])->name('announcements.edit');
            Route::put('/announcements/update/{id}', [App\Http\Controllers\CmsController::class , 'announcementsUpdate'])->name('announcements.update');
            Route::delete('/announcements/delete/{id}', [App\Http\Controllers\CmsController::class , 'announcementsDestroy'])->name('announcements.destroy');

            // Blogs Management
            Route::get('/blogs', [App\Http\Controllers\CmsController::class , 'blogsIndex'])->name('blogs.index');
            Route::get('/blogs/create', [App\Http\Controllers\CmsController::class , 'blogCreate'])->name('blogs.create');
            Route::post('/blogs/store', [App\Http\Controllers\CmsController::class , 'blogStore'])->name('blogs.store');
            Route::get('/blogs/edit/{id}', [App\Http\Controllers\CmsController::class , 'blogEdit'])->name('blogs.edit');
            Route::post('/blogs/update/{id}', [App\Http\Controllers\CmsController::class , 'blogUpdate'])->name('blogs.update');
            Route::delete('/blogs/delete/{id}', [App\Http\Controllers\CmsController::class , 'blogDestroy'])->name('blogs.destroy');
            Route::post('/blogs/ai-generate-titles', [App\Http\Controllers\CmsController::class , 'aiGenerateTitles'])->name('blogs.ai-generate-titles');
            Route::post('/blogs/ai-generate', [App\Http\Controllers\CmsController::class , 'aiGenerate'])->name('blogs.ai-generate');

            // Templates
            Route::get('/templates', [App\Http\Controllers\CmsController::class , 'templatesIndex'])->name('templates.index');
            Route::post('/templates/update', [App\Http\Controllers\CmsController::class , 'updateTemplate'])->name('templates.update');
        }
        );
    });
