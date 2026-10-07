@extends('layouts.app')

@section('title', 'Manajemen Inspector — HSE Pantau')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Administrasi Otoritas HSE</div>
            <h1 class="mt-0.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Akun Inspector Lapangan</h1>
            <p class="text-xs text-slate-500">Daftar petugas pengawas K3 yang terakreditasi per wilayah proyek</p>
        </div>
        <a href="{{ route('inspector.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-xs transition-all hover:bg-slate-800 active:scale-[0.98]">
            <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Buat Akun Inspector</span>
        </a>
    </div>

    <!-- Inspectors Table -->
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">ID Akun</th>
                        <th class="py-3 px-4">Nama Petugas Inspector</th>
                        <th class="py-3 px-4">Wilayah Operasional</th>
                        <th class="py-3 px-4">Hak Akses</th>
                        <th class="py-3 px-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($inspectors as $i)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-800">{{ $i->id }}</span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-xs font-bold text-white shadow-2xs">
                                        {{ strtoupper(substr($i->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $i->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700">Site {{ $i->site_code }}</span>
                                    <span class="text-slate-600 font-medium">({{ $i->site?->name ?? '—' }})</span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800 ring-1 ring-emerald-600/20">
                                    Pengawas Lapangan (K3)
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                Belum ada akun inspector terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">
            {{ $inspectors->links() }}
        </div>
    </div>
</div>
@endsection
