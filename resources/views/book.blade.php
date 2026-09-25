@extends('layouts.app')
@section('title', t('Book Appointment').' - '.site_setting('site_name', 'Skinoveda'))
@section('content')
@php
    $bookingServices = ($serviceCategories ?? collect())->map(fn ($service) => [
        'slug' => $service->slug,
        'name' => t($service->name),
        'focus' => $service->focusAreas->map(fn ($area) => [
            'id' => $area->id,
            'name' => $area->localized('name'),
        ])->values(),
    ])->values();
@endphp
<section class="book-page-hero"><div class="container"><span class="eyebrow">{{ t('Book Appointment') }}</span><h1>{{ t('Begin your Skinoveda care journey.') }}</h1><p>{{ t('Select a service category, share your preferred time, and our team will contact you to confirm your appointment.') }}</p></div></section>
<section class="book-page-section"><div class="container book-page-card"><form action="{{ route('appointments.store') }}" method="POST" class="grid gap-5 sm:grid-cols-2">@csrf
<label class="text-xs">{{ t('Care category') }}<select name="category" class="input mt-2 w-full" x-model="category" @change="resetFocus()" required><option value="">{{ t('Select category') }}</option>@foreach(($serviceCategories ?? \App\Models\ServiceCategory::publishedList()) as $service)<option value="{{ $service->slug }}">{{ t($service->name) }}</option>@endforeach</select></label>
<label class="text-xs">{{ t('Focus area') }}<select name="focus_area_id" class="input focus-area-select mt-2 w-full" data-focus-select data-selected="{{ old('focus_area_id', '') }}" disabled><option value="">{{ t('Select a focus area') }}</option></select><small class="mt-1 block text-[#8b818b]" data-focus-empty hidden>{{ t('No focus areas added for this category yet.') }}</small></label>
<label class="text-xs">{{ t('Preferred specialist') }}<select name="doctor_id" class="input mt-2 w-full"><option value="">{{ t('Any available expert') }}</option>@foreach($doctors as $doctor)<option value="{{ $doctor->id }}">{{ t($doctor->name) }} - {{ t($doctor->designation) }}</option>@endforeach</select></label>
<label class="text-xs">{{ t('Full name') }}<input class="input mt-2 w-full" name="client_name" value="{{ old('client_name') }}" required></label>
<label class="text-xs">{{ t('Phone') }}<input class="input mt-2 w-full" name="phone" value="{{ old('phone') }}" required></label>
<label class="text-xs">{{ t('Email') }}<input class="input mt-2 w-full" type="email" name="email" value="{{ old('email') }}" required></label>
<label class="text-xs">{{ t('Preferred date/time') }}<input class="input mt-2 w-full" type="datetime-local" name="appointment_at" value="{{ old('appointment_at') }}" required></label>
<label class="text-xs sm:col-span-2">{{ t('Message') }}<textarea class="input mt-2 w-full" name="notes" rows="4" placeholder="{{ t('Tell us what kind of care you are looking for') }}">{{ old('notes') }}</textarea></label>
<div class="sm:col-span-2"><button class="gold-button rounded-full px-8 py-4 text-sm font-bold">{{ t('Request Appointment ->') }}</button></div>
</form></div></section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const category = document.querySelector('select[name="category"]');
    const focus = document.querySelector('[data-focus-select]');
    if (!category || !focus) return;
    const services = @json($bookingServices);
    const empty = document.querySelector('[data-focus-empty]');
    function updateFocus() {
        const selected = services.find(service => service.slug === category.value);
        const areas = selected ? selected.focus : [];
        const previous = focus.dataset.selected || focus.value;
        focus.innerHTML = '<option value="">{{ t('Select a focus area') }}</option>';
        areas.forEach(area => focus.add(new Option(area.name, area.id)));
        focus.disabled = areas.length === 0;
        focus.value = areas.some(area => String(area.id) === String(previous)) ? previous : '';
        focus.dataset.selected = '';
        if (empty) empty.hidden = !category.value || areas.length > 0;
    }
    category.addEventListener('change', updateFocus);
    updateFocus();
});
</script>
@endsection
