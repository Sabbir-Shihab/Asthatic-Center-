@extends('admin.layout')
@section('content')
<h1 class="font-serif text-3xl text-white">Overview</h1>
<p class="mt-2 text-sm text-white/60">Manage the Skinoveda shop and clinic content.</p>
<div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
@foreach($counts as $label=>$count)
<a href="{{ route('admin.resource.index',['resource'=>$label]) }}" class="rounded-2xl border border-white/10 bg-white/[.06] p-6 text-white shadow-sm hover:border-[#e0a96d]/50"><div class="text-xs uppercase tracking-widest text-white/60">{{ $label }}</div><div class="mt-2 font-serif text-4xl text-[#f0c897]">{{ $count }}</div></a>
@endforeach
</div>
@endsection
