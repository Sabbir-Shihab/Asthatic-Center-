@extends('admin.layout')
@section('content')
<h1 class="font-serif text-3xl">Admin users</h1>
<p class="mt-2 text-sm text-white/60">Managers can access and manage every section of the admin panel.</p>
<div class="mt-6 grid gap-6 xl:grid-cols-[1fr_1.2fr]">
    <form method="POST" action="{{ route('admin.users.store') }}" class="rounded-2xl border border-white/10 bg-white/[.06] p-5">
        @csrf
        <h2 class="font-serif text-xl">Add manager</h2>
        @if($errors->any())<p class="mt-3 rounded-xl bg-red-500/10 p-3 text-sm text-red-200">{{ $errors->first() }}</p>@endif
        <label class="mt-4 block text-xs">Name<input name="name" required class="input mt-2 w-full"></label>
        <label class="mt-4 block text-xs">Email<input name="email" type="email" required class="input mt-2 w-full"></label>
        <label class="mt-4 block text-xs">Initial password<input name="password" type="password" minlength="8" required class="input mt-2 w-full"></label>
        <label class="mt-4 block text-xs">Confirm password<input name="password_confirmation" type="password" minlength="8" required class="input mt-2 w-full"></label>
        <button class="gold-button mt-5 rounded-xl px-5 py-3 text-sm font-semibold">Create manager</button>
    </form>
    <section class="rounded-2xl border border-white/10 bg-white/[.06] p-5">
        <h2 class="font-serif text-xl">Accounts</h2>
        <div class="mt-4 divide-y divide-white/10">
            @foreach($users as $user)
                <div class="flex flex-wrap items-center justify-between gap-3 py-4">
                    <div><p class="font-medium">{{ $user->name }} <span class="ml-2 rounded-full bg-white/10 px-2 py-1 text-[10px] uppercase tracking-wide text-[#f0c897]">{{ $user->role }}</span></p><p class="mt-1 text-xs text-white/55">{{ $user->email }}</p></div>
                    @if($user->role !== 'admin' && $user->id !== (int) session('skinoveda_admin_id'))
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this manager account?')">@csrf @method('DELETE')<button class="rounded-full border border-red-300/30 px-3 py-2 text-xs text-red-200">Delete</button></form>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
