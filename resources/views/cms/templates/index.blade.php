@extends('layouts.app')

@section('content')
<style>
    :root {
        --tpl-primary: #4f46e5;
        --tpl-active: #4338ca;
        --tpl-bg-soft: #f8fafc;
    }

    .pagetitle h1 {
        font-weight: 800;
        color: #1e293b;
    }

    .template-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .template-card {
        background: white !important;
        border-radius: 20px !important;
        overflow: hidden !important;
        border: 2px solid transparent !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: flex !important;
        flex-direction: column !important;
        height: 100% !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }

    .template-card:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15) !important;
    }

    .template-preview {
        position: relative !important;
        min-height: 200px !important;
        background: #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .template-preview img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
    }

    .placeholder-icon {
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        font-size: 3rem !important;
        color: #cbd5e1 !important;
        display: none !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    .placeholder-icon span {
        font-size: 0.8rem;
        margin-top: 10px;
        font-weight: 600;
    }

    .template-overlay {
        position: absolute !important;
        inset: 0 !important;
        background: rgba(30, 41, 59, 0.4) !important;
        opacity: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: opacity 0.3s ease !important;
    }

    .template-card:hover .template-overlay {
        opacity: 1 !important;
    }

    .template-info {
        padding: 1.25rem !important;
        text-align: center !important;
        flex-grow: 1 !important;
    }

    .template-info h3 {
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        margin-bottom: 0.5rem !important;
    }

    .template-info p {
        font-size: 0.85rem !important;
        color: #64748b !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="pagetitle">
    <h1>Design & Branding</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item">CMS</li>
            <li class="breadcrumb-item active">Website Templates</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row align-items-center mb-4">
                <div class="col-lg-12">
                    <h2 class="fw-bold text-dark">Website Theme Selection</h2>
                    <p class="text-muted">Personalize your landing page in one click. All themes are fully responsive and optimized for speed.</p>
                </div>
            </div>

            <form action="{{ route('cms.templates.update') }}" method="POST" id="templateForm">
                @csrf
                <input type="hidden" name="template" id="selectedTemplateInput" value="{{ $school->landing_template ?? 'modern_classic' }}">

                <div class="template-grid">
                    @php
                        $templates = [
                            ['id' => 'modern_classic', 'name' => 'Modern Classic', 'desc' => 'Traditional, informative, and trust-building.', 'img' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&q=80'],
                            ['id' => 'vibrant_creative', 'name' => 'Vibrant Creative', 'desc' => 'Best for activity-focused and modern education.', 'img' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&q=80'],
                            ['id' => 'academic_excellence', 'name' => 'Academic Excellence', 'desc' => 'High-end aesthetic for elite institutions.', 'img' => 'https://images.unsplash.com/photo-1427504746696-ea5abd73a3bd?w=600&q=80'],
                            ['id' => 'royal_academy', 'name' => 'Royal Academy', 'desc' => 'Luxury and heritage with a focus on success.', 'img' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=600&q=80'],
                            ['id' => 'tech_scholars', 'name' => 'Tech Scholars', 'desc' => 'Stem and IT focused tech-forward design.', 'img' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&q=80'],
                            ['id' => 'nature_montessori', 'name' => 'Nature Montessori', 'desc' => 'Greenery and organic feel for kids growth.', 'img' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?w=600&q=80'],
                            ['id' => 'classic_heritage', 'name' => 'Classic Heritage', 'desc' => 'Rich history and traditional values.', 'img' => 'https://images.unsplash.com/photo-1590407604675-927bb4eb3e9d?w=600&q=80'],
                            ['id' => 'skyline_high', 'name' => 'Skyline High', 'desc' => 'Bold city school look with blue themes.', 'img' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&q=80'],
                            ['id' => 'artistic_soul', 'name' => 'Artistic Soul', 'desc' => 'Creative soul for arts and talent focus.', 'img' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=600&q=80'],
                            ['id' => 'global_vision', 'name' => 'Global Vision', 'desc' => 'International curriculum style design.', 'img' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&q=80'],
                            ['id' => 'sports_academy', 'name' => 'Sports Academy', 'desc' => 'Energetic red for active sports focus.', 'img' => 'https://images.unsplash.com/photo-1526304640151-b5195e8392a9?w=600&q=80'],
                            ['id' => 'kinder_garden', 'name' => 'Kinder Garden', 'desc' => 'Soft pastels for early childcare centers.', 'img' => 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600&q=80'],
                        ];
                    @endphp

                    @foreach($templates as $tpl)
                        <div class="template-card {{ ($school->landing_template == $tpl['id'] || (!$school->landing_template && $tpl['id'] == 'modern_classic')) ? 'active' : '' }}" data-id="{{ $tpl['id'] }}">
                            <div class="template-preview">
                                <div class="placeholder-icon">
                                    <i class="bi bi-image"></i>
                                    <span>Template Preview</span>
                                </div>
                                <img src="{{ $tpl['img'] }}" alt="{{ $tpl['name'] }}" loading="lazy" onload="this.style.opacity=1" onerror="handleImageError(this)">
                                <div class="template-overlay">
                                    <button type="button" class="btn btn-light rounded-pill btn-sm px-4 fw-bold shadow-sm">Select This</button>
                                </div>
                                @if($school->landing_template == $tpl['id'] || (!$school->landing_template && $tpl['id'] == 'modern_classic'))
                                <div class="active-badge" style="display: block !important;"><i class="bi bi-check-circle-fill"></i> Current Active</div>
                                @endif
                            </div>
                            <div class="template-info">
                                <h3>{{ $tpl['name'] }}</h3>
                                <p>{{ $tpl['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5 pt-4 border-top">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg">
                        Apply Designing Changes <i class="bi bi-magic ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@section('scripts')
<script>
    function handleImageError(img) {
        $(img).closest('.template-preview').find('.placeholder-icon').css('display', 'flex');
        $(img).remove();
    }

    $(document).ready(function () {
        $('.template-card').on('click', function () {
            const tplId = $(this).data('id');
            $('#selectedTemplateInput').val(tplId);
            $('.template-card').removeClass('active');
            $(this).addClass('active');
            $('.active-badge').remove();
            $(this).find('.template-preview').append('<div class="active-badge" style="display: block !important;"><i class="bi bi-check-circle-fill"></i> Current Active</div>');
        });
    });
</script>
@endsection

@endsection