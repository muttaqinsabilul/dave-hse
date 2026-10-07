@extends('layouts.app')

@section('title', 'Manajemen Subkontraktor & Mandor — HSE Pantau')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Administrasi Otoritas HSE</div>
            <h1 class="mt-0.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Subkontraktor & Mandor</h1>
            <p class="text-xs text-slate-500">Kelola master data rekanan kontraktor dan mandor lapangan per wilayah proyek</p>
        </div>
        <a href="{{ route('subkon.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-xs transition-all hover:bg-slate-800 active:scale-[0.98]">
            <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Tambah Subkon / Mandor</span>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
        <form method="GET" action="{{ route('subkon.index') }}" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-1 flex-wrap items-center gap-3 min-w-[280px]">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[200px]">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $search }}" 
                        placeholder="Cari nama subkon, mandor, atau bidang..."
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2 pr-3 pl-9 text-xs font-medium text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                    >
                </div>

                <!-- Site Dropdown Filter -->
                <div class="relative">
                    <select name="site" onchange="this.form.submit()" class="appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2 pr-8 pl-3 text-xs font-semibold text-slate-700 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden">
                        <option value="">Semua Site Proyek</option>
                        @foreach($sites as $s)
                            <option value="{{ $s->code }}" @selected($site === $s->code)>Site {{ $s->code }} — {{ $s->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            </div>

            @if($search || $site)
                <a href="{{ route('subkon.index') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px] text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Nama Rekanan / Mandor</th>
                        <th class="py-3 px-4">Wilayah Site</th>
                        <th class="py-3 px-4">Bidang Pekerjaan</th>
                        <th class="py-3 px-4">Kontak / PIC</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subcontractors as $sub)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-xs font-bold text-white shadow-2xs shrink-0">
                                        {{ strtoupper(substr($sub->nama, 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-slate-900">{{ $sub->nama }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700">Site {{ $sub->site_code }}</span>
                                <span class="text-slate-500 font-medium ml-1">({{ $sub->site?->name ?? '—' }})</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $sub->bidang ?: '—' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                                {{ $sub->kontak ?: '—' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('subkon.destroy', $sub) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data {{ $sub->nama }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-rose-600 shadow-2xs hover:bg-rose-50 hover:border-rose-200 active:scale-[0.98]">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="font-medium">Belum ada data subkontraktor / mandor pada kriteria ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subcontractors->hasPages())
            <div class="border-t border-slate-100 p-4 bg-slate-50/50">
                {{ $subcontractors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
