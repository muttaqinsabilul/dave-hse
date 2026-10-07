@extends('layouts.app')

@section('title', "Profil Pekerja {$worker->nama} ({$worker->id}) — HSE Pantau")

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Actions Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('pekerja.index') }}" class="inline-flex items-center gap-1.5 font-medium text-slate-600 transition-colors hover:text-slate-900">
                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                <span>Direktori Tenaga Kerja</span>
            </a>
            <span class="text-slate-300">/</span>
            <span class="font-mono font-semibold text-slate-900">{{ $worker->id }}</span>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('idcard.show', $worker) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs transition-all hover:bg-slate-50 hover:text-slate-900 active:scale-[0.98]">
                <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
                <span>Buka ID Card Digital</span>
            </a>
        </div>
    </div>


    <!-- Worker Profile Master Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
        <!-- Top Identity & Status Area -->
        <div class="p-6">
            <div class="flex items-center justify-between gap-4">
                <!-- Left: Foto Profil & Info Identitas -->
                <div class="flex items-center gap-4 sm:gap-5 min-w-0">
                    <!-- Photo / Initials Frame -->
                    <div class="h-20 w-20 sm:h-22 sm:w-22 shrink-0 overflow-hidden rounded-2xl border border-slate-200/90 bg-slate-100 shadow-2xs">
                        @if($worker->foto_url)
                            <img src="{{ $worker->foto_url }}" alt="Foto {{ $worker->nama }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-slate-800 text-2xl font-bold text-white tracking-tight shadow-inner">
                                {{ strtoupper(substr($worker->nama, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Worker Name & Basic Info -->
                    <div class="space-y-1.5 min-w-0">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">{{ $worker->nama }}</h1>
                            <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100/90 px-2 py-0.5 font-mono text-xs font-semibold text-slate-700">
                                {{ $worker->id }}
                            </span>
                            <span class="inline-flex items-center rounded-md border border-emerald-200/80 bg-emerald-50/70 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                {{ $worker->jenis_pekerjaan }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-slate-500 font-medium">
                            <span class="text-slate-800 font-semibold">Site {{ $worker->site_code }} ({{ $worker->site->name }})</span>
                            <span class="text-slate-300">•</span>
                            <span>Mandor: <strong class="text-slate-700 font-semibold">{{ $worker->mandor_subkon }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Right: Toggle Sakelar (Justify Between ke Ujung Kanan) -->
                <div class="shrink-0">
                    <form method="POST" action="{{ route('pekerja.toggle-lokasi', $worker) }}" class="inline-flex items-center">
                        @csrf
                        <button type="submit" 
                                class="group inline-flex items-center gap-2.5 rounded-full border border-slate-200 bg-white py-1.5 pl-3.5 pr-1.5 text-xs font-semibold shadow-2xs transition-all hover:border-slate-300 hover:bg-slate-50 active:scale-[0.98] cursor-pointer"
                                title="{{ $worker->isOnsite() ? 'Klik untuk ubah ke Off-site' : 'Klik untuk ubah ke On-site' }}">
                            <span class="{{ $worker->isOnsite() ? 'text-emerald-700 font-bold' : 'text-slate-500 font-medium' }}">
                                {{ $worker->isOnsite() ? 'On-site' : 'Off-site' }}
                            </span>
                            <!-- Native Switch Slider Visual -->
                            <span class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition-colors duration-200 ease-in-out {{ $worker->isOnsite() ? 'bg-emerald-600' : 'bg-slate-300' }} p-0.5">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-xs transition duration-200 ease-in-out {{ $worker->isOnsite() ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Structured Metadata Specs Grid -->
            <div class="mt-6 border-t border-slate-100 pt-5">
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mandor / Subkon</dt>
                        <dd class="mt-1 truncate text-xs font-bold text-slate-800">{{ $worker->mandor_subkon }}</dd>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Usia & Asal Daerah</dt>
                        <dd class="mt-1 truncate text-xs font-bold text-slate-800">{{ $worker->usia }} tahun • {{ $worker->asal }}</dd>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Masa Kerja</dt>
                        <dd class="mt-1 text-xs font-bold text-slate-800">{{ $worker->lama_bekerja_hari }} hari</dd>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Terdaftar</dt>
                        <dd class="mt-1 text-xs font-bold text-slate-800">{{ $worker->tanggal_regis->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Medical Note / Health Status Notice -->
            <div class="mt-4">
                @if($worker->riwayat_penyakit)
                    <div class="flex items-start gap-3 rounded-xl border border-amber-200/90 bg-amber-50/70 p-3.5 text-xs text-amber-950">
                        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-amber-100 text-amber-700 mt-0.5">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-amber-950">Catatan Khusus Riwayat Medis (Komorbid)</div>
                            <p class="mt-0.5 text-amber-900/90 leading-relaxed">
                                Pekerja memiliki riwayat: <span class="font-semibold text-amber-950">{{ $worker->riwayat_penyakit }}</span>. Pastikan pengukuran tensi & evaluasi kelayakan dilakukan sebelum penugasan pekerjaan risiko tinggi.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2.5 rounded-xl border border-slate-200/80 bg-slate-50/60 px-3.5 py-2.5 text-xs text-slate-600">
                        <div class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <span>Status Medis Registrasi: <strong class="text-slate-800 font-semibold">Sehat (Tanpa catatan komorbiditas)</strong></span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Health Checks & Vitals History Section -->
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
            <div>
                <h2 class="text-base font-bold text-slate-900">Riwayat Vitals (Tensi & Suhu Tubuh)</h2>
                <p class="text-xs text-slate-500">Hasil pemantauan berkala 7 catatan terakhir pemeriksaan vitals pekerja</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[480px] text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Tanggal Pemeriksaan</th>
                        <th class="py-3 px-4">Tekanan Darah (Tensi)</th>
                        <th class="py-3 px-4">Suhu Tubuh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($worker->healthChecks as $c)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ $c->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-slate-900">
                                {{ $c->sistol }} / {{ $c->diastol }} <span class="text-[11px] font-normal text-slate-400">mmHg</span>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-slate-800">
                                {{ $c->suhu }} <span class="text-[11px] font-normal text-slate-400">°C</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                                    </svg>
                                    <span class="font-medium">Belum ada riwayat pemeriksaan tensi harian tercatat untuk pekerja ini.</span>
                                    <a href="{{ route('tensi.create', ['worker_id' => $worker->id]) }}" class="mt-1 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        <span>Catat Pemeriksaan Pertama</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

