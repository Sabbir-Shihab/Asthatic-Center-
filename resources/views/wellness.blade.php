@extends('layouts.app')
@section('title', t(site_setting('wellness_title', 'Wellness')).' — '.site_setting('site_name', 'Skinoveda'))
@section('content')
@php
    $title = t(site_setting('wellness_title', 'Holistic wellness for calm skin, body, and mind.'));
    $subtitle = t(site_setting('wellness_subtitle', 'A gentle care journey blending Ayurvedic wisdom, restorative rituals, and modern aesthetic support.'));
    $story = t(site_setting('wellness_story', 'Our wellness philosophy focuses on balance. From soothing consultations to calming rituals, we help you build a routine that supports healthy skin from the inside and outside. Each recommendation is shaped around comfort, consistency, and long term glow.'));
    $image = site_setting('wellness_image');
    $rituals = array_filter([
        t(site_setting('wellness_ritual_1', 'Ayurvedic inspired care plans')),
        t(site_setting('wellness_ritual_2', 'Stress calming skin rituals')),
        t(site_setting('wellness_ritual_3', 'Personal wellness consultation')),
    ]);
@endphp
<section class="wellness-hero">
    <div class="container wellness-hero-grid">
        <div class="wellness-copy">
            <span class="eyebrow">{{ t('Wellness & Ayurveda') }}</span>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
            <div class="wellness-actions">
                <a class="gold-button" href="{{ route('contact') }}">{{ t('Start your wellness plan') }}</a>
                <a class="wellness-outline" href="{{ route('treatments.category', 'ayurveda-panchakarma') }}">{{ t('View wellness treatments') }}</a>
            </div>
        </div>
        <div class="wellness-image-card">
            <img src="{{ $image ? media_url($image) : 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=85' }}" alt="{{ t('Wellness and Ayurvedic care') }}">
            <div class="wellness-floating-card"><span>{{ t('Natural healing') }}</span><strong>{{ t('Mindful care rituals') }}</strong></div>
        </div>
    </div>
</section>

<section class="wellness-light">
    <div class="container wellness-story-grid">
        <article class="wellness-story-card">
            <span class="wellness-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 19C5 9 13 5 20 5c0 7-4 15-14 15"/><path d="M6 18c4-4 8-7 13-9"/></svg></span>
            <h2>{{ t('A slower, softer way to care for your glow.') }}</h2>
            <p>{!! nl2br(e($story)) !!}</p>
        </article>
        <div class="wellness-rituals">
            @foreach($rituals as $ritual)
                <div><span>{{ sprintf('%02d', $loop->iteration) }}</span><strong>{{ $ritual }}</strong></div>
            @endforeach
        </div>
    </div>
</section>

<section class="wellness-programs">
    <div class="container">
        <div class="wellness-head">
            <div><span class="eyebrow">{{ t('Care pathways') }}</span><h2>{{ t('Wellness treatments designed for balance.') }}</h2></div>
            <a href="{{ route('treatments.category', 'ayurveda-panchakarma') }}">{{ t('View All →') }}</a>
        </div>
        <div class="wellness-treatment-grid">
            @forelse($wellnessTreatments->take(6) as $treatment)
                <a href="{{ route('treatments.show', $treatment) }}" class="wellness-treatment-card">
                    <img src="{{ $treatment->image ?: 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=700&q=85' }}" alt="{{ t($treatment->name) }}">
                    <div><span>{{ $treatment->duration_minutes ? $treatment->duration_minutes.' '.t('min') : t('Personalized') }}</span><h3>{{ t($treatment->name) }}</h3><p>{{ t($treatment->subtitle ?: Str::limit(strip_tags($treatment->description), 80)) }}</p><strong>{{ t('Learn more →') }}</strong></div>
                </a>
            @empty
                @foreach([['Ayurvedic Consultation','Personal guidance for balanced skin and wellness.'],['Calming Skin Ritual','A soothing ritual for stress tired skin.'],['Holistic Glow Plan','A gentle routine designed around your lifestyle.']] as $item)
                    <article class="wellness-treatment-card placeholder-card"><div><span>{{ t('Personalized') }}</span><h3>{{ t($item[0]) }}</h3><p>{{ t($item[1]) }}</p><strong>{{ t('Admin can add wellness treatments →') }}</strong></div></article>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

<section class="wellness-cta">
    <div class="container wellness-cta-card">
        <span class="eyebrow">{{ t('Begin gently') }}</span>
        <h2>{{ t('Book a consultation and let us understand what your skin and body need.') }}</h2>
        <a href="{{ route('contact') }}" class="gold-button">{{ t('Book appointment') }}</a>
    </div>
</section>
@endsection
