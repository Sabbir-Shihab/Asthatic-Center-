@extends('admin.layout')
@section('content')
@php
    $value = fn ($key) => old($key, $settings->get($key)?->value ?? $defaults[$key] ?? '');
@endphp
<h1 class="font-serif text-3xl text-white">Settings</h1>
<p class="mt-2 text-sm text-white/60">Manage the website name, logo, hero banner and contact information.</p>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}" class="mt-6 grid gap-6 rounded-2xl border border-white/10 bg-[#1b1023] p-6 text-white shadow-lg">
    @csrf

    <section class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2 border-b border-white/10 pb-2">
            <h2 class="font-serif text-2xl text-white">Brand</h2>
        </div>
        <label class="block text-xs text-white/75">Site Name<input name="site_name" value="{{ $value('site_name') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Subtitle<input name="site_subtitle" value="{{ $value('site_subtitle') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75 sm:col-span-2">Top Tagline<input name="site_tagline" value="{{ $value('site_tagline') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75 sm:col-span-2">Site Description<textarea name="site_description" rows="2" class="input mt-2 w-full">{{ $value('site_description') }}</textarea></label>
        <div class="block text-xs text-white/75">
            Site Logo
            <small class="mt-1 block text-[#e0a96d]">Recommended: transparent PNG/SVG, square or horizontal, max 2 MB.</small>
            @if($value('site_logo'))<img src="{{ media_url($value('site_logo')) }}" alt="Current logo" class="my-2 h-20 w-20 rounded-xl border border-white/15 object-contain bg-white/5">@endif
            <input type="file" name="site_logo_upload" accept="image/jpeg,image/png,image/webp,image/avif,image/svg+xml" class="mt-2 block w-full rounded-xl border border-white/15 bg-white/5 p-2 text-xs text-white file:mr-3 file:rounded-lg file:border-0 file:bg-[#e0a96d] file:px-3 file:py-2 file:text-[#21132a]">
            <input name="site_logo" value="{{ $value('site_logo') }}" class="input mt-2 w-full" placeholder="Or paste a logo URL">
        </div>
        <div class="block text-xs text-white/75">
            Favicon
            <small class="mt-1 block text-[#e0a96d]">Browser tab icon. Recommended: 512 x 512 px PNG/SVG/ICO, max 1 MB.</small>
            @if($value('site_favicon'))<img src="{{ media_url($value('site_favicon')) }}" alt="Current favicon" class="my-2 h-14 w-14 rounded-lg border border-white/15 object-contain bg-white/5">@endif
            <input type="file" name="site_favicon_upload" accept="image/x-icon,image/vnd.microsoft.icon,image/png,image/jpeg,image/webp,image/svg+xml" class="mt-2 block w-full rounded-xl border border-white/15 bg-white/5 p-2 text-xs text-white file:mr-3 file:rounded-lg file:border-0 file:bg-[#e0a96d] file:px-3 file:py-2 file:text-[#21132a]">
            <input name="site_favicon" value="{{ $value('site_favicon') }}" class="input mt-2 w-full" placeholder="Or paste a favicon URL">
        </div>
        <div class="block text-xs text-white/75">
            Hero Banner
            <small class="mt-1 block text-[#e0a96d]">Recommended size for 100% view: 1920 x 1080 px, 16:9. Keep important text/logo inside the center-left safe area. JPG, PNG, WebP or AVIF. Max 5 MB.</small>
            @if($value('hero_banner'))<img src="{{ media_url($value('hero_banner')) }}" alt="Current hero banner" class="my-2 h-28 w-full rounded-xl border border-white/15 object-cover">@endif
            <input type="file" name="hero_banner_upload" accept="image/jpeg,image/png,image/webp,image/avif" class="mt-2 block w-full rounded-xl border border-white/15 bg-white/5 p-2 text-xs text-white file:mr-3 file:rounded-lg file:border-0 file:bg-[#e0a96d] file:px-3 file:py-2 file:text-[#21132a]">
            <input name="hero_banner" value="{{ $value('hero_banner') }}" class="input mt-2 w-full" placeholder="Or paste a banner URL">
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2 border-b border-white/10 pb-2">
            <h2 class="font-serif text-2xl text-white">About Page</h2>
            <p class="mt-1 text-xs text-white/50">These details will show on the public About page.</p>
        </div>
        <label class="block text-xs text-white/75">About Title<input name="about_title" value="{{ $value('about_title') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">About Subtitle<input name="about_subtitle" value="{{ $value('about_subtitle') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75 sm:col-span-2">About Story<textarea name="about_story" rows="5" class="input mt-2 w-full">{{ $value('about_story') }}</textarea></label>
        <label class="block text-xs text-white/75 sm:col-span-2">Mission / Promise<textarea name="about_mission" rows="3" class="input mt-2 w-full">{{ $value('about_mission') }}</textarea></label>
        <label class="block text-xs text-white/75">Feature 1<input name="about_feature_1" value="{{ $value('about_feature_1') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Feature 2<input name="about_feature_2" value="{{ $value('about_feature_2') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Feature 3<input name="about_feature_3" value="{{ $value('about_feature_3') }}" class="input mt-2 w-full"></label>
        <div class="block text-xs text-white/75">
            About Image
            <small class="mt-1 block text-[#e0a96d]">Recommended: 1100 x 900 px, JPG/PNG/WebP, max 5 MB.</small>
            @if($value('about_image'))<img src="{{ media_url($value('about_image')) }}" alt="Current about image" class="my-2 h-28 w-full rounded-xl border border-white/15 object-cover">@endif
            <input type="file" name="about_image_upload" accept="image/jpeg,image/png,image/webp,image/avif" class="mt-2 block w-full rounded-xl border border-white/15 bg-white/5 p-2 text-xs text-white file:mr-3 file:rounded-lg file:border-0 file:bg-[#e0a96d] file:px-3 file:py-2 file:text-[#21132a]">
            <input name="about_image" value="{{ $value('about_image') }}" class="input mt-2 w-full" placeholder="Or paste an image URL">
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2 border-b border-white/10 pb-2">
            <h2 class="font-serif text-2xl text-white">Wellness Page</h2>
            <p class="mt-1 text-xs text-white/50">These details will show on the public Wellness page.</p>
        </div>
        <label class="block text-xs text-white/75">Wellness Title<input name="wellness_title" value="{{ $value('wellness_title') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Wellness Subtitle<input name="wellness_subtitle" value="{{ $value('wellness_subtitle') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75 sm:col-span-2">Wellness Story<textarea name="wellness_story" rows="4" class="input mt-2 w-full">{{ $value('wellness_story') }}</textarea></label>
        <label class="block text-xs text-white/75">Ritual 1<input name="wellness_ritual_1" value="{{ $value('wellness_ritual_1') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Ritual 2<input name="wellness_ritual_2" value="{{ $value('wellness_ritual_2') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Ritual 3<input name="wellness_ritual_3" value="{{ $value('wellness_ritual_3') }}" class="input mt-2 w-full"></label>
        <div class="block text-xs text-white/75">
            Wellness Image
            <small class="mt-1 block text-[#e0a96d]">Recommended: 1200 x 900 px calming spa/wellness image, max 5 MB.</small>
            @if($value('wellness_image'))<img src="{{ media_url($value('wellness_image')) }}" alt="Current wellness image" class="my-2 h-28 w-full rounded-xl border border-white/15 object-cover">@endif
            <input type="file" name="wellness_image_upload" accept="image/jpeg,image/png,image/webp,image/avif" class="mt-2 block w-full rounded-xl border border-white/15 bg-white/5 p-2 text-xs text-white file:mr-3 file:rounded-lg file:border-0 file:bg-[#e0a96d] file:px-3 file:py-2 file:text-[#21132a]">
            <input name="wellness_image" value="{{ $value('wellness_image') }}" class="input mt-2 w-full" placeholder="Or paste an image URL">
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2 border-b border-white/10 pb-2">
            <h2 class="font-serif text-2xl text-white">Contact Info</h2>
        </div>
        <label class="block text-xs text-white/75">Header Location<input name="contact_location" value="{{ $value('contact_location') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Phone<input name="contact_phone" value="{{ $value('contact_phone') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Email<input type="email" name="contact_email" value="{{ $value('contact_email') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Opening Hours<input name="opening_hours" value="{{ $value('opening_hours') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75 sm:col-span-2">Address<textarea name="contact_address" rows="3" class="input mt-2 w-full">{{ $value('contact_address') }}</textarea></label>
        <label class="block text-xs text-white/75 sm:col-span-2">Footer About<textarea name="footer_about" rows="3" class="input mt-2 w-full">{{ $value('footer_about') }}</textarea></label>
        <label class="block text-xs text-white/75">Newsletter Heading<input name="newsletter_heading" value="{{ $value('newsletter_heading') }}" class="input mt-2 w-full"></label>
        <label class="block text-xs text-white/75">Newsletter Text<input name="newsletter_text" value="{{ $value('newsletter_text') }}" class="input mt-2 w-full"></label>
    </section>

    <div class="flex gap-3">
        <button class="gold-button rounded-xl px-6 py-3 text-sm font-semibold">Save settings</button>
        <a href="{{ route('home') }}" class="rounded-xl border border-white/20 px-6 py-3 text-sm text-white hover:bg-white/10">View storefront</a>
    </div>
</form>
@if($errors->any())<div class="mt-4 rounded-xl bg-red-400/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>@endif
@endsection
