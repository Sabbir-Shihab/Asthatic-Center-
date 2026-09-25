@extends('layouts.app')
@section('title', t(site_setting('about_title', 'About Skinoveda')).' — '.site_setting('site_name', 'Skinoveda'))
@section('content')
@php
    $siteName = site_setting('site_name', 'Skinoveda');
    $siteSubtitle = site_setting('site_subtitle', 'Aesthetic Care');
    $title = t(site_setting('about_title', 'Where aesthetic science meets mindful wellness.'));
    $subtitle = t(site_setting('about_subtitle', 'Skinoveda was created as a calm, premium care space for advanced skin treatments, holistic wellness, and curated skincare rituals.'));
    $story = t(site_setting('about_story', 'Our approach blends modern dermatology, aesthetic treatments, premium skincare, and Ayurvedic wellness into one thoughtful experience. Every consultation starts with listening, understanding your skin goals, and designing care that feels personal, safe, and beautiful.'));
    $mission = t(site_setting('about_mission', 'To help every client feel confident in their skin through expert guidance, honest care, refined treatments, and a peaceful clinic experience.'));
    $aboutImage = site_setting('about_image');
    $features = array_filter([
        t(site_setting('about_feature_1', 'Expert led aesthetic treatments')),
        t(site_setting('about_feature_2', 'Holistic wellness and Ayurvedic care')),
        t(site_setting('about_feature_3', 'Premium skincare with personal guidance')),
    ]);
@endphp
<section class="about-page-hero">
    <div class="container about-hero-grid">
        <div class="about-copy">
            <span class="eyebrow">{{ t('About') }} {{ $siteName }}</span>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
            <div class="about-actions">
                <a href="{{ route('contact') }}" class="gold-button">{{ t('Book a consultation') }}</a>
                <a href="{{ route('treatments.index') }}" class="about-outline">{{ t('Explore treatments') }}</a>
            </div>
        </div>
        <div class="about-visual">
            <img src="{{ $aboutImage ? media_url($aboutImage) : 'https://images.unsplash.com/photo-1600334129128-685c5582fd35?auto=format&fit=crop&w=1200&q=85' }}" alt="{{ $siteName }} {{ t('clinic experience') }}">
            <div class="about-badge"><strong>{{ $siteName }}</strong><span>{{ t($siteSubtitle) }}</span></div>
        </div>
    </div>
</section>

<section class="about-light">
    <div class="container about-story-grid">
        <article class="about-card about-story-card">
            <span class="about-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 20s-5-3-5-8c3 0 5 2 5 8Z"/><path d="M12 20s5-3 5-8c-3 0-5 2-5 8Z"/><path d="M12 17S8 13 12 5c4 8 0 12 0 12Z"/></svg></span>
            <h2>{{ t('Our story') }}</h2>
            <p>{!! nl2br(e($story)) !!}</p>
        </article>
        <article class="about-card about-mission-card">
            <span class="eyebrow !text-[#ac7b52]">{{ t('Our promise') }}</span>
            <h2>{{ t('Care that feels personal.') }}</h2>
            <p>{!! nl2br(e($mission)) !!}</p>
            <div class="about-feature-list">
                @foreach($features as $feature)
                    <div><span>✓</span>{{ $feature }}</div>
                @endforeach
            </div>
        </article>
    </div>
</section>

<section class="about-dark-band">
    <div class="container">
        <div class="about-section-head">
            <div><span class="eyebrow">{{ t('Why clients choose us') }}</span><h2>{{ t('A calm clinic experience with refined details.') }}</h2></div>
            <a href="{{ route('contact') }}">{{ t('Visit or contact us →') }}</a>
        </div>
        <div class="about-values">
            @foreach([['Consultation first','We listen before recommending any treatment.'],['Expert guidance','Doctors and care experts shape your plan.'],['Premium rituals','Skincare and wellness are selected with intention.']] as $value)
                <article><span>{{ sprintf('%02d', $loop->iteration) }}</span><h3>{{ t($value[0]) }}</h3><p>{{ t($value[1]) }}</p></article>
            @endforeach
        </div>
    </div>
</section>

@if($doctors->isNotEmpty())
<section class="about-light about-team-section">
    <div class="container">
        <div class="about-section-head light-head">
            <div><span class="eyebrow !text-[#ac7b52]">{{ t('Our experts') }}</span><h2>{{ t('Meet the people behind your care.') }}</h2></div>
            <a href="{{ route('contact') }}">{{ t('Book appointment →') }}</a>
        </div>
        <div class="about-team-grid">
            @foreach($doctors->take(4) as $doctor)
                <a href="{{ route('doctors.show', $doctor) }}" class="about-doctor-card">
                    <img src="{{ $doctor->photo ?: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=500&q=85' }}" alt="{{ $doctor->name }}">
                    <h3>{{ t($doctor->name) }}</h3>
                    <p>{{ t($doctor->designation) }}</p>
                    <span>{{ t('View profile →') }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
