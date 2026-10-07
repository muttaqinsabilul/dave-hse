@extends('layouts.app')

@section('title', 'Direktori Pekerja — HSE Pantau')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ketenagakerjaan Proyek</div>
            <h1 class="mt-0.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Direktori Tenaga Kerja</h1>
            <p class="text-xs text-slate-500">Kelola identitas dan riwayat penempatan seluruh pekerja lapangan</p>
        </div>
        <a href="{{ route('pekerja.daftar') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-xs transition-all hover:bg-slate-800 active:scale-[0.98]">
            <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Registrasi Pekerja Baru</span>
        </a>
    </div>

    <!-- Search Bar -->
    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200/90 bg-white p-3.5 shadow-2xs">
        <div class="relative min-w-[240px] flex-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <input 
                name="q" 
                value="{{ request('q') }}" 
                placeholder="Cari nama pekerja, mandor, atau ID (cth: 01-001)..." 
                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2 pr-3 pl-9 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
            >
        </div>
        <button class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 active:scale-[0.98]">
            <span>Cari</span>
        </button>
        @if(request('q'))
            <a href="{{ route('pekerja.index') }}" class="rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">
                Reset
            </a>
        @endif
    </form>

    <!-- Workers Table -->
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">ID Pekerja</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Lokasi</th>
                        <th class="py-3 px-4">Site</th>
                        <th class="py-3 px-4">Jenis Pekerjaan</th>
                        <th class="py-3 px-4">Mandor / Subkon</th>
                        <th class="py-3 px-4">Usia</th>
                        <th class="py-3 px-4">Masa Bekerja</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($workers as $w)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-800">{{ $w->id }}</span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-slate-800 to-slate-950 text-xs font-bold text-white shadow-2xs">
                                        {{ strtoupper(substr($w->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $w->nama }}</div>
                                        <div class="text-[11px] text-slate-400">Asal: {{ $w->asal }}</div>
                                    </div>
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
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700">Site {{ $w->site_code }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800 ring-1 ring-emerald-600/20">
                                    {{ $w->jenis_pekerjaan }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">{{ $w->mandor_subkon }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $w->usia }} tahun</td>
                            <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>{{ $w->lama_bekerja_hari }} hari</span>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('pekerja.show', $w) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-2xs hover:border-slate-300 hover:bg-slate-50 active:scale-[0.98]">
                                    <span>Detail</span>
                                    <svg class="h-3 w-3 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="8" y1="12" x2="16" y2="12"/>
                                    </svg>
                                    <span class="font-medium">Tidak ada data tenaga kerja ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">
            {{ $workers->links() }}
        </div>
    </div>
</div>
@endsection
