@extends('layouts.app')

@section('title', 'Laporan IBPR — HSE Pantau')

@php
    $levelColor = [
        'LOW' => '#10B981',
        'MEDIUM' => '#F59E0B',
        'HIGH' => '#F97316',
        'EXTREME' => '#EF4444',
    ];
    $levelBadge = [
        'LOW' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
        'MEDIUM' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
        'HIGH' => 'bg-orange-50 text-orange-700 ring-1 ring-orange-600/20',
        'EXTREME' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hazard Identification & Risk Assessment</div>
            <h1 class="mt-0.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Laporan Matriks IBPR</h1>
            <p class="text-xs text-slate-500">Evaluasi bahaya lapangan & pengendalian risiko standar K3 konstruksi</p>
        </div>
        <a href="{{ route('ibpr.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-xs transition-all hover:bg-slate-800 active:scale-[0.98]">
            <svg class="h-4 w-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Input Laporan IBPR Baru</span>
        </a>
    </div>

    <!-- Filter & Search Toolbar with Date Range -->
    <form method="GET" action="{{ route('ibpr.index') }}" class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200/90 bg-white p-3.5 shadow-2xs">
        @if($isAdmin)
            <div class="relative min-w-[160px] flex-1 sm:flex-initial">
                <select name="site" class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2 pr-8 pl-3 text-xs font-semibold text-slate-800 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden">
                    <option value="">Semua Wilayah Site</option>
                    @foreach($sites as $s)
                        <option value="{{ $s->code }}" @selected($site === $s->code)>{{ $s->code }} — {{ $s->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </div>
        @endif

        <div class="relative min-w-[200px] flex-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <input 
                name="q" 
                value="{{ $search }}" 
                placeholder="Cari kegiatan, bahaya, atau risiko..." 
                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2 pr-3 pl-9 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
            >
        </div>

        <div class="relative min-w-[140px] flex-1 sm:flex-initial">
            <select name="level" class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2 pr-8 pl-3 text-xs font-semibold text-slate-800 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden">
                <option value="">Semua Level Risiko</option>
                @foreach(['LOW', 'MEDIUM', 'HIGH', 'EXTREME'] as $lv)
                    <option value="{{ $lv }}" @selected($levelFilter === $lv)>Risiko {{ $lv }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        <!-- Clean Date Range Picker -->
        <x-date-range-picker :from="$from" :to="$to" />

        @if($search || $levelFilter || ($isAdmin && $site) || $from || $to)
            <a href="{{ route('ibpr.index') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 shadow-2xs">
                Reset
            </a>
        @endif
    </form>

    <!-- IBPR Table -->
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Waktu Realtime</th>
                        <th class="py-3 px-4">Site</th>
                        <th class="py-3 px-4">Kegiatan Kerja</th>
                        <th class="py-3 px-4">Potensi Bahaya</th>
                        <th class="py-3 px-4">Dampak Risiko</th>
                        <th class="py-3 px-4 text-center">L</th>
                        <th class="py-3 px-4 text-center">S</th>
                        <th class="py-3 px-4 text-center">Skor</th>
                        <th class="py-3 px-4">Level Risiko</th>
                        <th class="py-3 px-4">Pengendalian & APD</th>
                        <th class="py-3 px-4">Inspector</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $r)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                {{ $r->tanggal_realtime->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[11px] font-bold text-slate-700">Site {{ $r->site_code }}</span>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-900 max-w-xs">{{ $r->kegiatan }}</td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs">{{ $r->bahaya }}</td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs">{{ $r->risiko }}</td>
                            <td class="py-3 px-4 text-center text-slate-700">{{ $r->likelihood }}</td>
                            <td class="py-3 px-4 text-center text-slate-700">{{ $r->severity }}</td>
                            <td class="py-3 px-4 text-center font-extrabold text-slate-900">{{ $r->skor }}</td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $levelBadge[$r->level] ?? 'bg-slate-100 text-slate-700' }}">
                                    <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $levelColor[$r->level] ?? '#64748b' }}"></span>
                                    {{ $r->level }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs text-[11px]">
                                {{ $r->pengendalian ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-[11px] text-slate-500 whitespace-nowrap">
                                {{ $r->inspector?->id ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="8" y1="12" x2="16" y2="12"/>
                                    </svg>
                                    <span class="font-medium">Belum ada catatan IBPR dalam basis data.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
