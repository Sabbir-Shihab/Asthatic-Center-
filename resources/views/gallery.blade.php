@extends('layouts.app')
@section('title', t('Gallery').' — '.site_setting('site_name', 'Skinoveda'))
@section('content')
<section class="simple-page-hero"><div class="container"><span class="eyebrow">{{ t('Gallery') }}</span><h1>{{ t('Real care moments and visible transformations.') }}</h1><p>{{ t('Browse before-after results and client stories uploaded from admin.') }}</p></div></section>
<section class="simple-light"><div class="container gallery-page-grid">
@forelse($results as $result)
<article class="gallery-page-card"><div class="grid {{ $result->before_image && $result->after_image ? 'grid-cols-2' : 'grid-cols-1' }}">@if($result->before_image)<img src="{{ $result->before_image }}" alt="{{ t($result->title) }} - before">@endif @if($result->after_image)<img src="{{ $result->after_image }}" alt="{{ t($result->title) }} - after">@endif</div><div><span>{{ t($result->treatment ?: 'Result') }}</span><h2>{{ t($result->title) }}</h2><p>{{ t($result->caption) }}</p></div></article>
@empty
<div class="empty-page-card">{{ t('Gallery images will appear here after admin uploads before-after results.') }}</div>
@endforelse
</div></section>
@endsection
