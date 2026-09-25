@extends('admin.layout')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-3"><div><h1 class="font-serif text-3xl text-white">{{ $title }}</h1><p class="mt-2 text-sm text-white/60">{{ $rows->total() }} records</p></div>
@if(!in_array($resource,['appointments','orders']))<a href="{{ route('admin.resource.create',['resource'=>$resource]) }}" class="gold-button rounded-xl px-5 py-3 text-sm font-semibold">Add {{ \Illuminate\Support\Str::singular($title) }}</a>@endif
</div>
@if($resource === 'appointments')
    <form method="GET" class="mt-5 flex flex-wrap items-end gap-3 rounded-2xl border border-white/10 bg-white/[.06] p-4">
        <label class="grid gap-2 text-xs text-white/70">Category
            <select name="category" onchange="this.form.submit()" class="input min-w-56">
                <option value="">All categories</option>
                @foreach(\App\Models\Treatment::categories() as $key=>$label)
                    <option value="{{ $key }}" @selected(request('category')===$key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-2 text-xs text-white/70">Date
            <input type="date" name="date" value="{{ $selectedDate?->toDateString() }}" onchange="this.form.submit()" class="admin-date input">
        </label>
        <label class="grid gap-2 text-xs text-white/70">Sort
            <select name="sort" onchange="this.form.submit()" class="input min-w-48">
                <option value="date_desc" @selected(request('sort', 'date_desc')==='date_desc')>Date: newest</option>
                <option value="date_asc" @selected(request('sort')==='date_asc')>Date: oldest</option>
                <option value="doctor_asc" @selected(request('sort')==='doctor_asc')>Doctor: A to Z</option>
                <option value="doctor_desc" @selected(request('sort')==='doctor_desc')>Doctor: Z to A</option>
            </select>
        </label>
        <a href="{{ route('admin.resource.index',['resource'=>'appointments']) }}" class="rounded-xl border border-white/15 px-4 py-2 text-xs text-white">Clear</a>
    </form>
    @if($selectedDate)
        <p class="mt-3 text-sm text-[#f0c897]">Showing appointments for {{ $selectedDate->format('j/n/Y') }}, after the selected sort.</p>
    @elseif($dateError)
        <p class="mt-3 text-xs text-red-300">Choose a valid date from the calendar.</p>
    @endif
@endif
@if($resource === 'orders')
    <form method="GET" class="mt-5 flex flex-wrap items-end gap-3 rounded-2xl border border-white/10 bg-white/[.06] p-4">
        <label class="grid gap-2 text-xs text-white/70">Date
            <input type="date" name="date" value="{{ $selectedDate?->toDateString() }}" onchange="this.form.submit()" class="admin-date input">
        </label>
        <label class="grid gap-2 text-xs text-white/70">Sort
            <select name="sort" onchange="this.form.submit()" class="input min-w-48">
                <option value="date_desc" @selected(request('sort', 'date_desc')==='date_desc')>Newest first</option>
                <option value="date_asc" @selected(request('sort')==='date_asc')>Oldest first</option>
            </select>
        </label>
        <a href="{{ route('admin.resource.index',['resource'=>'orders']) }}" class="rounded-xl border border-white/15 px-4 py-2 text-xs text-white">Clear</a>
    </form>
    @if($selectedDate)
        <p class="mt-3 text-sm text-[#f0c897]">Showing orders for {{ $selectedDate->format('j/n/Y') }}, after the selected sort.</p>
    @elseif($dateError)
        <p class="mt-3 text-xs text-red-300">Choose a valid date from the calendar.</p>
    @endif
@endif
<div class="mt-5 overflow-x-auto rounded-2xl border border-white/10 bg-[#1b1023] shadow-lg"><table class="w-full text-left text-sm text-white"><thead class="border-b border-white/10 text-xs uppercase text-white/60"><tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Record details</th><th class="px-4 py-3">Updated</th><th class="px-4 py-3">Actions</th></tr></thead><tbody>
@forelse($rows as $row)
<tr class="border-b border-white/[.07]">
    <td class="px-4 py-4 text-white/75">#{{ $row->id }}</td>
    <td class="px-4 py-4">
        @if($resource === 'schedules')
            <strong class="text-white">{{ $row->doctor?->name ?? 'Doctor schedule' }}</strong>
            <div class="mt-1 grid gap-1 text-xs text-white/60 md:grid-cols-2">
                <span>Day: {{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$row->day_of_week] ?? $row->day_of_week }}</span>
                <span>Time: {{ substr($row->starts_at,0,5) }} - {{ substr($row->ends_at,0,5) }}</span>
                <span>Treatment: {{ $row->treatment?->name ?? 'All treatments' }}</span>
                <span>Interval: {{ $row->slot_interval_minutes }} min</span>
                <span>Capacity: {{ $row->capacity }}</span>
                <span>Available: {{ $row->is_available ? 'Yes' : 'No' }}</span>
            </div>
            @if($row->notes)<div class="mt-2 max-w-2xl rounded-xl bg-white/[.06] px-3 py-2 text-xs text-white/70">Notes: {{ $row->notes }}</div>@endif
        @elseif($resource === 'slots')
            <strong class="text-white">{{ $row->title ?: 'Appointment slot' }}</strong>
            <div class="mt-1 grid gap-1 text-xs text-white/60 md:grid-cols-2">
                <span>Time: {{ $row->starts_at?->format('M j, Y - g:i A') }}</span>
                <span>Treatment: {{ $row->treatment?->name ?? 'All treatments' }}</span>
                <span>Doctor: {{ $row->doctor?->name ?? 'Any available expert' }}</span>
                <span>Capacity: {{ $row->capacity }}</span>
                <span>Available: {{ $row->is_available ? 'Yes' : 'No' }}</span>
            </div>
            @if($row->notes)<div class="mt-2 max-w-2xl rounded-xl bg-white/[.06] px-3 py-2 text-xs text-white/70">Notes: {{ $row->notes }}</div>@endif
        @elseif($resource === 'appointments')
            <strong class="text-white">{{ $row->client_name }}</strong>
            <div class="mt-1 grid gap-1 text-xs text-white/60 md:grid-cols-2">
                <span>Email: {{ $row->email }}</span>
                <span>Phone: {{ $row->phone }}</span>
                <span>Treatment: {{ $row->treatment?->name ?? 'Not selected' }}</span>
                <span>Category: {{ $row->treatment?->categoryLabel() ?? (\App\Models\Treatment::categories()[$row->category] ?? 'Not selected') }}</span>
                <span>Focus: {{ $row->focusArea?->name ?? 'Not selected' }}</span>
                <span>Slot: {{ $row->appointment_at?->format('j/n/Y, g:i A') }}</span>
                <span>Doctor: {{ $row->doctor?->name ?? 'Any available expert' }}</span>
                <span>Status: {{ $row->status }}</span>
            </div>
            @if($row->notes)<div class="mt-2 max-w-2xl rounded-xl bg-white/[.06] px-3 py-2 text-xs text-white/70">Notes: {{ $row->notes }}</div>@endif
        @elseif($resource === 'orders')
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <strong class="text-white">{{ $row->order_number }}</strong>
                    <div class="mt-1 text-xs text-white/60">{{ $row->customer_name }} · {{ $row->phone }} · {{ $row->city }}</div>
                    <div class="mt-1 text-xs text-white/45">{{ $row->email }}</div>
                </div>
                <div class="rounded-xl border border-[#e0a96d]/25 bg-[#e0a96d]/10 px-3 py-2 text-right">
                    <span class="block text-[10px] uppercase tracking-widest text-[#e0a96d]">Total</span>
                    <strong class="text-white">৳ {{ number_format($row->total, 2) }}</strong>
                </div>
            </div>
            <div class="mt-3 overflow-hidden rounded-2xl border border-white/10">
                <div class="grid grid-cols-[1fr_70px_95px_95px] bg-white/[.06] px-3 py-2 text-[10px] uppercase tracking-widest text-white/45">
                    <span>Product</span><span class="text-center">Qty</span><span class="text-right">Price</span><span class="text-right">Subtotal</span>
                </div>
                @forelse($row->items as $item)
                    <div class="grid grid-cols-[1fr_70px_95px_95px] border-t border-white/[.07] px-3 py-2 text-xs text-white/70">
                        <span class="font-medium text-white">{{ $item->product_name }}</span>
                        <span class="text-center">{{ $item->quantity }} pcs</span>
                        <span class="text-right">৳ {{ number_format($item->unit_price, 2) }}</span>
                        <span class="text-right text-[#f0c897]">৳ {{ number_format($item->line_total, 2) }}</span>
                    </div>
                @empty
                    <div class="px-3 py-3 text-xs text-white/50">No order items saved.</div>
                @endforelse
            </div>
            <div class="mt-3 grid gap-1 text-xs text-white/55 md:grid-cols-2">
                <span>Shipping address: {{ $row->shipping_address }}</span>
                <span>Payment: {{ $row->payment_method_name ?: strtoupper($row->payment_method) }}@if($row->payment_account) · {{ $row->payment_account }}@endif</span>
                <span>Status: {{ $row->status }} · Payment {{ $row->payment_status ?? 'pending' }}</span>
                <span>Order date: {{ $row->created_at?->format('j/n/Y, g:i A') }}</span>
                <span>Send money: ৳ {{ number_format($row->advance_amount ?? 0, 2) }} · TRX: {{ $row->transaction_id ?: 'Not submitted' }}</span>
                <span>Subtotal: ৳ {{ number_format($row->subtotal, 2) }} · Shipping: ৳ {{ number_format($row->shipping, 2) }}</span>
            </div>
        @elseif($resource === 'payment-methods')
            <strong class="text-white">{{ $row->name }}</strong>
            <div class="mt-1 text-xs text-white/60">{{ $row->account_number ?: 'Number not set' }} · Send ৳ {{ number_format($row->advance_amount, 2) }}</div>
            <div class="mt-1 text-xs {{ $row->is_active ? 'text-emerald-300' : 'text-white/45' }}">{{ $row->is_active ? 'Visible at checkout' : 'Hidden from checkout' }}</div>
        @elseif($resource === 'focus-areas')
            <strong class="text-white">{{ $row->name }}</strong>
            <div class="mt-1 text-xs text-white/60">{{ $row->category?->name ?? 'No service' }} · {{ $row->slug }}</div>
            @if($row->summary)<div class="mt-2 max-w-2xl text-xs text-white/55">{{ $row->summary }}</div>@endif
        @elseif(in_array($resource, ['products', 'doctors', 'gallery', 'before-afters'], true))
            <div class="flex items-start gap-3">
                @php($preview = $resource === 'products' ? $row->image : ($resource === 'doctors' ? $row->photo : ($row->after_image ?: $row->before_image)))
                @if($preview)<img src="{{ $preview }}" alt="" class="h-14 w-14 rounded-lg object-cover">@endif
                <div><strong class="text-white">{{ $row->name ?? $row->title }}</strong><div class="mt-1 text-xs text-white/55">{{ $row->description ?? $row->designation ?? $row->treatment ?? '' }}</div></div>
            </div>
        @else
            <strong class="text-white">{{ $row->name ?? $row->title ?? $row->client_name ?? $row->customer_name ?? $row->order_number ?? 'Record' }}</strong>
            <div class="mt-1 max-w-lg truncate text-xs text-white/55">{{ $row->email ?? $row->slug ?? $row->status ?? '' }}</div>
        @endif
    </td>
    <td class="px-4 py-4 text-xs text-white/60">{{ $row->updated_at?->format('M j, Y') }}</td>
    <td class="px-4 py-4">
        @if($resource === 'orders')
            <div class="grid gap-2">
                @if($row->status !== 'confirmed')
                    <form method="POST" action="{{ route('admin.orders.confirm', $row->id) }}">@csrf<button class="rounded-full bg-[#e0a96d] px-3 py-1 text-xs font-semibold text-[#21132a]">Confirm order</button></form>
                @endif
                <span class="rounded-full bg-white/10 px-2 py-1 text-xs text-white/80">{{ $row->status }}</span>
                <form method="POST" action="{{ route('admin.resource.destroy',['resource'=>$resource,'id'=>$row->id]) }}" onsubmit="return confirm('Delete this order?')">@csrf @method('DELETE')<button class="text-left text-sm text-red-300">Delete</button></form>
            </div>
        @else
            <div class="flex items-center gap-3">
                @if($resource !== 'appointments')<a href="{{ route('admin.resource.edit',['resource'=>$resource,'id'=>$row->id]) }}" class="text-[#f0c897]">Edit</a>@endif
                <form method="POST" action="{{ route('admin.resource.destroy',['resource'=>$resource,'id'=>$row->id]) }}" onsubmit="return confirm('Delete this record?')">@csrf @method('DELETE')<button class="text-red-300">Delete</button></form>
            </div>
        @endif
    </td>
</tr>
@empty<tr><td colspan="4" class="px-4 py-12 text-center text-white/55">No records yet.</td></tr>@endforelse
</tbody></table></div><div class="mt-5 text-white">{{ $rows->links() }}</div>
@endsection
