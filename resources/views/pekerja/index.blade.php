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

    <!-- Workers Table -->
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
        <!-- Table Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4 sm:p-5">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Tenaga Kerja</h2>
                <p class="text-[11px] text-slate-500">Total {{ $workers->total() }} pekerja</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Status On-site / Off-site Filter Pills (Kanan dekat search) -->
                <div class="inline-flex items-center rounded-xl bg-slate-100/90 p-1 text-xs">
                    <a href="{{ route('pekerja.index', array_filter(['q' => request('q'), 'site' => $site])) }}" 
                       class="rounded-lg px-3 py-1 font-semibold transition-all {{ empty($status) ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                        Semua <span class="ml-1 text-[10px] {{ empty($status) ? 'text-slate-500' : 'text-slate-400' }}">{{ $totalCount }}</span>
                    </a>
                    <a href="{{ route('pekerja.index', array_filter(['status' => 'ONSITE', 'q' => request('q'), 'site' => $site])) }}" 
                       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 font-semibold transition-all {{ $status === 'ONSITE' ? 'bg-white text-emerald-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-emerald-700' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span>On-site</span>
                        <span class="text-[10px] {{ $status === 'ONSITE' ? 'text-emerald-600 font-bold' : 'text-slate-400' }}">{{ $onsiteCount }}</span>
                    </a>
                    <a href="{{ route('pekerja.index', array_filter(['status' => 'OFFSITE', 'q' => request('q'), 'site' => $site])) }}" 
                       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 font-semibold transition-all {{ $status === 'OFFSITE' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                        <span>Off-site</span>
                        <span class="text-[10px] {{ $status === 'OFFSITE' ? 'text-slate-600 font-bold' : 'text-slate-400' }}">{{ $offsiteCount }}</span>
                    </a>
                </div>

                <form method="GET" action="{{ route('pekerja.index') }}" class="flex items-center gap-2">
                    @if($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    @if($isAdmin && $sites->count() > 1)
                        <div class="relative">
                            <select name="site" onchange="this.form.submit()" class="appearance-none rounded-xl border border-slate-200 bg-slate-50/70 py-2 pr-8 pl-3 text-xs font-semibold text-slate-700 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden">
                                <option value="">Semua Site</option>
                                @foreach($sites as $s)
                                    <option value="{{ $s->code }}" @selected($site === $s->code)>Site {{ $s->code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>
                    @elseif($site)
                        <input type="hidden" name="site" value="{{ $site }}">
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
                            value="{{ request('q') }}" 
                            placeholder="Cari nama, mandor, atau ID..." 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2 pr-8 pl-8 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                        >
                        @if(request('q'))
                            <a href="{{ route('pekerja.index', array_filter(['status' => $status, 'site' => $site])) }}" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600" title="Reset pencarian">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 active:scale-[0.98]">
                        <span>Cari</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">ID Pekerja</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Lokasi</th>
                        @if($isAdmin && empty($site))
                            <th class="py-3 px-4">Site</th>
                        @endif
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
                                    @if($w->foto_url)
                                        <img src="{{ $w->foto_url }}" alt="{{ $w->nama }}" class="h-9 w-9 rounded-xl object-cover ring-1 ring-slate-200 shadow-2xs shrink-0">
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-slate-800 to-slate-950 text-xs font-bold text-white shadow-2xs shrink-0">
                                            {{ strtoupper(substr($w->nama, 0, 1)) }}
                                        </div>
                                    @endif
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
                            @if($isAdmin && empty($site))
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700">Site {{ $w->site_code }}</span>
                                </td>
                            @endif
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
                            <td colspan="{{ ($isAdmin && empty($site)) ? 8 : 7 }}" class="py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="8" y1="12" x2="16" y2="12"/>
                                    </svg>
                                    <span class="font-medium">Tidak ada data tenaga kerja ditemukan.</span>
                                    @if(request('q') || $status)
                                        <a href="{{ route('pekerja.index', array_filter(['site' => $site])) }}" class="mt-1 text-xs font-semibold text-slate-900 underline hover:text-slate-700">
                                            Bersihkan filter & pencarian
                                        </a>
                                    @endif
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
