@extends('admin.layout')
@section('content')
<h1 class="font-serif text-3xl text-white">{{ $row ? 'Edit' : 'Add' }} {{ \Illuminate\Support\Str::singular($title) }}</h1>
<form method="POST" enctype="multipart/form-data" action="{{ $row ? route('admin.resource.update',['resource'=>$resource,'id'=>$row->id]) : route('admin.resource.store',['resource'=>$resource]) }}" class="mt-6 grid gap-4 rounded-2xl border border-white/10 bg-[#1b1023] p-6 text-white shadow-lg sm:grid-cols-2">@csrf @if($row) @method('PUT') @endif
@foreach($fields as $field)
@continue(in_array($field,['id','created_at','updated_at']))
@php
    $image=in_array($field,['image','photo','hero_photo','logo','client_photo','before_image','after_image']);
    $storedValue=$image ? $row?->getRawOriginal($field) : $row?->$field;
    $value=old($field,$storedValue);
    $checkbox=in_array($field,['is_featured','is_active','is_approved','is_published','is_available']);
    $json=in_array($field,['skin_types','credentials','focus_areas']);
    $textarea=in_array($field,['description','bio','quote','review','caption','notes','shipping_address','short_description','excerpt','content','overview','who_for','what_to_expect','care_note','summary','summary_bn','details','details_bn','who_for_bn','what_to_expect_bn','care_note_bn','instructions']);
    $dateTime=in_array($field,['starts_at','appointment_at']) && $resource !== 'schedules';
    $scheduleTime=in_array($field,['starts_at','ends_at']) && $resource === 'schedules';
    $imageHints=[];
@endphp
@if($checkbox)
    <label class="flex items-start gap-2 text-sm text-white"><input type="checkbox" name="{{ $field }}" value="1" @checked((bool)$value || (!$row && in_array($field,['is_available','is_active']))) class="mt-1 accent-[#e0a96d]"><span>{{ $resource === 'treatments' && $field === 'is_featured' ? 'Show on Home Signature Treatments' : \Illuminate\Support\Str::headline($field) }}@if($resource === 'treatments' && $field === 'is_featured')<small class="mt-1 block text-xs text-[#e0a96d]">Maximum 4 featured treatments show on the home page.</small>@endif</span></label>
@elseif($field === 'treatment_id')
    <label class="block text-xs text-white/75">Treatment<select name="{{ $field }}" class="input mt-2 w-full"><option value="">All treatments / not selected</option>@foreach(\App\Models\Treatment::where('is_active', true)->orderBy('name')->get() as $treatment)<option value="{{ $treatment->id }}" @selected((string)$value === (string)$treatment->id)>{{ $treatment->name }}</option>@endforeach</select></label>
@elseif($field === 'doctor_id')
    <label class="block text-xs text-white/75">Doctor<select name="{{ $field }}" class="input mt-2 w-full"><option value="">Any available expert</option>@foreach(\App\Models\Doctor::where('is_active', true)->orderBy('name')->get() as $doctor)<option value="{{ $doctor->id }}" @selected((string)$value === (string)$doctor->id)>{{ $doctor->name }} - {{ $doctor->designation }}</option>@endforeach</select></label>
@elseif($field === 'brand_id' && $resource === 'products')
    <label class="block text-xs text-white/75">Brand<select name="{{ $field }}" class="input mt-2 w-full" required><option value="">Select brand</option>@foreach(\App\Models\Brand::where('is_active', true)->orderBy('name')->get() as $brand)<option value="{{ $brand->id }}" @selected((string)$value === (string)$brand->id)>{{ $brand->name }}</option>@endforeach</select><small class="mt-1 block text-[#e0a96d]">Select product brand from Admin → Brands.</small></label>
