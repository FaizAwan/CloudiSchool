@extends('layouts.app')

@section('content')
<!-- Stitch Custom Styles -->
<style>
    :root {
        --stitch-navy: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #172554 100%);
        --stitch-gold: linear-gradient(to bottom, #f3d078 0%, #c9a041 50%, #8a6e2a 100%);
        --stitch-glass: rgba(255, 255, 255, 0.95);
        --stitch-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    .card-stitch {
        background: var(--stitch-glass);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 16px;
        box-shadow: var(--stitch-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-stitch {
        background: var(--stitch-navy);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        transition: all 0.3s ease;
    }
    .btn-stitch:hover {
        background: linear-gradient(135deg, #2563eb 0%, #172554 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(30, 64, 175, 0.4);
        color: white;
    }
    .pagetitle h1 {
        font-weight: 800;
        letter-spacing: -1px;
        background: var(--stitch-navy);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<div class="pagetitle mb-4">
    <h1>{{ isset($blog) ? 'Edit' : 'Create' }} Blog Post</h1>
    <nav>
        <ol class="breadcrumb mt-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item">CMS</li>
            <li class="breadcrumb-item"><a href="{{ route('cms.blogs.index') }}">Blogs</a></li>
            <li class="breadcrumb-item active">{{ isset($blog) ? 'Edit' : 'Create' }}</li>
        </ol>
    </nav>
</div>

<div class="container-fluid p-0">
            <div class="col-lg-10">
                <!-- AI Blog Generator Tool -->
                <div class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-robot text-primary me-2"></i> AI Blog Architect</h5>
                        <p class="text-muted small">Type a topic or few keywords below and let AI generate professional,
                            SEO-optimized blog titles and content for you instantly.</p>
                        
                        <!-- Step 1: Topic Input -->
                        <div id="step-topic">
                            <div class="input-group">
                                <input type="text" id="ai-topic" class="form-control py-2"
                                    placeholder="e.g. Importance of Sports in Schools">
                                <select id="ai-niche" class="form-select" style="max-width: 150px;">
                                    <option value="Education">Education</option>
                                    <option value="School Life">School Life</option>
                                    <option value="Teaching Tips">Teaching Tips</option>
                                    <option value="Students">Students</option>
                                </select>
                                <button class="btn btn-primary px-4" type="button" id="btn-get-titles">
                                    <span id="titles-loader" class="spinner-border spinner-border-sm me-2 d-none"
                                        role="status"></span>
                                    Get Title Suggestions
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Title Selection -->
                        <div id="step-titles" class="mt-4 d-none">
                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Choose a title:</strong> Select one of these AI-generated titles below
                            </div>
                            <div id="titles-container" class="mb-3">
                                <!-- Titles will be populated here -->
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-success px-4" type="button" id="btn-generate-content">
                                    <span id="content-loader" class="spinner-border spinner-border-sm me-2 d-none"
                                        role="status"></span>
                                    Generate Full Blog
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="btn-back-to-topic">
                                    <i class="fas fa-arrow-left me-1"></i> Change Topic
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <form action="{{ isset($blog) ? route('cms.blogs.update', $blog->id) : route('cms.blogs.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($blog))
                                @method('POST') {{-- Assuming the route is POST for update if not using PUT --}}
                            @endif

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Title</label>
                                        <input type="text" name="title" id="blog-title" class="form-control" required
                                            value="{{ old('title', $blog->title ?? '') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Author Name</label>
                                        <input type="text" name="author" class="form-control"
                                            value="{{ old('author', $blog->author ?? Auth::user()->name) }}">
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Content</label>
                                        <textarea name="content" id="editor"
                                            class="form-control">{{ old('content', $blog->content ?? '') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="published" {{ old('status', $blog->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                                            <option value="draft" {{ old('status', $blog->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Featured Image</label>
                                        @if(isset($blog) && $blog->image)
                                            <div class="mb-2">
                                                <img src="{{ Str::startsWith($blog->image, 'http') ? $blog->image : asset('storage/' . $blog->image) }}"
                                                    class="rounded shadow-sm img-fluid">
                                            </div>
                                        @endif
                                        <input type="file" name="image" class="form-control">
                                        <small class="text-muted">Max size: 2MB.</small>
                                    </div>

                                    <hr class="my-4">

                                    <h5 class="fw-bold mb-3 text-success"><i class="fas fa-search me-2"></i> SEO
                                        Optimization</h5>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">SEO Meta Title</label>
                                        <input type="text" name="meta_title" id="meta-title"
                                            class="form-control form-control-sm"
                                            value="{{ old('meta_title', $blog->meta_title ?? '') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">SEO Meta Description</label>
                                        <textarea name="meta_description" id="meta-description"
                                            class="form-control form-control-sm"
                                            rows="3">{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">SEO Keywords</label>
                                        <input type="text" name="meta_keywords" id="meta-keywords"
                                            class="form-control form-control-sm" placeholder="keyword1, keyword2"
                                            value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary py-3 fw-bold rounded-pill">
                                    {{ isset($blog) ? 'Update Post' : 'Publish Blog Post' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        let blogEditor;
        let selectedTitle = null;
        
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
            })
            .then(editor => {
                blogEditor = editor;
            })
            .catch(error => {
                console.error(error);
            });

        // Step 1: Get Title Suggestions
        document.getElementById('btn-get-titles').addEventListener('click', async function () {
            const topic = document.getElementById('ai-topic').value;
            const niche = document.getElementById('ai-niche').value;

            if (!topic) {
                alert('Please enter a topic first.');
                return;
            }

            const btn = this;
            const loader = document.getElementById('titles-loader');

            btn.disabled = true;
            loader.classList.remove('d-none');

            try {
                const response = await fetch('{{ route('cms.blogs.ai-generate-titles') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ topic, niche })
                });

                const result = await response.json();

                if (result.success && result.data.titles) {
                    displayTitles(result.data.titles, topic, niche);
                    // Show step 2, hide step 1
                    document.getElementById('step-topic').classList.add('d-none');
                    document.getElementById('step-titles').classList.remove('d-none');
                } else {
                    alert('Failed to generate titles: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred while generating titles.');
            } finally {
                btn.disabled = false;
                loader.classList.add('d-none');
            }
        });

        // Display titles as radio options
        function displayTitles(titles, topic, niche) {
            const container = document.getElementById('titles-container');
            container.innerHTML = '';

            titles.forEach((title, index) => {
                const div = document.createElement('div');
                div.className = 'form-check mb-2 p-3 border rounded hover-shadow';
                div.style.cursor = 'pointer';
                div.style.transition = 'all 0.2s';
                
                div.innerHTML = `
                    <input class="form-check-input" type="radio" name="selectedTitle" id="title${index}" value="${title}">
                    <label class="form-check-label w-100" for="title${index}" style="cursor: pointer;">
                        <strong>${index + 1}.</strong> ${title}
                    </label>
                `;
                
                // Add hover effect
                div.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                    this.style.borderColor = '#0d6efd';
                });
                div.addEventListener('mouseleave', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    if (!radio.checked) {
                        this.style.backgroundColor = '';
                        this.style.borderColor = '#dee2e6';
                    }
                });
                
                // Click anywhere on div to select
                div.addEventListener('click', function(e) {
                    if (e.target.tagName !== 'INPUT') {
                        const radio = this.querySelector('input[type="radio"]');
                        radio.checked = true;
                        selectedTitle = radio.value;
                        
                        // Update all divs styling
                        document.querySelectorAll('#titles-container > div').forEach(d => {
                            d.style.backgroundColor = '';
                            d.style.borderColor = '#dee2e6';
                        });
                        this.style.backgroundColor = '#e7f3ff';
                        this.style.borderColor = '#0d6efd';
                    }
                });
                
                // Radio change event
                const radio = div.querySelector('input[type="radio"]');
                radio.addEventListener('change', function() {
                    selectedTitle = this.value;
                    document.querySelectorAll('#titles-container > div').forEach(d => {
                        d.style.backgroundColor = '';
                        d.style.borderColor = '#dee2e6';
                    });
                    div.style.backgroundColor = '#e7f3ff';
                    div.style.borderColor = '#0d6efd';
                });
                
                container.appendChild(div);
            });

            // Store topic and niche for later use
            container.dataset.topic = topic;
            container.dataset.niche = niche;
        }

        // Step 2: Generate Full Content
        document.getElementById('btn-generate-content').addEventListener('click', async function () {
            if (!selectedTitle) {
                alert('Please select a title first.');
                return;
            }

            const container = document.getElementById('titles-container');
            const topic = container.dataset.topic;
            const niche = container.dataset.niche;

            const btn = this;
            const loader = document.getElementById('content-loader');

            btn.disabled = true;
            loader.classList.remove('d-none');

            try {
                const response = await fetch('{{ route('cms.blogs.ai-generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        topic: topic,
                        title: selectedTitle,
                        niche: niche 
                    })
                });

                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('blog-title').value = data.title || selectedTitle;
                    document.getElementById('meta-title').value = data.meta_title;
                    document.getElementById('meta-description').value = data.meta_description;
                    document.getElementById('meta-keywords').value = data.meta_keywords;

                    if (blogEditor) {
                        blogEditor.setData(data.content);
                    } else {
                        document.getElementById('editor').value = data.content;
                    }

                    // Scroll to title
                    document.getElementById('blog-title').scrollIntoView({ behavior: 'smooth' });
                    
                    // Show success message
                    alert('✅ Blog content generated successfully! Review and publish when ready.');
                } else {
                    alert('AI Generation failed: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred during AI generation.');
            } finally {
                btn.disabled = false;
                loader.classList.add('d-none');
            }
        });

        // Back button to change topic
        document.getElementById('btn-back-to-topic').addEventListener('click', function() {
            document.getElementById('step-titles').classList.add('d-none');
            document.getElementById('step-topic').classList.remove('d-none');
            selectedTitle = null;
        });
    </script>
@endsection