@extends('layouts.app')

@section('title', 'Dashboard Monitoring K3 — HSE Pantau')

@php
    $levelColor = [
        'LOW' => '#10B981',      // Emerald 500
        'MEDIUM' => '#F59E0B',   // Amber 500
        'HIGH' => '#F97316',     // Orange 500
        'EXTREME' => '#EF4444',  // Red 500
    ];
    $levelBadge = [
        'LOW' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
        'MEDIUM' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
        'HIGH' => 'bg-orange-50 text-orange-700 ring-1 ring-orange-600/20',
        'EXTREME' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
    ];
    $donutTotal = max(1, array_sum($donut->toArray()));
    $stops = [];
    $acc = 0;
    foreach (['LOW', 'MEDIUM', 'HIGH', 'EXTREME'] as $lv) {
        $pct = ($donut[$lv] ?? 0) / $donutTotal * 100;
        if ($pct > 0) {
            $stops[] = "{$levelColor[$lv]} {$acc}% " . ($acc + $pct) . '%';
            $acc += $pct;
        }
    }
@endphp

@section('content')
<div class="space-y-6">
    <!-- Page Header & Quick Actions -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                <span>•</span>
                <span class="rounded-md bg-slate-100 px-2 py-0.5 font-semibold text-slate-700">
                    {{ $site ? "Site {$site}" : 'Semua Wilayah Site' }}
                </span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Ringkasan Pemantauan K3
            </h1>
        </div>

        <!-- Filter Tanggal & Site Sejajar Judul -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2">
            @if($search)
                <input type="hidden" name="q" value="{{ $search }}">
            @endif
            @if($user?->isAdmin())
                <div class="relative">
                    <select name="site" onchange="this.form.submit()" class="appearance-none rounded-xl border border-slate-300 bg-white py-2 pr-7 pl-3 text-xs font-semibold text-slate-800 shadow-2xs transition-all focus:border-slate-900 focus:outline-hidden">
                        <option value="">Semua Site</option>
                        @foreach($sites as $s)
                            <option value="{{ $s->code }}" @selected($site === $s->code)>Site {{ $s->code }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400">
                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            @endif

            <x-date-range-picker :from="$from" :to="$to" />

            @if($from || $to || ($user?->isAdmin() && $site))
                <a href="{{ route('dashboard', $search ? ['q' => $search] : []) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 shadow-2xs" title="Reset filter tanggal & site">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Worker Personal Pass Banner (if logged in as worker) -->
    @if($ownWorker)
        @php($todayCheck = $ownWorker->healthChecks->first())
        <div class="relative overflow-hidden rounded-2xl border border-emerald-200/90 bg-gradient-to-r from-emerald-950 via-slate-900 to-slate-950 p-5 text-white shadow-md">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-emerald-500/10 blur-2xl"></div>
            <div class="relative flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-2xl font-black text-white shadow-inner ring-2 ring-emerald-400/30">
                        {{ strtoupper(substr($ownWorker->nama, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold">{{ $ownWorker->nama }}</span>
                            <span class="rounded-md bg-emerald-500/20 px-2 py-0.5 text-xs font-semibold text-emerald-300 ring-1 ring-emerald-400/30">
                                {{ $ownWorker->id }}
                            </span>
                        </div>
                        <div class="mt-0.5 text-xs text-slate-300">
                            {{ $ownWorker->jenis_pekerjaan }} • Mandor: {{ $ownWorker->mandor_subkon }} • Site {{ $ownWorker->site_code }} • Masa Kerja {{ $ownWorker->lama_bekerja_hari }} Hari
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            @if($todayCheck)
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $todayCheck->status === 'NORMAL' ? 'bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/30' : 'bg-rose-500/20 text-rose-300 ring-1 ring-rose-400/30' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $todayCheck->status === 'NORMAL' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                    Tensi Hari Ini: {{ $todayCheck->sistol }}/{{ $todayCheck->diastol }} mmHg ({{ $todayCheck->suhu }}°C) — {{ $todayCheck->status }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/20 px-2.5 py-0.5 text-xs font-semibold text-amber-300 ring-1 ring-amber-400/30">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                    Belum Cek Tensi Hari Ini — Harap Kunjungi Pos HSE
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('idcard.show', $ownWorker) }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-semibold text-slate-900 shadow-xs transition-all hover:bg-slate-100 active:scale-[0.98]">
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                    <span>ID Card Digital Saya</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Top KPI Stat Cards -->
    <div class="grid gap-4 sm:grid-cols-2">
        <!-- Card 1: Pekerja On-site -->
        <div class="relative overflow-hidden rounded-2xl border border-emerald-200/90 bg-white p-5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Pekerja On-site</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-600/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    ON-SITE
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-700">{{ $stats['pekerja_onsite'] }}</span>
                <span class="text-xs text-slate-500">orang di proyek</span>
            </div>
        </div>

        <!-- Card 2: Pekerja Off-site -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Pekerja Off-site</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                    OFF-SITE
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-800">{{ $stats['pekerja_offsite'] }}</span>
                <span class="text-xs text-slate-500">orang off duty</span>
            </div>
        </div>
    </div>

    <!-- Sebaran Risiko IBPR Card -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-2xs">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-slate-900 sm:text-base">Sebaran Risiko IBPR</h2>
                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                        @if($hasDateFilter)
                            @if($from === $to)
                                {{ \Illuminate\Support\Carbon::parse($from)->translatedFormat('d M Y') }}
                            @else
                                {{ ($from ? \Illuminate\Support\Carbon::parse($from)->translatedFormat('d M Y') : 'Awal') }} s/d {{ ($to ? \Illuminate\Support\Carbon::parse($to)->translatedFormat('d M Y') : 'Hari ini') }}
                            @endif
                        @else
                            Hari Ini ({{ now()->translatedFormat('d M Y') }})
                        @endif
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-slate-500">Proporsi klasifikasi bahaya & keselamatan teridentifikasi di lapangan</p>
            </div>
            <div class="text-xs text-slate-500">
                Total Evaluasi: <span class="font-bold text-slate-900">{{ array_sum($donut->toArray()) }}</span> Laporan
            </div>
        </div>

        <div class="mt-5 flex flex-wrap items-center justify-around gap-6">
            <!-- Donut Graphic -->
            <div class="relative flex h-32 w-32 items-center justify-center rounded-full shadow-inner ring-2 ring-slate-100 shrink-0" 
                 style="background: conic-gradient({{ implode(',', $stops ?: ['#e2e8f0 0% 100%']) }});">
                <div class="flex h-20 w-20 flex-col items-center justify-center rounded-full bg-white shadow-xs">
                    <span class="text-xl font-black text-slate-900">{{ array_sum($donut->toArray()) }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</span>
                </div>
            </div>

            <!-- Risk Level Stat Pills -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1 max-w-2xl">
                @foreach(['LOW', 'MEDIUM', 'HIGH', 'EXTREME'] as $lv)
                    <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-slate-50/70 p-3 shadow-2xs">
                        <span class="h-3 w-3 rounded-full shrink-0" style="background: {{ $levelColor[$lv] }}"></span>
                        <div>
                            <div class="text-[10px] font-bold uppercase text-slate-500">{{ $lv }}</div>
                            <div class="text-base font-black text-slate-900">{{ $donut[$lv] ?? 0 }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="space-y-6">
        <!-- Table 1: Pekerja + Vitals Hari Ini -->
        <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Tenaga Kerja & Vitals Hari Ini</h2>
                    <p class="text-xs text-slate-500">Rekap status kesehatan dan keberadaan mandor & pekerja di lapangan</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search Input Terintegrasi di Tabel -->
                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                        @if($site)
                            <input type="hidden" name="site" value="{{ $site }}">
                        @endif
                        @if($from)
                            <input type="hidden" name="from" value="{{ $from }}">
                        @endif
                        @if($to)
                            <input type="hidden" name="to" value="{{ $to }}">
                        @endif
                        <div class="relative w-56 sm:w-64">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                            </div>
                            <input 
                                name="q" 
                                value="{{ $search }}" 
                                placeholder="Cari nama atau ID pekerja..." 
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/70 py-1.5 pr-8 pl-8 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                            >
                            @if($search)
                                <a href="{{ route('dashboard', array_filter(['site' => $site, 'from' => $from, 'to' => $to])) }}" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600" title="Hapus pencarian">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </a>
                            @endif
                        </div>
                        <button type="submit" class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 active:scale-[0.98]">
                            <span>Cari</span>
                        </button>
                    </form>
                    <div class="text-xs text-slate-500">
                        Total: <span class="font-bold text-slate-800">{{ $totalWorkers ?? $workers->count() }}</span> data
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Nama Pekerja</th>
                            <th class="py-3 px-4">Lokasi</th>
                            <th class="py-3 px-4">Jenis Pekerjaan</th>
                            <th class="py-3 px-4">Mandor</th>
                            <th class="py-3 px-4">Usia</th>
                            <th class="py-3 px-4">Status Vitals</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($workers as $w)
                            @php($cek = $w->healthChecks->first())
                            <tr class="transition-colors hover:bg-slate-50/60">
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-slate-800">{{ $w->id }}</span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-700">
                                            {{ strtoupper(substr($w->nama, 0, 1)) }}
                                        </div>
                                        <span>{{ $w->nama }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($w->isOnsite())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            On-site
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Off-site
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $w->jenis_pekerjaan }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $w->mandor_subkon }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $w->usia }} thn</td>
                                <td class="py-3 px-4">
                                    @if($cek)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $cek->status === 'FLAG' ? 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20' }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $cek->status === 'FLAG' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                            {{ $cek->status }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">
                                            Belum Cek
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('pekerja.show', $w) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-2xs hover:border-slate-300 hover:bg-slate-50 active:scale-[0.98]">
                                        <span>Detail</span>
                                        <svg class="h-3 w-3 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="8" y1="12" x2="16" y2="12"/>
                                        </svg>
                                        <span>Tidak ada data pekerja pada kriteria pencarian ini.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Tenaga Kerja Sesuai Style Pill Filter -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-3.5 bg-slate-50/50 rounded-b-2xl">
                <div class="text-xs font-medium text-slate-500">
                    Menampilkan <span class="font-bold text-slate-800">{{ $workers->firstItem() ?? 0 }}-{{ $workers->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ $totalWorkers }}</span> data
                </div>
                <div class="flex items-center gap-2">
                    @if($workers->onFirstPage())
                        <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-400 cursor-not-allowed opacity-50 select-none">
                            <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                            <span>Sebelumnya</span>
                        </span>
                    @else
                        <a href="{{ $workers->previousPageUrl() }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-400 active:scale-[0.98] transition cursor-pointer">
                            <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                            <span>Sebelumnya</span>
                        </a>
                    @endif

                    @if($workers->hasMorePages())
                        <a href="{{ $workers->nextPageUrl() }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-400 active:scale-[0.98] transition cursor-pointer">
                            <span>Berikutnya</span>
                            <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-400 cursor-not-allowed opacity-50 select-none">
                            <span>Berikutnya</span>
                            <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table 2: Laporan IBPR Terbaru -->
        <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Laporan IBPR Terkini</h2>
                    <p class="text-xs text-slate-500">Identifikasi Bahaya dan Penilaian Risiko realtime berdasarkan matriks 5×5</p>
                </div>
                <div class="text-xs text-slate-500">
                    Total: <span class="font-bold text-slate-800">{{ $totalIbpr }}</span> laporan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Site</th>
                            <th class="py-3 px-4">Kegiatan</th>
                            <th class="py-3 px-4">Risiko Teridentifikasi</th>
                            <th class="py-3 px-4 text-center">L</th>
                            <th class="py-3 px-4 text-center">S</th>
                            <th class="py-3 px-4 text-center">Skor</th>
                            <th class="py-3 px-4">Level</th>
                            <th class="py-3 px-4">Pengendalian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ibprList as $r)
                            <tr class="transition-colors hover:bg-slate-50/60">
                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $r->tanggal_realtime->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[11px] font-bold text-slate-700">{{ $r->site_code }}</span>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-900 max-w-xs">{{ $r->kegiatan }}</td>
                                <td class="py-3 px-4 text-slate-600 max-w-xs">{{ $r->risiko }}</td>
                                <td class="py-3 px-4 text-center text-slate-700">{{ $r->likelihood }}</td>
                                <td class="py-3 px-4 text-center text-slate-700">{{ $r->severity }}</td>
                                <td class="py-3 px-4 text-center font-bold text-slate-900">{{ $r->skor }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $levelBadge[$r->level] ?? 'bg-slate-100 text-slate-700' }}">
                                        <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $levelColor[$r->level] ?? '#64748b' }}"></span>
                                        {{ $r->level }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600 max-w-xs text-[11px]">
                                    {{ $r->pengendalian ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="8" y1="12" x2="16" y2="12"/>
                                        </svg>
                                        <span>Belum ada data IBPR tercatat di filter ini.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Laporan IBPR Sesuai Style Pill Filter -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-3.5 bg-slate-50/50 rounded-b-2xl">
                <div class="text-xs font-medium text-slate-500">
                    Menampilkan <span class="font-bold text-slate-800">{{ $ibprList->firstItem() ?? 0 }}-{{ $ibprList->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ $totalIbpr }}</span> laporan
                </div>
                <div class="flex items-center gap-2">
                    @if($ibprList->onFirstPage())
                        <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-400 cursor-not-allowed opacity-50 select-none">
                            <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                            <span>Sebelumnya</span>
                        </span>
                    @else
                        <a href="{{ $ibprList->previousPageUrl() }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-400 active:scale-[0.98] transition cursor-pointer">
                            <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                            <span>Sebelumnya</span>
                        </a>
                    @endif

                    @if($ibprList->hasMorePages())
                        <a href="{{ $ibprList->nextPageUrl() }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-400 active:scale-[0.98] transition cursor-pointer">
                            <span>Berikutnya</span>
                            <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-400 cursor-not-allowed opacity-50 select-none">
                            <span>Berikutnya</span>
                            <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
