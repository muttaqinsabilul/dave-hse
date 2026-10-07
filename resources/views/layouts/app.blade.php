<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HSE Pantau — Monitoring K3')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23059669'><path d='M12 2L3 6v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V6l-9-4zm1 14h-2v-3H8v-2h3V8h2v3h3v2h-3v3z'/></svg>">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-mesh-pattern font-sans text-slate-800 antialiased selection:bg-emerald-500/20 selection:text-emerald-900">

@if(($kind ?? null) !== null)
    <!-- ================= LOGGED IN APP LAYOUT (WITH LEFT SIDEBAR) ================= -->
    <div class="min-h-screen flex">
        <!-- Backdrop for Mobile Sidebar -->
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs transition-opacity hidden lg:hidden"></div>

        <!-- Left Sidebar Navbar -->
        <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col justify-between border-r border-slate-200 bg-white transition-transform duration-300 -translate-x-full lg:translate-x-0">
            <div>
                <!-- Brand Header -->
                <div class="flex h-14 min-h-[56px] items-center gap-2.5 border-b border-slate-200 px-5">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-2xs shadow-emerald-600/20 ring-1 ring-emerald-500/30">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L3.5 5.5V11C3.5 16.5 7.1 21.6 12 23C16.9 21.6 20.5 16.5 20.5 11V5.5L12 2ZM17 12.5H13.5V16H10.5V12.5H7V9.5H10.5V6H13.5V9.5H17V12.5Z"/>
                        </svg>
                    </div>
                    <div class="overflow-hidden">
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-bold tracking-tight text-slate-900">HSE Pantau</span>
                            <span class="rounded bg-emerald-50 px-1 py-0.2 text-[9px] font-bold text-emerald-700 ring-1 ring-emerald-600/20">K3</span>
                        </div>
                        <p class="truncate text-[10px] font-medium text-slate-400">Monitoring & Vitals</p>
                    </div>

                    <!-- Close button on Mobile -->
                    <button type="button" id="close-sidebar-btn" class="ml-auto flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 lg:hidden">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="px-3 py-4 space-y-6">
                    <div>
                        <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Menu Utama
                        </div>
                        <nav class="space-y-1">
                            <!-- Dashboard -->
                            <a href="{{ route('dashboard') }}" 
                               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110 {{ request()->routeIs('dashboard') ? 'text-emerald-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                                    <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            @if(($kind ?? null) === 'hse')
                                <!-- Data Pekerja -->
                                <a href="{{ route('pekerja.index') }}" 
                                   class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition-all {{ request()->routeIs('pekerja.*') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110 {{ request()->routeIs('pekerja.*') ? 'text-emerald-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                        <span>Tenaga Kerja</span>
                                    </div>
                                    @if(isset($navTotalPekerja))
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold transition-colors {{ request()->routeIs('pekerja.*') ? 'bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/30' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                            {{ $navTotalPekerja }}
                                        </span>
                                    @endif
                                </a>

                                <!-- Laporan IBPR -->
                                <a href="{{ route('ibpr.index') }}" 
                                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition-all {{ request()->routeIs('ibpr.*') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110 {{ request()->routeIs('ibpr.*') ? 'text-emerald-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <span>Laporan IBPR</span>
                                </a>
                            @endif
                        </nav>
                    </div>

                    @if(($isAdmin ?? false))
                        <!-- Administrasi Admin Section -->
                        <div>
                            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Administrasi
                            </div>
                            <nav class="space-y-1">
                                <a href="{{ route('inspector.index') }}" 
                                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition-all {{ request()->routeIs('inspector.*') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110 {{ request()->routeIs('inspector.*') ? 'text-emerald-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <polygon points="17 11 19 13 23 9"/>
                                    </svg>
                                    <span>Inspector Site</span>
                                </a>
                            </nav>
                        </div>
                    @endif

                    @if(($kind ?? null) === 'hse')
                        <!-- Pintasan Cepat Input -->
                        <div>
                            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Input Lapangan
                            </div>
                            <div class="space-y-1.5 px-1">
                                <a href="{{ route('tensi.create') }}" class="flex items-center gap-2.5 rounded-xl border border-slate-200/80 bg-slate-50/70 px-3 py-2 text-xs font-semibold text-slate-700 transition-all hover:border-rose-300 hover:bg-rose-50/50 hover:text-rose-900">
                                    <svg class="h-4 w-4 text-rose-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                    <span>Rekam Tensi</span>
                                </a>
                                <a href="{{ route('ibpr.create') }}" class="flex items-center gap-2.5 rounded-xl border border-slate-200/80 bg-slate-50/70 px-3 py-2 text-xs font-semibold text-slate-700 transition-all hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-900">
                                    <svg class="h-4 w-4 text-amber-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                        <line x1="12" y1="9" x2="12" y2="13"/>
                                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                                    </svg>
                                    <span>Input IBPR</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar User Profile & Logout Bottom Section -->
            <div class="border-t border-slate-100 p-3 bg-slate-50/50">
                @if(isset($authName) || isset($authLabel))
                    <div class="mb-3 flex items-center gap-2.5 rounded-xl border border-slate-200/80 bg-white p-2.5 shadow-2xs">
                        <div class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-white">
                            <span class="relative flex h-2 w-2 absolute -top-0.5 -right-0.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            </span>
                            {{ strtoupper(substr($authName ?? $authLabel, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden flex-1">
                            <div class="truncate text-xs font-bold text-slate-900">{{ $authName ?? $authLabel }}</div>
                            <div class="truncate text-[10px] text-slate-400">{{ $authRole ?? (($isAdmin ?? false) ? 'Super Administrator' : 'Petugas Aktif') }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('keluar') }}">
                        @csrf
                        <button class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2 text-xs font-semibold text-slate-600 shadow-2xs transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-700 active:scale-[0.98]">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            <span>Keluar Sistem</span>
                        </button>
                    </form>
                @endif
            </div>
        </aside>

        <!-- Main Content Area (Offset by Sidebar on Desktop) -->
        <div class="flex-1 flex flex-col min-h-screen lg:pl-64">
            <!-- Top Navbar for Breadcrumbs & Mobile Toggle -->
            <header class="h-14 min-h-[56px] border-b border-slate-200 bg-white">
                <div class="flex h-full items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <!-- Mobile Hamburger Button -->
                        <button type="button" id="open-sidebar-btn" class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 lg:hidden">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="3" y1="12" x2="21" y2="12"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <line x1="3" y1="18" x2="21" y2="18"/>
                            </svg>
                        </button>

                        <div>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <span class="font-semibold text-slate-700">HSE Pantau</span>
                                <span>/</span>
                                <span class="font-medium text-slate-500">
                                    @if(request()->routeIs('dashboard')) Dashboard Monitoring
                                    @elseif(request()->routeIs('pekerja.*')) Direktori Tenaga Kerja
                                    @elseif(request()->routeIs('ibpr.*')) Matriks Laporan IBPR
                                    @elseif(request()->routeIs('inspector.*')) Manajemen Inspector
                                    @elseif(request()->routeIs('tensi.*')) Rekam Vitals Tensi
                                    @else Halaman Aktif
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Topbar Controls -->
                    <div class="flex items-center gap-3 text-xs text-slate-500">
                        <div class="hidden sm:flex items-center gap-1.5 font-medium">
                            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>{{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                        </div>
                        @if(isset($authName) || isset($authLabel))
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 font-medium text-[11px] text-slate-700">
                                {{ $authName ?? $authLabel }}
                            </span>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Page Container -->
            <div class="flex-1 p-4 sm:p-6 lg:p-8">
                <!-- Flash Alerts -->
                @if(session('ok'))
                    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm text-emerald-900 shadow-2xs backdrop-blur-xs">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div class="font-medium leading-relaxed">{{ session('ok') }}</div>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-900 shadow-2xs backdrop-blur-xs">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-rose-600 text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-rose-950">Harap periksa kembali input berikut:</div>
                            <ul class="mt-1 list-disc space-y-0.5 pl-5 text-xs text-rose-800">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Content Slot -->
                <main>
                    @yield('content')
                </main>
            </div>

            <!-- Footer -->
            <footer class="border-t border-slate-200/80 px-6 py-4 text-center text-xs text-slate-400 bg-white/50">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>HSE Pantau Sentinel • Sistem Keselamatan & Kesehatan Kerja Konstruksi</div>
                    <div>Dokumentasi Real-time © {{ date('Y') }}</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Script for Mobile Sidebar Toggle -->
    <script>
    (function() {
        const sidebar = document.getElementById('sidebar-menu');
        const backdrop = document.getElementById('mobile-sidebar-backdrop');
        const openBtn = document.getElementById('open-sidebar-btn');
        const closeBtn = document.getElementById('close-sidebar-btn');

        function openSidebar() {
            if (!sidebar || !backdrop) return;
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        }

        function closeSidebar() {
            if (!sidebar || !backdrop) return;
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }

        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);
    })();
    </script>
@else
    <!-- ================= GUEST / PORTAL MASUK LAYOUT (CLEAN & CENTERED) ================= -->
    <div class="min-h-screen flex flex-col justify-between">
        <header class="border-b border-slate-200/90 bg-white shadow-2xs">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-xs">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L3.5 5.5V11C3.5 16.5 7.1 21.6 12 23C16.9 21.6 20.5 16.5 20.5 11V5.5L12 2ZM17 12.5H13.5V16H10.5V12.5H7V9.5H10.5V6H13.5V9.5H17V12.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-base font-bold tracking-tight text-slate-900">HSE Pantau</span>
                            <span class="rounded-md bg-emerald-50 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700 ring-1 ring-emerald-600/20">K3 Sentinel</span>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500">Sistem Keselamatan & Kesehatan Kerja</p>
                    </div>
                </a>
            </div>
        </header>

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 w-full flex-1">
            <!-- Flash Alerts -->
            @if(session('ok'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm text-emerald-900 shadow-2xs backdrop-blur-xs">
                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="font-medium leading-relaxed">{{ session('ok') }}</div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-900 shadow-2xs backdrop-blur-xs">
                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-rose-600 text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold text-rose-950">Harap periksa kembali input berikut:</div>
                        <ul class="mt-1 list-disc space-y-0.5 pl-5 text-xs text-rose-800">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <main>
                @yield('content')
            </main>
        </div>

        <footer class="border-t border-slate-200/80 px-6 py-4 text-center text-xs text-slate-400 bg-white/50">
            <div>HSE Pantau Sentinel • Dokumentasi Real-time © {{ date('Y') }}</div>
        </footer>
    </div>
@endif

</body>
</html>
