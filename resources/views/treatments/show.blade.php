@extends('layouts.app')
@section('title', t($treatment->name).' - '.site_setting('site_name', 'Skinoveda'))
@section('content')
@php
    $image = $treatment->image ?: 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=1400&q=85';
@endphp
<section class="treatment-detail-hero">
    <div class="container treatment-detail-grid">
        <div class="treatment-detail-copy">
            <a href="{{ route('treatments.index') }}" class="treatment-back-link">{{ t('All treatments') }}</a>
            <span class="contact-eyebrow">{{ t('Treatment details') }}</span>
            <h1>{{ t($treatment->name) }}</h1>
            @if($treatment->subtitle)<p class="treatment-subtitle">{{ t($treatment->subtitle) }}</p>@endif
            <p>{{ t($treatment->description ?: 'A personalised Skinoveda treatment prepared by our expert care team for your skin goals, comfort, and wellness.') }}</p>
            <div class="treatment-facts">
                <article><small>{{ t('Time') }}</small><strong>{{ $treatment->duration_minutes }} {{ t('min') }}</strong></article>
                <article><small>{{ t('Charge') }}</small><strong>{{ $treatment->price ? t('BDT').' '.number_format($treatment->price) : t('Consultation based') }}</strong></article>
                <article><small>{{ t('Status') }}</small><strong>{{ $treatment->is_featured ? t('Featured') : t('Available') }}</strong></article>
            </div>
            <div class="treatment-hero-actions">
                <a href="#book-treatment" class="gold-button rounded-full px-6 py-3 text-sm font-semibold">{{ t('Book this treatment') }}</a>
                <a href="{{ route('contact') }}" class="treatment-soft-button">{{ t('Ask before booking') }}</a>
            </div>
        </div>
        <div class="treatment-detail-image">
            <img src="{{ $image }}" alt="{{ t($treatment->name) }}">
        </div>
    </div>
</section>

<section class="treatment-booking-panel" id="book-treatment">
    <div class="container treatment-booking-grid">
        <div>
            <span class="contact-eyebrow">{{ t('Appointment request') }}</span>
            <h2>{{ t('Choose your preferred date and time.') }}</h2>
            <p>{{ t('Fill out the form and the request will appear in Admin -> Appointments for confirmation.') }}</p>
            @if(session('appointment_submitted'))
                <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-900">{{ session('appointment_submitted') }}</div>
            @endif
        </div>
        <form action="{{ route('appointments.store') }}" method="POST" class="treatment-slot-form">
            @csrf
            <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-xs">{{ t('Full name') }}<input class="input mt-2 w-full" name="client_name" required></label>
                <label class="text-xs">{{ t('Phone') }}<input class="input mt-2 w-full" name="phone" required></label>
                <label class="text-xs">{{ t('Email') }}<input class="input mt-2 w-full" type="email" name="email" required></label>
                <label class="text-xs">{{ t('Preferred date/time') }}<input class="input mt-2 w-full" type="datetime-local" name="appointment_at" required></label>
                <label class="text-xs sm:col-span-2">{{ t('Preferred expert') }}<select name="doctor_id" class="input mt-2 w-full"><option value="">{{ t('Any available expert') }}</option>@foreach($doctors as $doctor)<option value="{{ $doctor->id }}">{{ t($doctor->name) }} - {{ t($doctor->designation) }}</option>@endforeach</select></label>
                <label class="text-xs sm:col-span-2">{{ t('Message') }}<textarea class="input mt-2 w-full" name="notes" rows="3" placeholder="{{ t('Share skin concern, preferred time, or extra notes') }}"></textarea></label>
            </div>
            <button class="gold-button mt-5 w-full rounded-xl py-3 text-sm font-semibold">{{ t('Request appointment') }}</button>
        </form>
    </div>
</section>

@if($relatedTreatments->isNotEmpty())
<section class="treatment-related-section">
    <div class="container">
        <div class="treatment-section-head">
            <span class="contact-eyebrow">{{ t('You may also like') }}</span>
            <h2>{{ t('Related Treatments') }}</h2>
        </div>
        <div class="treatment-grid-page compact">
            @foreach($relatedTreatments as $related)
                <a href="{{ route('treatments.show', $related) }}" class="treatment-page-card">
                    <img src="{{ $related->image ?: 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=900&q=85' }}" alt="{{ t($related->name) }}">
                    <div class="treatment-page-card-body">
                        <span>{{ $related->duration_minutes }} {{ t('min') }}</span>
                        <h3>{{ t($related->name) }}</h3>
                        <p>{{ t($related->subtitle) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
