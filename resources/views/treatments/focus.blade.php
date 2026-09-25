@extends('layouts.app')
@section('title', $focusArea->localized('name').' - '.site_setting('site_name', 'Skinoveda'))
@section('content')
@php
    $image = $focusArea->image ?: ($serviceCategory->image ?: 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=1400&q=85');
@endphp
<section class="service-detail-hero">
    <div class="container service-detail-grid">
        <div>
            <a href="{{ route('treatments.category', $activeCategory) }}" class="treatment-back-link">{{ t('Back to service') }}</a>
            <span class="eyebrow">{{ t($categoryTitle) }}</span>
            <h1>{{ $focusArea->localized('name') }}</h1>
            <p>{{ $focusArea->localized('summary') }}</p>
            <a href="#book-focus" class="gold-button">{{ t('Book a slot →') }}</a>
        </div>
        <img src="{{ $image }}" alt="{{ $focusArea->localized('name') }}">
    </div>
</section>
<section class="service-detail-body">
    <div class="container">
    <div class="service-detail-layout">
        <article class="service-detail-card">
            <span class="eyebrow !text-[#ac7b52]">{{ t('About this focus') }}</span>
            <h2>{{ $focusArea->localized('name') }}</h2>
            <p>{{ $focusArea->localized('details') }}</p>
            <div class="service-info-grid">
                <div><span>01</span><h3>{{ t('Who this is for') }}</h3><p>{{ $focusArea->localized('who_for') }}</p></div>
                <div><span>02</span><h3>{{ t('What to expect') }}</h3><p>{{ $focusArea->localized('what_to_expect') }}</p></div>
                <div><span>03</span><h3>{{ t('Care note') }}</h3><p>{{ $focusArea->localized('care_note') }}</p></div>
            </div>
            @if($focusArea->duration_minutes || $focusArea->price)
                <div class="service-admin-treatments">
                    @if($focusArea->duration_minutes)
                        <p><strong>{{ t('Time') }}:</strong> {{ $focusArea->duration_minutes }} {{ t('min') }}</p>
                    @endif
                    @if($focusArea->price)
                        <p><strong>{{ t('Charge') }}:</strong> {{ t('BDT') }} {{ number_format($focusArea->price) }}</p>
                    @endif
                </div>
            @endif
        </article>
        <aside id="book-focus" class="service-book-card">
            <span class="eyebrow !text-[#ac7b52]">{{ t('Book a slot') }}</span>
            <h2>{{ t('Request appointment') }}</h2>
            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="category" value="{{ $activeCategory }}">
                @include('partials.focus-select', ['selectedFocusId' => $focusArea->id])
                <label>{{ t('Full name') }}<input class="input" name="client_name" required></label>
                <label>{{ t('Phone') }}<input class="input" name="phone" required></label>
                <label>{{ t('Email') }}<input class="input" type="email" name="email" required></label>
                <label>{{ t('Preferred date/time') }}<input class="input" type="datetime-local" name="appointment_at" required></label>
                <label>{{ t('Preferred specialist') }}
                    <select name="doctor_id" class="input">
                        <option value="">{{ t('Any available expert') }}</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ t($doctor->name) }} - {{ t($doctor->designation) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>{{ t('Message') }}<textarea class="input" name="notes" rows="3" placeholder="{{ $focusArea->localized('name') }}">{{ old('notes') }}</textarea></label>
                <button class="gold-button">{{ t('Book slot →') }}</button>
            </form>
        </aside>
    </div>
    @if($related->isNotEmpty())
        <div class="focus-related">
            <div class="focus-related-head">
                <div>
                    <span class="eyebrow !text-[#ac7b52]">{{ t($categoryTitle) }}</span>
                    <h3>{{ t('Related focus areas') }}</h3>
                </div>
                <p>{{ t('Choose another focus within this service. Each one opens its own care details.') }}</p>
            </div>
            <div class="focus-related-grid">
                @foreach($related as $item)
                    <a class="focus-related-card" href="{{ route('treatments.focus', [$activeCategory, $item->slug]) }}">
                        <span class="focus-related-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="focus-related-copy">
                            <strong>{{ $item->localized('name') }}</strong>
                            <small>{{ $item->localized('summary') }}</small>
                        </span>
                        <span class="focus-related-arrow" aria-hidden="true">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
</section>
@endsection
