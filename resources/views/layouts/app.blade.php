@php
    $siteName = site_setting('site_name', 'Skinoveda');
    $siteSubtitle = site_setting('site_subtitle', 'Aesthetic Care');
    $siteTagline = site_setting('site_tagline', 'NATURAL BEAUTY · MODERN SCIENCE · HOLISTIC WELLNESS');
    $siteDescription = site_setting('site_description', 'Expert aesthetic care, holistic wellness and premium skincare at Skinoveda.');
    $siteLogo = site_setting('site_logo');
    $siteFavicon = site_setting('site_favicon') ?: $siteLogo;
    $contactLocation = site_setting('contact_location', 'DHAKA, BANGLADESH');
    $contactAddress = site_setting('contact_address', "Gulshan Avenue, Dhaka\nBangladesh");
    $contactPhone = site_setting('contact_phone', '+880 1700 000000');
    $contactEmail = site_setting('contact_email', 'hello@skinoveda.local');
    $openingHours = site_setting('opening_hours', 'Sat-Thu, 10 am-8 pm');
    $footerAbout = site_setting('footer_about', 'A thoughtful blend of modern aesthetic science and holistic care, created just for you.');
    $newsletterHeading = site_setting('newsletter_heading', 'A little glow in your inbox');
    $newsletterText = site_setting('newsletter_text', 'New arrivals and thoughtful skin advice.');
    $telLink = preg_replace('/[^\d+]/', '', $contactPhone);
    $isTreatments = request()->routeIs('treatments.*');
    $isShop = request()->routeIs('shop.*');
    $isContact = request()->routeIs('contact');
    $isAbout = request()->routeIs('about');
    $isWellness = request()->routeIs('wellness');
    $isTeam = request()->routeIs('team');
    $isGallery = request()->routeIs('gallery') || request()->routeIs('results.*') || request()->routeIs('testimonials.*');
    $isBlog = request()->routeIs('blog');
    $isBook = request()->routeIs('book');
    $serviceCategories = \App\Models\ServiceCategory::publishedList();
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() === 'bn' ? 'bn' : 'en' }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ t($siteDescription) }}"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteName.' — '.t($siteSubtitle))</title>
    @if($siteFavicon)<link rel="icon" href="{{ media_url($siteFavicon) }}">@endif
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body x-data="store" x-init="successMessage = @js(session('appointment_submitted', ''))" @scroll.window="mobileOpen = false" class="antialiased">
<style>[x-cloak]{display:none!important}</style>
<div class="page-shell min-h-screen">
    <header class="site-header fixed inset-x-0 top-0 z-40 border-b border-white/10">
        <div class="site-topline container flex items-center justify-between py-3 text-[10px] tracking-[.18em] text-white/50"><span>{{ t($siteTagline) }}</span><span class="hidden sm:block">{{ t($contactLocation) }} &nbsp;·&nbsp; {{ $contactPhone }}</span></div>
        <div class="container flex h-[76px] items-center justify-between gap-5">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
                @if($siteLogo)
                    <img src="{{ media_url($siteLogo) }}" alt="{{ $siteName }}" class="h-11 w-11 rounded-full border border-[#e0a96d]/60 object-contain p-1">
                @else
                    <span class="grid h-11 w-11 place-items-center rounded-full border border-[#e0a96d]/60 text-2xl text-[#e0a96d]">✿</span>
                @endif
                <span><span class="block font-serif text-[20px] leading-tight">{{ $siteName }}</span><span class="site-subtitle text-[10px] tracking-[.2em] text-[#e0a96d]">{{ app()->getLocale() === 'bn' ? t($siteSubtitle) : strtoupper($siteSubtitle) }}</span></span>
            </a>
            <nav class="desktop-nav hidden min-w-0 items-center text-[12px] uppercase tracking-[.04em] text-white/75 xl:flex">
                <a href="{{ route('home') }}" data-nav="home" class="site-nav-link">{{ t('Home') }}</a>
                <a href="{{ route('about') }}" data-nav="about" class="site-nav-link {{ $isAbout ? 'is-active' : '' }}">{{ t('About Us') }}</a>
                <div class="services-menu group relative">
                    <a href="{{ route('treatments.index') }}" data-nav="services" class="site-nav-link {{ $isTreatments ? 'is-active' : '' }}">{{ t('Services') }} <span class="menu-caret">▾</span></a>
                    <div class="services-dropdown">
                        @foreach($serviceCategories as $service)
                            <a href="{{ route('treatments.category', $service->slug) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.</span>{{ t($service->name) }}</a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('shop.index') }}" data-nav="shop" class="site-nav-link {{ $isShop ? 'is-active' : '' }}">{{ t('Skincare Shop') }}</a>
                <a href="{{ route('team') }}" data-nav="team" class="site-nav-link {{ $isTeam ? 'is-active' : '' }}">{{ t('Our Team') }}</a>
                <a href="{{ route('gallery') }}" data-nav="gallery" class="site-nav-link {{ $isGallery ? 'is-active' : '' }}">{{ t('Gallery') }}</a>
                <a href="{{ route('wellness') }}" data-nav="wellness" class="site-nav-link {{ $isWellness ? 'is-active' : '' }}">{{ t('Wellness') }}</a>
                <a href="{{ route('blog') }}" data-nav="blog" class="site-nav-link {{ $isBlog ? 'is-active' : '' }}">{{ t('Blog') }}</a>
                <a href="{{ route('contact') }}" data-nav="contact" class="site-nav-link {{ $isContact ? 'is-active' : '' }}">{{ t('Contact') }}</a>
            </nav>
            <div class="header-actions flex shrink-0 items-center gap-2"><button @click="searchOpen=!searchOpen" aria-label="Search" class="grid h-9 w-9 place-items-center rounded-full text-white/80 hover:bg-white/10"><span class="site-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg></span></button><a href="{{ route('contact') }}" aria-label="Contact" class="hidden h-9 w-9 place-items-center rounded-full text-white/80 hover:bg-white/10 sm:grid xl:hidden"><span class="site-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4 21c1.3-5 5-7 8-7s6.7 2 8 7"/></svg></span></a><button @click="cartOpen=true" class="relative grid h-9 w-9 place-items-center rounded-full text-white/80 hover:bg-white/10" aria-label="Open shopping bag"><span class="site-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 9h12l-1 11H7L6 9Z"/><path d="M9 9V7a3 3 0 0 1 6 0v2"/></svg></span><span x-show="cartCount" x-text="cartCount" class="absolute -right-0.5 -top-0.5 grid h-4 w-4 place-items-center rounded-full bg-[#e0a96d] text-[9px] text-[#160e19]"></span></button>@include('partials.lang-switch')<a href="{{ route('book') }}" class="gold-button header-book hidden whitespace-nowrap rounded-full px-4 py-2 text-xs font-semibold sm:block">{{ t('Book Appointment') }}</a><button @click="mobileOpen=!mobileOpen" class="grid h-9 w-9 place-items-center text-xl xl:hidden"><span class="site-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></span></button></div>
        </div>
        <div x-show="searchOpen" x-cloak style="display:none" x-transition class="container pb-4"><form action="{{ route('shop.index') }}" class="mx-auto flex max-w-xl gap-2"><input name="q" class="input w-full" placeholder="{{ t('Search products and treatments...') }}"><button class="gold-button rounded-xl px-5">{{ t('Search') }}</button></form></div>
        <nav x-show="mobileOpen" x-cloak style="display:none" x-transition class="container grid gap-3 pb-5 text-sm xl:hidden">
            <a href="{{ route('home') }}" data-nav="home" class="site-nav-link">{{ t('Home') }}</a>
            <a href="{{ route('about') }}" data-nav="about" class="site-nav-link {{ $isAbout ? 'is-active' : '' }}">{{ t('About Us') }}</a>
            <a href="{{ route('treatments.index') }}" data-nav="services" class="site-nav-link {{ $isTreatments ? 'is-active' : '' }}">{{ t('Services') }}</a>
            <div class="mobile-service-list">
                @foreach($serviceCategories as $service)
                    <a href="{{ route('treatments.category', $service->slug) }}">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}. {{ t($service->name) }}</a>
                @endforeach
            </div>
            <a href="{{ route('shop.index') }}" data-nav="shop" class="site-nav-link {{ $isShop ? 'is-active' : '' }}">{{ t('Skincare Shop') }}</a>
            <a href="{{ route('team') }}" data-nav="team" class="site-nav-link {{ $isTeam ? 'is-active' : '' }}">{{ t('Our Team') }}</a>
            <a href="{{ route('gallery') }}" data-nav="gallery" class="site-nav-link {{ $isGallery ? 'is-active' : '' }}">{{ t('Gallery') }}</a>
            <a href="{{ route('wellness') }}" data-nav="wellness" class="site-nav-link {{ $isWellness ? 'is-active' : '' }}">{{ t('Wellness') }}</a>
            <a href="{{ route('blog') }}" data-nav="blog" class="site-nav-link {{ $isBlog ? 'is-active' : '' }}">{{ t('Blog') }}</a>
            <a href="{{ route('contact') }}" data-nav="contact" class="site-nav-link {{ $isContact ? 'is-active' : '' }}">{{ t('Contact') }}</a>
            <a href="{{ route('book') }}" class="gold-button rounded-full px-5 py-3 text-center text-xs font-semibold sm:hidden">{{ t('Book Appointment') }}</a>
        </nav>
    </header>
    @if(session('appointment_submitted'))
        <div class="site-notice site-notice-success" role="status"><span class="site-notice-icon">✓</span><span>{{ session('appointment_submitted') }}</span></div>
    @endif
    <div x-show="successMessage" x-cloak class="site-notice site-notice-success" role="status"><span class="site-notice-icon">✓</span><span x-text="successMessage"></span></div>
    <div x-show="successMessage" x-cloak x-transition.opacity class="success-popup-backdrop" role="dialog" aria-modal="true">
        <div x-transition.scale.95 class="success-popup-card" @click.outside="successMessage=''">
            <div class="success-popup-icon">✓</div>
            <p class="success-popup-eyebrow">{{ t('Thank you for choosing Skinoveda') }}</p>
            <h2>{{ t('Request successful') }}</h2>
            <p x-text="successMessage"></p>
            <button type="button" class="gold-button success-popup-button" @click="successMessage=''">{{ t('Continue shopping') }}</button>
        </div>
    </div>
    <main class="site-main">@yield('content')</main>
    <footer class="site-footer">
        <div class="container footer-top">
            <div class="footer-brand-card">
                <a href="{{ route('home') }}" class="footer-brand">
                    @if($siteLogo)
                        <img src="{{ media_url($siteLogo) }}" alt="{{ $siteName }}">
                    @else
                        <span class="footer-brand-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 20s-5-3-5-8c3 0 5 2 5 8Z"/><path d="M12 20s5-3 5-8c-3 0-5 2-5 8Z"/><path d="M12 17S8 13 12 5c4 8 0 12 0 12Z"/></svg></span>
                    @endif
                    <span><strong>{{ $siteName }}</strong><small>{{ app()->getLocale() === 'bn' ? t($siteSubtitle) : strtoupper($siteSubtitle) }}</small></span>
                </a>
                <p>{{ t($footerAbout) }}</p>
                <div class="footer-socials" aria-label="Social links">
                    <a href="#" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Instagram">◎</a>
                    <a href="#" aria-label="YouTube">▶</a>
                </div>
            </div>

            <div class="footer-links">
                <h3>{{ t('Explore') }}</h3>
                <a href="{{ route('home') }}">{{ t('Home') }}</a>
                <a href="{{ route('treatments.index') }}">{{ t('Treatments') }}</a>
                <a href="{{ route('shop.index') }}">{{ t('Skincare shop') }}</a>
                <a href="{{ route('wellness') }}">{{ t('Wellness') }}</a>
                <a href="{{ route('about') }}">{{ t('About') }}</a>
            </div>

            <div class="footer-links">
                <h3>{{ t('Care') }}</h3>
                <a href="{{ route('treatments.category', 'laser-aesthetic') }}">{{ t('Laser & Aesthetic') }}</a>
                <a href="{{ route('treatments.category', 'ayurveda-panchakarma') }}">{{ t('Ayurveda & Panchakarma') }}</a>
                <a href="{{ route('treatments.category', 'skin-dermatology') }}">{{ t('Skin & Dermatology') }}</a>
                <a href="{{ route('results.index') }}">{{ t('Before & After') }}</a>
                <a href="{{ route('testimonials.index') }}">{{ t('Client Stories') }}</a>
            </div>

            <div class="footer-contact-card">
                <h3>{{ t('Visit the clinic') }}</h3>
                <p>{!! nl2br(e(t($contactAddress))) !!}</p>
                <div class="footer-contact-item"><span>{{ t('Hours') }}</span><strong>{{ t($openingHours) }}</strong></div>
                <div class="footer-contact-item"><span>{{ t('Phone') }}</span><a href="tel:{{ $telLink }}">{{ $contactPhone }}</a></div>
                @if($contactEmail)<div class="footer-contact-item"><span>{{ t('Email') }}</span><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></div>@endif
            </div>
        </div>

        <div class="container footer-newsletter">
            <div><h3>{{ t($newsletterHeading) }}</h3><p>{{ t($newsletterText) }}</p></div>
            <form action="{{ route('newsletter.store') }}" method="POST">@csrf<input type="email" name="email" required placeholder="{{ t('Your email address') }}"><button class="gold-button">{{ t('Subscribe') }}</button></form>
        </div>

        <div class="container footer-bottom">
            <span>© {{ date('Y') }} {{ $siteName }} {{ t($siteSubtitle) }}. {{ t('Made with care.') }}</span>
            <div><a href="{{ route('contact') }}">{{ t('Contact') }}</a><a href="{{ route('book') }}">{{ t('Book Appointment') }}</a></div>
        </div>
    </footer>
