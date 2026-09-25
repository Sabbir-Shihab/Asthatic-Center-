@extends('admin.layout')
@section('content')
<h1 class="font-serif text-3xl text-white">Website Hero Banner</h1>
<p class="mt-2 text-sm text-white/60">This image controls the main homepage hero banner.</p>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.hero.update') }}" class="mt-6 grid gap-5 rounded-2xl border border-white/10 bg-[#1b1023] p-6 text-white shadow-lg">
    @csrf
    @if($heroBanner)
        <div>
            <div class="mb-2 text-xs uppercase tracking-widest text-white/50">Current banner</div>
            <img src="{{ media_url($heroBanner) }}" alt="Current website hero banner" class="h-48 w-full rounded-xl border border-white/15 object-cover">
        </div>
    @endif
    <label class="block text-xs text-white/75">
        Hero Banner
        <small class="mt-1 block text-[#e0a96d]">Recommended size: 1600 x 420 px. JPG, PNG, WebP or AVIF. Max 5 MB.</small>
        <input type="file" name="hero_banner_upload" accept="image/jpeg,image/png,image/webp,image/avif" class="mt-3 block w-full rounded-xl border border-white/15 bg-white/5 p-2 text-xs text-white file:mr-3 file:rounded-lg file:border-0 file:bg-[#e0a96d] file:px-3 file:py-2 file:text-[#21132a]">
    </label>
    <label class="block text-xs text-white/75">
        Or paste an image URL
        <input name="hero_banner" value="{{ old('hero_banner', $heroBanner) }}" class="input mt-2 w-full" placeholder="https://example.com/banner.jpg">
    </label>
    <div class="flex gap-3">
        <button class="gold-button rounded-xl px-6 py-3 text-sm font-semibold">Save banner</button>
        <a href="{{ route('home') }}" class="rounded-xl border border-white/20 px-6 py-3 text-sm text-white hover:bg-white/10">View storefront</a>
    </div>
</form>
@if($errors->any())<div class="mt-4 rounded-xl bg-red-400/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>@endif
@endsection
