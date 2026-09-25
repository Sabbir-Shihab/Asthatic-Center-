@extends('layouts.app')
@section('title', t('Contact').' - '.site_setting('site_name', 'Skinoveda'))
@section('content')
@php
    $siteName = site_setting('site_name', 'Skinoveda');
    $siteSubtitle = t(site_setting('site_subtitle', 'Aesthetic Care'));
    $siteTagline = t(site_setting('site_tagline', 'Natural beauty - modern science - holistic wellness'));
    $address = t(site_setting('contact_address', "Gulshan Avenue, Dhaka\nBangladesh"));
    $location = t(site_setting('contact_location', 'Dhaka, Bangladesh'));
    $phone = site_setting('contact_phone', '+880 1700 000000');
    $email = site_setting('contact_email', 'hello@skinoveda.local');
    $hours = t(site_setting('opening_hours', 'Sat-Thu, 10 am-8 pm'));
    $telLink = preg_replace('/[^\d+]/', '', $phone);
    $mapQuery = trim(site_setting('contact_location', 'Dhaka, Bangladesh').' '.site_setting('contact_address', "Gulshan Avenue, Dhaka\nBangladesh"));
    $mapUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode($mapQuery);
    $mapEmbed = 'https://maps.google.com/maps?q='.urlencode($mapQuery).'&output=embed';
@endphp

<section class="contact-hero">
    <div class="container contact-wrap">
        <div class="contact-copy">
            <span class="contact-eyebrow">{{ $siteTagline }}</span>
            <h1>{{ t('Visit') }} {{ $siteName }}<small>{{ $siteSubtitle }}</small></h1>
            <p>{{ t('Book a consultation, ask about treatments, or visit the clinic for a calm, expert-led skincare experience.') }}</p>
            <div class="contact-actions">
                <a href="tel:{{ $telLink }}">{{ t('Call') }} {{ $phone }}</a>
                @if($email)<a href="mailto:{{ $email }}">{{ t('Email us') }}</a>@endif
            </div>
        </div>
        <div class="contact-card-grid">
            <article><span>⌖</span><small>{{ t('Location') }}</small><strong>{{ $location }}</strong><p>{!! nl2br(e($address)) !!}</p></article>
            <article><span>☏</span><small>{{ t('Phone') }}</small><strong>{{ $phone }}</strong><p>{{ t('Call for appointment support and availability.') }}</p></article>
            <article><span>✉</span><small>{{ t('Email') }}</small><strong>{{ $email ?: t('Not set') }}</strong><p>{{ t('Send skincare questions, booking notes, or product queries.') }}</p></article>
            <article><span>◷</span><small>{{ t('Hours') }}</small><strong>{{ $hours }}</strong><p>{{ t('Clinic schedule can be updated anytime from Settings.') }}</p></article>
        </div>
    </div>
</section>

<section class="contact-panel-section">
    <div class="container contact-panel-grid">
        <div class="contact-map-card">
            <a class="contact-map-art contact-google-map" href="{{ $mapUrl }}" target="_blank" rel="noopener">
                <iframe src="{{ $mapEmbed }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ $siteName }} {{ t('Location') }}"></iframe>
                <div class="contact-map-overlay">
                    <span>⌖</span>
                    <strong>{{ $location }}</strong>
                    <p>{!! nl2br(e($address)) !!}</p>
                    <small>{{ t('Open in Google Maps →') }}</small>
                </div>
            </a>
        </div>
        <form action="{{ route('appointments.store') }}" method="POST" class="contact-form-card">
            @csrf
            <span class="contact-eyebrow">{{ t('Request a visit') }}</span>
            <h2>{{ t('Plan your appointment') }}</h2>
            <div class="contact-form-grid">
                <label>{{ t('Full name') }}<input name="client_name" required></label>
                <label>{{ t('Phone') }}<input name="phone" required></label>
                <label>{{ t('Email') }}<input type="email" name="email" required></label>
                <label>{{ t('Preferred time') }}<input type="datetime-local" name="appointment_at" required></label>
                <label>{{ t('Treatment') }}<select name="treatment_id"><option value="">{{ t('Choose a treatment') }}</option>@foreach($allTreatments as $treatment)<option value="{{ $treatment->id }}">{{ t($treatment->name) }}</option>@endforeach</select></label>
                <label>{{ t('Doctor') }}<select name="doctor_id"><option value="">{{ t('Any available expert') }}</option>@foreach($doctors as $doctor)<option value="{{ $doctor->id }}">{{ t($doctor->name) }}</option>@endforeach</select></label>
                <label class="wide">{{ t('Message') }}<textarea name="notes" rows="4" placeholder="{{ t('Tell us what you need help with') }}"></textarea></label>
            </div>
            <button class="gold-button rounded-xl px-6 py-3 text-sm font-semibold">{{ t('Request appointment') }}</button>
        </form>
    </div>
</section>
@endsection
