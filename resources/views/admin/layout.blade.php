<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Skinoveda Admin')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="admin-panel min-h-screen bg-[#100819] text-[#f8f3f8]">
    <header class="border-b border-white/10 bg-[#1b0d25] text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
            <a href="{{ route('admin.dashboard') }}" class="font-serif text-xl">Skinoveda <span class="text-xs text-[#e0a96d]">ADMIN</span></a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="rounded-full border border-white/20 px-4 py-2 text-xs text-white hover:bg-white/10">Sign out</button>
            </form>
        </div>
    </header>
    <div class="mx-auto grid max-w-7xl gap-6 px-5 py-8 md:grid-cols-[220px_1fr]">
        @php
            $shopPages = ['products' => 'Products', 'brands' => 'Brands', 'categories' => 'Categories', 'orders' => 'Orders'];
            $currentResource = (string) request()->route('resource');
            $inShop = request()->routeIs('admin.ecommerce') || isset($shopPages[$currentResource]);
        @endphp
        <nav class="grid h-fit grid-cols-2 gap-2 rounded-2xl border border-white/10 bg-white/[.06] p-3 shadow-lg md:grid-cols-1">
            <a href="{{ route('admin.ecommerce') }}" class="rounded-xl px-3 py-2.5 text-sm font-medium {{ $inShop ? 'bg-white/10 text-[#f0c897]' : 'text-white/85 hover:bg-white/10 hover:text-[#f0c897]' }}">E-commerce</a>
            @foreach(['service-categories'=>'Service Categories','focus-areas'=>'Focus Areas','treatments'=>'Signature Treatments','blog'=>'Blog Posts','doctors'=>'Doctors','appointments'=>'Appointments','testimonials'=>'Testimonials','gallery'=>'Gallery','before-afters'=>'Before & Afters','payment-methods'=>'Payment Methods'] as $key=>$label)
                <a href="{{ route('admin.resource.index',['resource'=>$key]) }}" class="rounded-xl px-3 py-2.5 text-sm font-medium text-white/85 hover:bg-white/10 hover:text-[#f0c897]">{{ $label }}</a>
            @endforeach
            <a href="{{ route('admin.users.index') }}" class="rounded-xl px-3 py-2.5 text-sm font-medium text-white/85 hover:bg-white/10 hover:text-[#f0c897]">Users & Managers</a>
            <a href="{{ route('admin.password') }}" class="rounded-xl px-3 py-2.5 text-sm font-medium text-white/85 hover:bg-white/10 hover:text-[#f0c897]">Change Password</a>
            <a href="{{ route('admin.settings') }}" class="rounded-xl px-3 py-2.5 text-sm font-medium text-white/85 hover:bg-white/10 hover:text-[#f0c897]">Settings</a>
            <a href="{{ route('home') }}" class="mt-2 rounded-xl border-t border-white/10 px-3 py-3 text-sm text-[#e0a96d]">← View Main site</a>
        </nav>
        <main>
            @if($inShop && ! request()->routeIs('admin.ecommerce'))
                <nav class="mb-5 flex flex-wrap gap-2">
                    @foreach($shopPages as $key => $label)
                        <a href="{{ route('admin.resource.index', ['resource' => $key]) }}" class="rounded-full px-4 py-2 text-xs font-semibold {{ $currentResource === $key ? 'bg-[#e0a96d] text-[#21132a]' : 'border border-white/15 text-white/80 hover:text-[#f0c897]' }}">{{ $label }}</a>
                    @endforeach
                </nav>
            @endif
            @if(session('saved'))
                <div class="mb-4 rounded-xl border border-green-300/20 bg-green-400/10 px-4 py-3 text-sm text-green-200">{{ session('saved') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    <style>
        .admin-panel input:not([type=checkbox]),.admin-panel textarea,.admin-panel select{color:#24192a;background:#fff}
        .admin-panel input::placeholder,.admin-panel textarea::placeholder{color:#807580}
        .admin-panel table{color:#f8f3f8}
        .admin-panel table th{color:#d6c8d9}
        .admin-panel table td{color:#f4edf5}
        .admin-panel table td .text-\[\#887c88\]{color:#c1b3c4}
        .admin-panel a{transition:background-color .18s,color .18s}
    </style>
</body>
</html>



