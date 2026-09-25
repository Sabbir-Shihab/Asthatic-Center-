@extends('layouts.app')
@section('title', t('Client Stories').' - '.site_setting('site_name', 'Skinoveda'))
@section('content')
<section class="testimonials-gallery-hero">
    <div class="container">
        <span class="contact-eyebrow">{{ t('Client stories') }}</span>
        <h1>{{ t('What our clients say.') }}</h1>
        <p>{{ t('Every review is managed from the admin panel with client photo, name, rating and story.') }}</p>
    </div>
</section>
<section class="testimonials-gallery-section">
    <div class="container testimonials-gallery-grid">
        @forelse($testimonials as $item)
            <article class="testimonial-gallery-card">
                <div class="testimonial-gallery-head">
                    <img src="{{ $item->client_photo ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=160&q=80' }}" alt="{{ t($item->client_name) }}">
                    <div><h2>{{ t($item->client_name) }}</h2><span>{{ str_repeat('★', (int) $item->rating) }}</span></div>
                </div>
                <p>“{{ t($item->review) }}”</p>
            </article>
        @empty
            <div class="treatment-empty">{{ t('Client testimonials will appear here after Admin uploads them.') }}</div>
        @endforelse
    </div>
</section>
@endsection
