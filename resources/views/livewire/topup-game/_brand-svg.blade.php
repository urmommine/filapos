@props([
    'slug' => '',
    'class' => 'w-5 h-5',
])

@php
    $s = strtolower($slug ?? '');
@endphp

@if(str_contains($s, 'mobile-legends') || str_contains($s, 'mlbb'))
    {{-- Mobile Legends: Shield & Crossed Swords --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
    </svg>

@elseif(str_contains($s, 'free-fire') || str_contains($s, 'ff'))
    {{-- Free Fire: Flame --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
    </svg>

@elseif(str_contains($s, 'genshin') || str_contains($s, 'honkai'))
    {{-- Genshin Impact: Primogem Sparkle Star --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
    </svg>

@elseif(str_contains($s, 'pubg'))
    {{-- PUBG Mobile: Target / Crosshair Scope --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="12" cy="12" r="4" stroke-linecap="round" stroke-linejoin="round" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4M2 12h4m12 0h4" />
    </svg>

@elseif(str_contains($s, 'valorant'))
    {{-- Valorant: Emblem --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18.5 4L12 19.5 5.5 4h4.5l2 5.5 2-5.5h4.5z" />
    </svg>

@elseif(str_contains($s, 'honor') || str_contains($s, 'kings'))
    {{-- Honor of Kings: Crown --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 18h18M3 18l3-11 6 5 6-5 3 11H3z" />
    </svg>

@elseif(str_contains($s, 'roblox'))
    {{-- Roblox: 3D Cube Block --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
    </svg>

@elseif(str_contains($s, 'cod') || str_contains($s, 'duty'))
    {{-- Call of Duty: Crosshair Target --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8" />
    </svg>

@elseif(str_contains($s, 'steam'))
    {{-- Steam Wallet: Controller & Wallet --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
    </svg>

@elseif(str_contains($s, 'google') || str_contains($s, 'play'))
    {{-- Google Play: Gift / Card --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>

@elseif(str_contains($s, 'dana') || str_contains($s, 'wallet') || str_contains($s, 'ovo') || str_contains($s, 'gopay'))
    {{-- E-Wallet --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>

@elseif(str_contains($s, 'pulsa') || str_contains($s, 'data') || str_contains($s, 'pln'))
    {{-- Pulsa / PLN / Token --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
    </svg>

@else
    {{-- Generic Gaming Controller SVG --}}
    <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0-4h2m-2 0h-2m8 1a6 6 0 11-12 0 6 6 0 0112 0zM7 11h2m-1-1v2m-5 4c0 3.314 3.134 6 7 6s7-2.686 7-6" />
    </svg>
@endif
