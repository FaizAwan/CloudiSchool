@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('superadmin.blogs.index') }}">Blogs</a></li>
                <li class="breadcrumb-item active">{{ isset($blog) ? 'Edit' : 'Create' }} Blog</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- AI Blog Generator Tool -->
                <div class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-robot text-primary me-2"></i> AI Blog Architect</h5>
                        <p class="text-muted small">Type a topic or few keywords below and let AI generate a professional,
                            SEO-optimized blog for you instantly.</p>
                        <div class="input-group">
                            <input type="text" id="ai-topic" class="form-control py-2"
                                placeholder="e.g. Benefits of Digital Schools in 2026">
                            <select id="ai-niche" class="form-select" style="max-width: 150px;">
                                <option value="Education">Education</option>
                                <option value="Technology">Technology</option>
                                <option value="School Management">School Management</option>
                                <option value="Parenting">Parenting</option>
                            </select>
                            <button class="btn btn-primary px-4" type="button" id="btn-generate-ai">
                                <span id="ai-loader" class="spinner-border spinner-border-sm me-2 d-none"
                                    role="status"></span>
                                Generate with AI
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4">{{ isset($blog) ? 'Edit' : 'Create New' }} Blog Post</h2>

                        <form
                            action="{{ isset($blog) ? route('superadmin.blogs.update', $blog->id) : route('superadmin.blogs.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($blog))
                                @method('PUT')
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
                                            value="{{ old('author', $blog->author ?? 'CloudiSchool Team') }}">
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
                                        <small class="text-muted">Max size: 2MB. Recommended: 1200x600px</small>
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
                                <button type="submit" class="btn btn-dark py-3 fw-bold rounded-pill">
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

        document.getElementById('btn-generate-ai').addEventListener('click', async function () {
            const topic = document.getElementById('ai-topic').value;
            const niche = document.getElementById('ai-niche').value;

            if (!topic) {
                alert('Please enter a topic first.');
                return;
            }

            const btn = this;
            const loader = document.getElementById('ai-loader');

            btn.disabled = true;
            loader.classList.remove('d-none');

            try {
                const response = await fetch('{{ route('superadmin.blogs.ai-generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ topic, niche })
                });

                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('blog-title').value = data.title;
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
    </script>
@endsection