@elseif($field === 'category_id' && $resource === 'products')
    <label class="block text-xs text-white/75">Product Category<select name="{{ $field }}" class="input mt-2 w-full" required><option value="">Select category</option>@foreach(\App\Models\Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $category)<option value="{{ $category->id }}" @selected((string)$value === (string)$category->id)>{{ $category->name }}</option>@endforeach</select><small class="mt-1 block text-[#e0a96d]">Select product category from Admin → Categories.</small></label>@elseif($field === 'category' && $resource === 'treatments')
    <label class="block text-xs text-white/75">Treatment Category<select name="{{ $field }}" class="input mt-2 w-full">@foreach(\App\Models\Treatment::categories() as $key=>$label)<option value="{{ $key }}" @selected(($value ?: 'laser-aesthetic') === $key)>{{ $label }}</option>@endforeach</select></label>
@elseif($field === 'service_category_id')
    <label class="block text-xs text-white/75 sm:col-span-2">Service<select name="service_category_id" class="input mt-2 w-full" required><option value="">Select service</option>@foreach(\App\Models\ServiceCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $service)<option value="{{ $service->id }}" @selected((string)$value === (string)$service->id)>{{ $service->name }}</option>@endforeach</select><small class="mt-1 block text-[#e0a96d]">This focus area appears inside the selected service page.</small></label>
@elseif($resource === 'payment-methods' && $field === 'account_number')
    <label class="block text-xs text-white/75">Wallet number<input name="account_number" value="{{ $value }}" class="input mt-2 w-full" placeholder="01XXXXXXXXX"><small class="mt-1 block text-[#e0a96d]">Shown at checkout. Required while this method is active.</small></label>
@elseif($resource === 'payment-methods' && $field === 'advance_amount')
    <label class="block text-xs text-white/75">Send money amount (BDT)<input type="number" name="advance_amount" min="0" step="0.01" value="{{ $value === null || $value === '' ? '0' : $value }}" class="input mt-2 w-full" required><small class="mt-1 block text-[#e0a96d]">Delivery advance the customer sends before the order is confirmed. Active methods need at least 1.</small></label>
@elseif($field === 'day_of_week')
    <label class="block text-xs text-white/75">Day Of Week<select name="{{ $field }}" class="input mt-2 w-full">@foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i=>$day)<option value="{{ $i }}" @selected((string)$value === (string)$i)>{{ $day }}</option>@endforeach</select></label>
@elseif($image)
    <div class="block text-xs text-white/75">{{ \Illuminate\Support\Str::headline($field) }}@isset($imageHints[$field])<small class="mt-1 block text-[#e0a96d]">{{ $imageHints[$field] }}</small>@endisset @if($value)<img src="{{ media_url($value) }}" alt="Current {{ $field }}" class="my-2 h-24 w-24 rounded-xl border border-white/15 object-cover">@endif<input type="file" name="{{ $field }}_upload" accept="image/jpeg,image/png,image/webp,image/avif" class="mt-2 block w-full rounded-xl border border-white/15 bg-white/5 p-2 text-xs text-white file:mr-3 file:rounded-lg file:border-0 file:bg-[#e0a96d] file:px-3 file:py-2 file:text-[#21132a]"><input name="{{ $field }}" value="{{ $value }}" class="input mt-2 w-full" placeholder="Or paste an image URL"></div>
@else
    <label class="block text-xs text-white/75 {{ $textarea ? 'sm:col-span-2' : '' }}">{{ \Illuminate\Support\Str::headline($field) }}
        @if($json)
            @if($field === 'focus_areas' && $resource === 'service-categories')
                <textarea name="{{ $field }}" rows="4" class="input mt-2 w-full" placeholder="One focus area per line, or comma-separated">{{ is_array($value) ? implode("\n", $value) : $value }}</textarea>
                <small class="mt-1 block text-[#e0a96d]">These show as Included focus areas on the public service pages. For full detail pages, use Admin → Focus Areas.</small>
            @else
                <input name="{{ $field }}[]" value="{{ is_array($value)?implode(', ',$value):$value }}" class="input mt-2 w-full" placeholder="Comma-separated values">
            @endif
        @elseif($textarea)
            <textarea name="{{ $field }}" rows="3" class="input mt-2 w-full">{{ is_array($value)?json_encode($value):$value }}</textarea>
        @elseif($dateTime)
            <input type="datetime-local" name="{{ $field }}" value="{{ $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : '' }}" class="input mt-2 w-full" @required($field === 'starts_at')>
        @elseif($scheduleTime)
            <input type="time" name="{{ $field }}" value="{{ $value ? substr((string)$value,0,5) : '' }}" class="input mt-2 w-full" required>
        @else
            <input name="{{ $field }}" value="{{ is_array($value)?json_encode($value):$value }}" class="input mt-2 w-full" @required(in_array($field,['name','slug','brand_id','category_id','client_name']))>
        @endif
    </label>
