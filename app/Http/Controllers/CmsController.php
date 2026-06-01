<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsNews;
use App\Models\CmsEvent;
use App\Models\CmsGallery;
use App\Models\CmsAnnouncement;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function newsIndex()
    {
        $news = CmsNews::where('tenant_id', Auth::user()->tenant_id)->latest()->paginate(10);
        return \view('cms.news.index', compact('news'));
    }

    public function newsCreate()
    {
        return \view('cms.news.create_edit');
    }

    public function newsStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->all();
        $data['tenant_id'] = Auth::user()->tenant_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cms/news', 'public');
        }

        CmsNews::create($data);
        return redirect()->route('cms.news.index')->with('success', 'News added successfully');
    }

    public function newsEdit($id)
    {
        $news = CmsNews::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        return \view('cms.news.create_edit', compact('news'));
    }

    public function newsUpdate(Request $request, $id)
    {
        $news = CmsNews::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);

        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('cms/news', 'public');
        }

        $news->update($data);
        return redirect()->route('cms.news.index')->with('success', 'News updated successfully');
    }

    public function newsDestroy($id)
    {
        $news = CmsNews::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();
        return redirect()->route('cms.news.index')->with('success', 'News deleted successfully');
    }

    public function eventsIndex()
    {
        $events = CmsEvent::where('tenant_id', Auth::user()->tenant_id)->latest()->paginate(10);
        return \view('cms.events.index', compact('events'));
    }

    public function eventsCreate()
    {
        return \view('cms.events.create_edit');
    }

    public function eventsStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'event_date' => 'required|date',
            'location' => 'required',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->all();
        $data['tenant_id'] = Auth::user()->tenant_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cms/events', 'public');
        }

        CmsEvent::create($data);
        return redirect()->route('cms.events.index')->with('success', 'Event added successfully');
    }

    public function eventsEdit($id)
    {
        $event = CmsEvent::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        return \view('cms.events.create_edit', compact('event'));
    }

    public function eventsUpdate(Request $request, $id)
    {
        $event = CmsEvent::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);

        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'event_date' => 'required|date',
            'location' => 'required',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('cms/events', 'public');
        }

        $event->update($data);
        return redirect()->route('cms.events.index')->with('success', 'Event updated successfully');
    }

    public function eventsDestroy($id)
    {
        $event = CmsEvent::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();
        return redirect()->route('cms.events.index')->with('success', 'Event deleted successfully');
    }

    public function galleryIndex()
    {
        $gallery = CmsGallery::where('tenant_id', Auth::user()->tenant_id)->latest()->paginate(10);
        return \view('cms.gallery.index', compact('gallery'));
    }

    public function galleryCreate()
    {
        return \view('cms.gallery.create_edit');
    }

    public function galleryStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'image' => 'required|image|max:2048',
        ]);

        $data = $request->all();
        $data['tenant_id'] = Auth::user()->tenant_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cms/gallery', 'public');
        }

        CmsGallery::create($data);
        return redirect()->route('cms.gallery.index')->with('success', 'Image added to gallery successfully');
    }

    public function galleryEdit($id)
    {
        $item = CmsGallery::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        return \view('cms.gallery.create_edit', compact('item'));
    }

    public function galleryUpdate(Request $request, $id)
    {
        $item = CmsGallery::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('cms/gallery', 'public');
        }

        $item->update($data);
        return redirect()->route('cms.gallery.index')->with('success', 'Gallery item updated successfully');
    }

    public function galleryDestroy($id)
    {
        $item = CmsGallery::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();
        return redirect()->route('cms.gallery.index')->with('success', 'Gallery item deleted successfully');
    }

    public function announcementsIndex()
    {
        $announcements = CmsAnnouncement::where('tenant_id', Auth::user()->tenant_id)->latest()->paginate(10);
        return \view('cms.announcements.index', compact('announcements'));
    }

    public function announcementsCreate()
    {
        return \view('cms.announcements.create_edit');
    }

    public function announcementsStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'type' => 'required|in:general,exam,holiday,urgent',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();
        $data['tenant_id'] = Auth::user()->tenant_id;

        CmsAnnouncement::create($data);
        return redirect()->route('cms.announcements.index')->with('success', 'Announcement added successfully');
    }

    public function announcementsEdit($id)
    {
        $announcement = CmsAnnouncement::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        return \view('cms.announcements.create_edit', compact('announcement'));
    }

    public function announcementsUpdate(Request $request, $id)
    {
        $announcement = CmsAnnouncement::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'type' => 'required|in:general,exam,holiday,urgent',
            'status' => 'required|in:active,inactive',
        ]);

        $announcement->update($request->all());
        return redirect()->route('cms.announcements.index')->with('success', 'Announcement updated successfully');
    }

    public function announcementsDestroy($id)
    {
        $item = CmsAnnouncement::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $item->delete();
        return redirect()->route('cms.announcements.index')->with('success', 'Announcement deleted successfully');
    }

    // Blogs management for School Admin
    public function blogsIndex()
    {
        $blogs = Blog::where('tenant_id', Auth::user()->tenant_id)->latest()->paginate(10);
        return \view('cms.blogs.index', compact('blogs'));
    }

    public function blogCreate()
    {
        return \view('cms.blogs.create_edit');
    }

    public function blogStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'author' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        Blog::create($data);

        return redirect()->route('cms.blogs.index')->with('success', 'Blog created successfully.');
    }

    public function blogEdit($id)
    {
        $blog = Blog::where('tenant_id', Auth::user()->tenant_id)->where('id', $id)->firstOrFail();
        return \view('cms.blogs.create_edit', compact('blog'));
    }

    public function blogUpdate(Request $request, $id)
    {
        $blog = Blog::where('tenant_id', Auth::user()->tenant_id)->where('id', $id)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'author' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);

        return redirect()->route('cms.blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function blogDestroy($id)
    {
        $blog = Blog::where('tenant_id', Auth::user()->tenant_id)->where('id', $id)->firstOrFail();
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();
        return redirect()->route('cms.blogs.index')->with('success', 'Blog deleted successfully.');
    }

    public function aiGenerateTitles(Request $request, \App\Services\OpenAIService $aiService)
    {
        $topic = $request->input('topic');
        $niche = $request->input('niche', 'Education');

        if (empty($topic)) {
            return response()->json(['success' => false, 'error' => 'Topic is required'], 400);
        }

        $result = $aiService->generateTitleSuggestions($topic, $niche);

        if (is_string($result)) {
            return response()->json(['success' => false, 'error' => $result], 500);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function aiGenerate(Request $request, \App\Services\OpenAIService $aiService)
    {
        $topic = $request->input('topic');
        $title = $request->input('title'); // Optional: if user selected a title
        $niche = $request->input('niche', 'Education');

        if (empty($topic)) {
            return response()->json(['success' => false, 'error' => 'Topic is required'], 400);
        }

        // If a specific title is provided, use it; otherwise use the topic
        $promptText = $title ? $title : $topic;

        $result = $aiService->generateBlogPost($promptText, $niche);

        if (is_string($result)) {
            return response()->json(['success' => false, 'error' => $result], 500);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function templatesIndex()
    {
        $school = \App\Models\School::where('id', Auth::user()->tenant_id)->firstOrFail();
        return \view('cms.templates.index', compact('school'));
    }

    public function updateTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required'
        ]);

        $school = \App\Models\School::where('id', Auth::user()->tenant_id)->firstOrFail();
        $school->update(['landing_template' => $request->template]);

        return redirect()->back()->with('success', 'Website template updated successfully!');
    }
}
