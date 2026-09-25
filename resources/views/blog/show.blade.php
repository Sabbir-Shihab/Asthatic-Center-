@extends('layouts.app')
@section('title', t($post->title).' — '.site_setting('site_name', 'Skinoveda'))
@section('content')
@php($postImage = $post->getRawOriginal('image'))
<section class="blog-post-hero"><div class="container blog-post-hero-inner"><a href="{{ route('blog') }}" class="treatment-back-link">← {{ t('Back to Blog') }}</a><span class="eyebrow">{{ optional($post->published_at)->format('d M Y') ?: 'Skinoveda Journal' }}</span><h1>{{ t($post->title) }}</h1>@if($post->excerpt)<p>{{ t($post->excerpt) }}</p>@endif</div></section>
<section class="blog-post-content"><div class="container blog-post-layout"><article>@if($postImage)<div class="blog-post-cover"><img src="{{ str_starts_with($postImage, 'http') ? $postImage : asset('storage/'.ltrim($postImage, '/')) }}" alt="{{ $post->title }}"></div>@endif<div class="blog-post-copy">{!! nl2br(e($post->content)) !!}</div></article><aside><span class="eyebrow">{{ t('Skinoveda Journal') }}</span><h2>{{ t('Thoughtful guidance for your care journey.') }}</h2><a href="{{ route('book') }}" class="gold-button">{{ t('Book a consultation') }}</a></aside></div></section>
@endsection