@endif
@endforeach
<div class="flex gap-3 sm:col-span-2"><button class="gold-button rounded-xl px-6 py-3 text-sm font-semibold">Save record</button><a href="{{ route('admin.resource.index',['resource'=>$resource]) }}" class="rounded-xl border border-white/20 px-6 py-3 text-sm text-white hover:bg-white/10">Cancel</a></div></form>
@if($errors->any())<div class="mt-4 rounded-xl bg-red-400/10 p-4 text-sm text-red-200">{{ $errors->first() }}</div>@endif
@if($resource === 'service-categories' && $row)
@php($focusAreas = $row->focusAreas()->get())
<section class="mt-6 rounded-2xl border border-white/10 bg-[#1b1023] p-6 text-white shadow-lg">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="font-serif text-2xl">Included focus areas</h2>
            <p class="mt-1 text-sm text-white/60">Add or delete focus areas for this service. Active items show on the public services and service detail pages.</p>
        </div>
        <a href="{{ route('treatments.category', $row->slug) }}" target="_blank" class="rounded-xl border border-white/20 px-4 py-2 text-xs text-white hover:bg-white/10">View on site</a>
    </div>
    <form method="POST" action="{{ route('admin.service-categories.focus-areas.store', $row->id) }}" class="mt-5 grid gap-4 rounded-2xl border border-white/10 bg-white/[.04] p-4 sm:grid-cols-2">@csrf
        <label class="block text-xs text-white/75">Name<input name="name" class="input mt-2 w-full" placeholder="Acne Scar Care" required></label>
        <label class="block text-xs text-white/75">Bangla name<input name="name_bn" class="input mt-2 w-full" placeholder="Optional"></label>
        <label class="block text-xs text-white/75 sm:col-span-2">Short summary<textarea name="summary" rows="2" class="input mt-2 w-full" placeholder="Optional detail for the focus area page"></textarea></label>
        <label class="block text-xs text-white/75">Sort order<input type="number" min="0" name="sort_order" value="{{ ($focusAreas->max('sort_order') ?? 0) + 1 }}" class="input mt-2 w-full"></label>
        <label class="flex items-center gap-2 text-sm text-white"><input type="checkbox" name="is_active" value="1" checked class="accent-[#e0a96d]">Visible on site</label>
        <div class="sm:col-span-2"><button class="gold-button rounded-xl px-5 py-3 text-sm font-semibold">Add focus area</button></div>
    </form>
    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
        @forelse($focusAreas as $focus)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/[.07] px-4 py-3 last:border-b-0">
                <div>
                    <strong class="text-white">{{ $focus->name }}</strong>
                    <div class="mt-1 text-xs text-white/55">{{ $focus->slug }} · Order {{ $focus->sort_order }} · {{ $focus->is_active ? 'Visible' : 'Hidden' }}</div>
                    @if($focus->summary)<p class="mt-1 max-w-2xl text-xs text-white/50">{{ $focus->summary }}</p>@endif
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.resource.edit', ['resource' => 'focus-areas', 'id' => $focus->id]) }}" class="text-sm text-[#f0c897]">Edit details</a>
                    <form method="POST" action="{{ route('admin.service-categories.focus-areas.destroy', [$row->id, $focus->id]) }}" onsubmit="return confirm('Delete this focus area?')">@csrf @method('DELETE')<button class="text-sm text-red-300">Delete</button></form>
                </div>
            </div>
        @empty
            <div class="px-4 py-8 text-sm text-white/55">No included focus areas yet.</div>
        @endforelse
    </div>
</section>
@endif
@endsection
