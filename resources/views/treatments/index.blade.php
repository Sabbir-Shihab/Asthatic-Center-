@extends('layouts.app')
@section('title', t('Our Services').' - '.site_setting('site_name', 'Skinoveda'))
@section('content')
<section class="services-catalog-hero"><div class="container"><span class="eyebrow">{{ t('Our Services') }}</span><h1>{{ t('Comprehensive Care for Skin, Body, Mind & Wellness') }}</h1><p>{{ t('Explore our specialized services, designed around modern aesthetic science, Ayurveda, naturopathy and holistic wellness.') }}</p></div></section>
<section class="services-catalog-section"><div class="container services-catalog-grid">
@foreach(\App\Models\ServiceCategory::publishedList() as $service)
@php($fallbackFocusAreas = array_values(array_filter((array) $service->focus_areas)))
<article class="services-catalog-card"><div class="services-catalog-title"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} {{ $service->icon ?: '✦' }}</span><h2>{{ t($service->name) }}</h2><a href="{{ route('treatments.category', $service->slug) }}">{{ t('View treatments →') }}</a></div><div class="services-catalog-groups"><div>@if($service->focusAreas->isNotEmpty())<h3>{{ t('Included focus areas') }}</h3><ul>@foreach($service->focusAreas as $focus)<li>{{ $focus->localized('name') }}</li>@endforeach</ul>@elseif($fallbackFocusAreas)<h3>{{ t('Included focus areas') }}</h3><ul>@foreach($fallbackFocusAreas as $focus)<li>{{ t($focus) }}</li>@endforeach</ul>@else<h3>{{ t($service->headline ?: $service->name) }}</h3><ul><li>{{ t($service->short_description) }}</li></ul>@endif</div></div></article>
@endforeach
</div></section>
@endsection
