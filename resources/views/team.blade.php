@extends('layouts.app')
@section('title', t('Our Team').' — '.site_setting('site_name', 'Skinoveda'))
@section('content')
<section class="simple-page-hero"><div class="container"><span class="eyebrow">{{ t('Our Team') }}</span><h1>{{ t('Meet the experts behind your care.') }}</h1><p>{{ t('Experienced doctors and wellness specialists guiding every treatment with calm, personal attention.') }}</p></div></section>
<section class="simple-light"><div class="container team-page-grid">
@forelse($doctors as $doctor)
<a href="{{ route('doctors.show', $doctor) }}" class="team-page-card"><img src="{{ $doctor->photo ?: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=600&q=85' }}" alt="{{ t($doctor->name) }}"><div><span>{{ t($doctor->designation) }}</span><h2>{{ t($doctor->name) }}</h2><p>{{ t($doctor->quote ?: 'Expert-led care designed around your skin and wellness goals.') }}</p><strong>{{ t('View profile →') }}</strong></div></a>
@empty
<div class="empty-page-card">{{ t('Team profiles will appear here after admin adds doctors.') }}</div>
@endforelse
</div></section>
@endsection
