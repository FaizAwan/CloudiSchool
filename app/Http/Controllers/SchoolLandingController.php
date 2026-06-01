<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\CmsNews;
use App\Models\CmsEvent;
use App\Models\CmsGallery;
use App\Models\CmsAnnouncement;
use App\Models\Blog;
use App\Models\Tenant;

class SchoolLandingController extends Controller
{
    public function show($school_slug)
    {
        // Find by slug for SEO friendly URLs
        $school = School::where('slug', $school_slug)->firstOrFail();

        $news = CmsNews::where('tenant_id', $school->tenant_id)->where('status', 'published')->latest()->take(3)->get();
        $events = CmsEvent::where('tenant_id', $school->tenant_id)->where('status', 'published')->latest()->take(3)->get();
        $blogs = Blog::where('tenant_id', $school->tenant_id)->where('status', 'published')->latest()->take(3)->get();
        $gallery = CmsGallery::where('tenant_id', $school->tenant_id)->latest()->take(6)->get();
        $announcements = CmsAnnouncement::where('tenant_id', $school->tenant_id)->where('status', 'active')->latest()->get();

        $template = $school->landing_template ?? 'modern_classic';
        $viewName = "school_landing.templates.{$template}.index";

        if (!view()->exists($viewName)) {
            $viewName = 'school_landing.index';
        }

        return \view($viewName, compact('school', 'news', 'events', 'blogs', 'gallery', 'announcements'));
    }

    public function news($school_slug)
    {
        $school = School::where('slug', $school_slug)->firstOrFail();
        $news = CmsNews::where('tenant_id', $school->tenant_id)->where('status', 'published')->latest()->paginate(12);
        return \view('school_landing.news', compact('school', 'news'));
    }

    public function showNews($school_slug, $news_slug)
    {
        $school = School::where('slug', $school_slug)->firstOrFail();
        $item = CmsNews::where('slug', $news_slug)->where('tenant_id', $school->tenant_id)->firstOrFail();
        return \view('school_landing.show-item', compact('school', 'item'))->with('type', 'news');
    }

    public function events($school_slug)
    {
        $school = School::where('slug', $school_slug)->firstOrFail();
        $events = CmsEvent::where('tenant_id', $school->tenant_id)->where('status', 'published')->latest()->paginate(12);
        return \view('school_landing.events', compact('school', 'events'));
    }

    public function showEvent($school_slug, $event_slug)
    {
        $school = School::where('slug', $school_slug)->firstOrFail();
        $item = CmsEvent::where('slug', $event_slug)->where('tenant_id', $school->tenant_id)->firstOrFail();
        return \view('school_landing.show-item', compact('school', 'item'))->with('type', 'event');
    }

    public function blogs($school_slug)
    {
        $school = School::where('slug', $school_slug)->firstOrFail();
        $blogs = Blog::where('tenant_id', $school->tenant_id)->where('status', 'published')->latest()->paginate(12);
        return \view('school_landing.blogs', compact('school', 'blogs'));
    }

    public function showBlog($school_slug, $blog_slug)
    {
        $school = School::where('slug', $school_slug)->firstOrFail();
        $item = Blog::where('slug', $blog_slug)->where('tenant_id', $school->tenant_id)->firstOrFail();
        return \view('school_landing.show-item', compact('school', 'item'))->with('type', 'blog');
    }

    public function gallery($school_slug)
    {
        $school = School::where('slug', $school_slug)->firstOrFail();
        $gallery = CmsGallery::where('tenant_id', $school->tenant_id)->latest()->paginate(16);
        return \view('school_landing.gallery', compact('school', 'gallery'));
    }

    public function contact($school_slug)
    {
        $school = School::where('slug', $school_slug)->firstOrFail();
        return \view('school_landing.contact', compact('school'));
    }
}
