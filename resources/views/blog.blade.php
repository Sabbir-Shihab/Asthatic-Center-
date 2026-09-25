@extends('layouts.app')
@section('title', t('Blog').' — '.site_setting('site_name', 'Skinoveda'))
@section('content')
<section class="simple-page-hero"><div class="container"><span class="eyebrow">{{ t('Skinoveda Blog') }}</span><h1>{{ t('Guides for beauty, health, and natural wellness.') }}</h1><p>{{ t('Educational care notes across laser aesthetics, Ayurveda, dermatology, naturopathy, and holistic lifestyle.') }}</p></div></section>
<section class="simple-light"><div class="container blog-page-grid">
@forelse($posts as $post)
@php($postImage = $post->getRawOriginal('image'))
<article class="blog-page-card">
@if($postImage)<img src="{{ str_starts_with($postImage, 'http') ? $postImage : asset('storage/'.ltrim($postImage, '/')) }}" alt="{{ $post->title }}" class="blog-post-image">@endif
<span>{{ optional($post->published_at)->format('d M Y') ?: 'Skinoveda Journal' }}</span><h2>{{ t($post->title) }}</h2><p>{{ t($post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 150)) }}</p>
<a href="{{ route('blog.post', $post->slug) }}" class="blog-read-more">{{ t('See more →') }}</a>
</article>
@empty
<div class="empty-page-card">{{ t('New journal articles will appear here after they are published from the admin panel.') }}</div>
@endforelse
</div></section>
@endsection
