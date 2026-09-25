@extends('layouts.app')
@section('title', t('Before & After').' - '.site_setting('site_name', 'Skinoveda'))
@section('content')
<section class="results-gallery-hero">
    <div class="container">
        <span class="contact-eyebrow">{{ t('Before & After') }}</span>
        <h1>{{ t('Real results. Real people.') }}</h1>
        <p>{{ t('Explore every transformation uploaded from the admin panel.') }}</p>
    </div>
</section>
<section class="results-gallery-section">
    <div class="container results-gallery-grid">
        @forelse($results as $result)
            <article class="result-gallery-card">
                <div class="result-gallery-images">
                    @if($result->before_image)<img src="{{ $result->before_image }}" alt="{{ t('Before') }} - {{ t($result->title) }}">@endif
                    @if($result->after_image)<img src="{{ $result->after_image }}" alt="{{ t('After') }} - {{ t($result->title) }}">@endif
                </div>
                <div class="result-gallery-body">
                    <span>{{ t($result->treatment ?: 'Skinoveda Result') }}</span>
                    <h2>{{ t($result->title) }}</h2>
                    @if($result->caption)<p>{{ t($result->caption) }}</p>@endif
                </div>
            </article>
        @empty
            <div class="treatment-empty">{{ t('Before & After results will appear here after Admin uploads them.') }}</div>
        @endforelse
    </div>
</section>
@endsection
