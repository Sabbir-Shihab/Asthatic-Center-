@extends('layouts.app')
@section('title', t($doctor->name).' - '.site_setting('site_name', 'Skinoveda'))
@section('content')
@php
    $heroPath = $doctor->getRawOriginal('hero_photo');
    $photoPath = $heroPath && (str_starts_with($heroPath, 'http') || \Illuminate\Support\Facades\Storage::disk('public')->exists($heroPath))
        ? $heroPath
        : ($doctor->getRawOriginal('photo') ?: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=1200&q=85');
    $photo = str_starts_with($photoPath, 'http') ? $photoPath : asset('storage/'.ltrim($photoPath, '/'));
    $credentials = is_array($doctor->credentials) ? $doctor->credentials : [];
@endphp
<section class="doctor-profile-hero">
    <div class="container doctor-profile-grid">
        <div class="doctor-profile-copy">
            <a href="{{ route('home') }}#about" class="treatment-back-link">{{ t('Back to experts') }}</a>
            <span class="contact-eyebrow">{{ t('Meet our expert') }}</span>
            <h1>{{ t($doctor->name) }}</h1>
            <p class="doctor-profile-role">{{ t($doctor->designation) }}</p>
            @if($doctor->quote)<blockquote>“{{ t($doctor->quote) }}”</blockquote>@endif
            <div class="doctor-profile-facts">
                <article><small>{{ t('Experience') }}</small><strong>{{ $doctor->years_experience }}{{ t('+ Years') }}</strong></article>
                <article><small>{{ t('Profession') }}</small><strong>{{ t($doctor->designation) }}</strong></article>
            </div>
            <div class="treatment-hero-actions">
                <button @click="bookingOpen=true" class="gold-button rounded-full px-6 py-3 text-sm font-semibold">{{ t('Book appointment') }}</button>
                <a href="{{ route('contact') }}" class="treatment-soft-button">{{ t('Contact clinic') }}</a>
            </div>
        </div>
        <div class="doctor-profile-photo"><img src="{{ $photo }}" alt="{{ t($doctor->name) }}"></div>
    </div>
</section>

<section class="doctor-profile-body">
    <div class="container doctor-profile-body-grid">
        <article>
            <span class="contact-eyebrow">{{ t('About') }}</span>
            <h2>{{ t('Professional profile') }}</h2>
            <p>{{ t($doctor->bio ?: 'Expert aesthetic care with a thoughtful, patient-first approach.') }}</p>
        </article>
        <article>
            <span class="contact-eyebrow">{{ t('Degree & expertise') }}</span>
            <h2>{{ t('Credentials') }}</h2>
            <div class="doctor-credential-list">
                @forelse($credentials as $credential)
                    <span>{{ t($credential) }}</span>
                @empty
                    <span>{{ t('Credentials can be added from Admin → Doctors.') }}</span>
                @endforelse
            </div>
        </article>
    </div>
</section>

<div x-show="bookingOpen" x-transition.opacity class="fixed inset-0 z-[60] grid place-items-center bg-black/65 p-4" @click.self="bookingOpen=false">
    <form action="{{ route('appointments.store') }}" method="POST" class="w-full max-w-xl rounded-3xl bg-[#fbf9f6] p-7 text-[#201624] shadow-2xl">
        @csrf
        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
        <div class="flex items-start justify-between gap-4"><div><span class="text-xs uppercase tracking-widest text-[#ac7b52]">{{ t('Appointment request') }}</span><h2 class="mt-2 font-serif text-3xl">{{ t($doctor->name) }}</h2></div><button type="button" @click="bookingOpen=false" class="text-2xl">x</button></div>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <label class="text-xs">{{ t('Full name') }}<input class="input mt-2 w-full" name="client_name" required></label>
            <label class="text-xs">{{ t('Phone') }}<input class="input mt-2 w-full" name="phone" required></label>
            <label class="text-xs">{{ t('Email') }}<input class="input mt-2 w-full" type="email" name="email" required></label>
            <label class="text-xs">{{ t('Preferred date/time') }}<input class="input mt-2 w-full" type="datetime-local" name="appointment_at" required></label>
            <label class="text-xs sm:col-span-2">{{ t('Treatment') }}<select name="treatment_id" class="input mt-2 w-full"><option value="">{{ t('Consultation first') }}</option>@foreach($allTreatments as $treatment)<option value="{{ $treatment->id }}">{{ t($treatment->name) }}</option>@endforeach</select></label>
            <label class="text-xs sm:col-span-2">{{ t('Message') }}<textarea class="input mt-2 w-full" name="notes" rows="3"></textarea></label>
        </div>
        <button class="gold-button mt-5 w-full rounded-xl py-3 text-sm font-semibold">{{ t('Request appointment') }}</button>
    </form>
</div>
@endsection
