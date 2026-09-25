@extends('admin.layout')
@section('title', 'E-commerce — Skinoveda Admin')
@section('content')
<h1 class="font-serif text-3xl text-white">E-commerce</h1>
<p class="mt-2 text-sm text-white/60">Products, brands, categories, and orders live here.</p>
<div class="mt-6 grid gap-4 sm:grid-cols-2">
    @foreach($sections as $key => $section)
        <a href="{{ route('admin.resource.index', ['resource' => $key]) }}" class="rounded-2xl border border-white/10 bg-white/[.06] p-6 text-white shadow-sm hover:border-[#e0a96d]/50">
            <div class="text-xs uppercase tracking-widest text-white/60">{{ $section['label'] }}</div>
            <div class="mt-2 font-serif text-4xl text-[#f0c897]">{{ $section['count'] }}</div>
            <p class="mt-3 text-sm text-white/70">{{ $section['text'] }}</p>
        </a>
    @endforeach
</div>
@endsection