</div>
<div x-show="cartOpen" x-transition.opacity class="fixed inset-0 z-50 bg-black/60" @click.self="cartOpen=false" @keydown.escape.window="cartOpen=false"><aside x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col bg-[#fbf9f6] p-6 text-[#201624]"><div class="flex items-center justify-between border-b border-black/10 pb-5"><div><h2 class="font-serif text-2xl">{{ t('Your bag') }}</h2><p class="mt-1 text-xs text-black/50" x-text="cartCount + @js(t(' thoughtfully chosen item(s)'))"></p></div><button @click="cartOpen=false" class="text-2xl">×</button></div><div class="flex-1 overflow-auto py-4"><template x-if="!cart.length"><div class="py-16 text-center"><div class="text-4xl">♡</div><p class="mt-4 text-sm text-black/55">{{ t('Your bag is waiting for a little glow.') }}</p><a href="{{ route('shop.index') }}" @click="cartOpen=false" class="mt-5 inline-block text-sm text-[#96653f]">{{ t('Explore the shop →') }}</a></div></template><template x-for="item in cart" :key="item.id"><div class="flex items-start justify-between gap-4 border-b border-black/10 py-4"><div><div class="font-medium" x-text="item.name"></div><div class="mt-1 text-xs text-black/50">{{ t('Qty') }} <span x-text="item.qty"></span></div></div><div class="text-right"><div class="text-sm" x-text="money(item.price * item.qty)"></div><button @click="remove(item.id)" class="mt-2 text-xs text-black/45 underline">{{ t('Remove') }}</button></div></div></template></div><div class="border-t border-black/10 pt-5"><div class="flex justify-between"><span>{{ t('Subtotal') }}</span><strong x-text="money(cartTotal)"></strong></div><p class="mt-2 text-xs text-black/50">{{ t('Shipping and taxes calculated at checkout.') }}</p><button @click="openCheckout()" class="gold-button mt-5 w-full rounded-xl py-4 text-sm font-semibold" :disabled="!cart.length">{{ t('Continue to checkout') }}</button></div></aside></div>
@include('partials.checkout')
<script>window.svI18n=@json(['checkoutError'=>t('Please check your details and try again.')])</script>
</body>
</html>
