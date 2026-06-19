<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Parkir') }} — @yield('title', 'Sistem Manajemen Parkir')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">

        <div class="flex h-screen overflow-hidden">

            {{-- ===== SIDEBAR ===== --}}
            <aside class="w-64 bg-gray-900 text-gray-100 flex flex-col shrink-0">

                {{-- Logo / App Name --}}
                <div class="flex items-center gap-2 px-6 py-5 border-b border-gray-700">
                    <svg class="h-7 w-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 17l4 4 4-4m-4-5v9M4 6h16M4 10h16" />
                    </svg>
                    <span class="text-lg font-semibold tracking-wide">Sistem Parkir</span>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

                    {{-- Semua role: POS --}}
                    <p class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-gray-400">POS Gerbang</p>

                    <a href="{{ route('pos.entry') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                               {{ request()->routeIs('pos.entry') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14" />
                        </svg>
                        Entry Gate
                    </a>

                    <a href="{{ route('pos.exit') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                               {{ request()->routeIs('pos.exit') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                        Exit Gate
                    </a>

                    {{-- Hanya admin --}}
                    @if (auth()->check() && auth()->user()->role === 'admin')
                        <div class="pt-4">
                            <p class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-gray-400">Admin</p>

                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                                       {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </a>

                            <a href="{{ route('allotment') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                                       {{ request()->routeIs('allotment') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Allotment
                            </a>

                            <a href="{{ route('members') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                                       {{ request()->routeIs('members') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Members
                            </a>

                            <a href="{{ route('users') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                                       {{ request()->routeIs('users') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Users
                            </a>

                            <a href="{{ route('rates') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                                       {{ request()->routeIs('rates') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Payments &amp; Rates
                            </a>

                            <a href="{{ route('report') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                                       {{ request()->routeIs('report') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Report
                            </a>
                        </div>
                    @endif

                </nav>
            </aside>

            {{-- ===== MAIN AREA ===== --}}
            <div class="flex-1 flex flex-col overflow-hidden">

                {{-- ===== TOPBAR ===== --}}
                <header class="bg-white shadow-sm border-b border-gray-200 shrink-0">
                    <div class="flex items-center justify-between px-6 py-3">
                        <h1 class="text-base font-semibold text-gray-700">@yield('title', 'Sistem Manajemen Parkir')</h1>

                        <div class="flex items-center gap-4">
                            {{-- Nama user --}}
                            <span class="text-sm text-gray-600 font-medium">
                                {{ auth()->user()->name }}
                                <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ auth()->user()->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700' }}">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </span>

                            {{-- Tombol Logout --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-red-600 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                {{-- ===== CONTENT AREA ===== --}}
                <main class="flex-1 overflow-y-auto p-6">

                    {{-- Flash error (dari CheckRole redirect) --}}
                    @if (session('error'))
                        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')

                </main>

            </div>
        </div>

        @stack('scripts')
    </body>
</html>
