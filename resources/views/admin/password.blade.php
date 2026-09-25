@extends('admin.layout')
@section('content')
<h1 class="font-serif text-3xl">Change password</h1>
<p class="mt-2 text-sm text-white/60">Update the password for your admin account.</p>
<form method="POST" action="{{ route('admin.password.update') }}" class="mt-6 max-w-xl rounded-2xl border border-white/10 bg-white/[.06] p-5">
    @csrf @method('PUT')
    @if($errors->any())<p class="mb-4 rounded-xl bg-red-500/10 p-3 text-sm text-red-200">{{ $errors->first() }}</p>@endif
    <label class="block text-xs">Current password<input name="current_password" type="password" required class="input mt-2 w-full"></label>
    <label class="mt-4 block text-xs">New password<input name="password" type="password" minlength="8" required class="input mt-2 w-full"></label>
    <label class="mt-4 block text-xs">Confirm new password<input name="password_confirmation" type="password" minlength="8" required class="input mt-2 w-full"></label>
    <button class="gold-button mt-5 rounded-xl px-5 py-3 text-sm font-semibold">Save password</button>
</form>
@endsection
