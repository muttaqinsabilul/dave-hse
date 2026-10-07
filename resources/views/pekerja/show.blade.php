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
            <a href="{{ route('tensi.create', ['worker_id' => $worker->id]) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 active:scale-[0.98]">
                <svg class="h-3.5 w-3.5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Vitals Tensi</span>
            </a>
        </div>
    </div>

    <!-- Flash Status Notification -->
    @if(session('status'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200/90 bg-emerald-50/90 p-4 text-xs font-semibold text-emerald-900 shadow-2xs">
            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Worker Profile Master Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
        <!-- Top Identity & Status Area -->
        <div class="p-6">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <!-- Avatar & Identity Info -->
                <div class="flex flex-wrap items-center gap-5">
                    <!-- Photo / Initials Frame -->
                    <div class="h-20 w-20 sm:h-22 sm:w-22 shrink-0 overflow-hidden rounded-2xl border border-slate-200/90 bg-slate-100 shadow-2xs">
                        @if(!empty($worker->foto_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($worker->foto_path))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($worker->foto_path) }}" alt="Foto {{ $worker->nama }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-slate-800 text-2xl font-bold text-white tracking-tight shadow-inner">
                                {{ strtoupper(substr($worker->nama, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Worker Name & Basic Info -->
                    <div class="space-y-1.5">
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

                <!-- Presence Status & Fast Action Toggle -->
                <div class="flex flex-col sm:items-end gap-2.5 w-full sm:w-auto">
                    <!-- Status Presence Indicator -->
                    <div>
                        @if($worker->isOnsite())
                            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50/80 px-3 py-1 text-xs font-semibold text-emerald-800">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                                </span>
                                <span>Di Lokasi Proyek (On-site)</span>
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                <span>Luar Lokasi (Off-site)</span>
                            </div>
                        @endif
                    </div>

                    <!-- Toggle Form Button -->
                    <form method="POST" action="{{ route('pekerja.toggle-lokasi', $worker) }}" class="inline-flex items-center w-full sm:w-auto">
                        @csrf
                        @if($worker->isOnsite())
                            <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs transition-all hover:bg-slate-50 hover:text-slate-900 active:scale-[0.98]">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                <span>Tandai Pulang / Off-site</span>
                            </button>
                        @else
                            <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 text-xs font-semibold shadow-2xs transition-all active:scale-[0.98]">
                                <svg class="h-3.5 w-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>Tandai Masuk On-site</span>
                            </button>
                        @endif
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
                <p class="text-xs text-slate-500">Hasil pemantauan berkala 7 catatan terakhir untuk evaluasi kelayakan kerja K3</p>
            </div>
            <span class="rounded-lg border border-slate-200/80 bg-slate-50 px-2.5 py-1 text-[11px] font-medium text-slate-600">
                Ambang Normal K3: Sistol 90–120 / Diastol 60–80 mmHg • Suhu 36.5–37.5°C
            </span>
        </div>

        @php
            $latestCheck = $worker->healthChecks->first();
        @endphp

        <!-- Quick Summary Cards (if checks exist) -->
        @if($latestCheck)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-5 border-b border-slate-100 bg-slate-50/40">
                <div class="rounded-xl border border-slate-200/80 bg-white p-3.5 shadow-2xs">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tekanan Darah Terakhir</div>
                    <div class="mt-1 flex items-baseline gap-2">
                        <span class="text-xl font-bold {{ $latestCheck->status === 'FLAG' ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $latestCheck->sistol }}/{{ $latestCheck->diastol }}
                        </span>
                        <span class="text-xs font-medium text-slate-400">mmHg</span>
                    </div>
                    <div class="mt-1 text-[11px] text-slate-500">
                        Pemeriksaan: {{ $latestCheck->tanggal->format('d/m/Y') }}
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-white p-3.5 shadow-2xs">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Suhu Tubuh Terakhir</div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="text-xl font-bold text-slate-900">{{ $latestCheck->suhu }}</span>
                        <span class="text-xs font-medium text-slate-400">°C</span>
                    </div>
                    <div class="mt-1 text-[11px] {{ (float)$latestCheck->suhu > 37.5 || (float)$latestCheck->suhu < 36.0 ? 'text-amber-600 font-semibold' : 'text-slate-500' }}">
                        {{ (float)$latestCheck->suhu > 37.5 ? 'Di atas ambang normal' : 'Rentang suhu optimal' }}
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-white p-3.5 shadow-2xs">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Kelayakan Kerja</div>
                    <div class="mt-1.5">
                        @if($latestCheck->status === 'NORMAL')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-600/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Layak Kerja Penuh
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-rose-600/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                Perlu Evaluasi Medis (FLAG)
                            </span>
                        @endif
                    </div>
                    <div class="mt-1 text-[11px] text-slate-500">
                        Standar K3 Lapangan
                    </div>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Tanggal Pemeriksaan</th>
                        <th class="py-3 px-4">Tekanan Darah (Tensi)</th>
                        <th class="py-3 px-4">Suhu Tubuh</th>
                        <th class="py-3 px-4">Status Kelayakan</th>
                        <th class="py-3 px-4">Catatan Standar K3</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($worker->healthChecks as $c)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ $c->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-sm font-bold {{ $c->status === 'FLAG' ? 'text-rose-600' : 'text-slate-900' }}">
                                {{ $c->sistol }} / {{ $c->diastol }} <span class="text-[11px] font-normal text-slate-400">mmHg</span>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-slate-800">
                                {{ $c->suhu }} <span class="text-[11px] font-normal text-slate-400">°C</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $c->status === 'FLAG' ? 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $c->status === 'FLAG' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                    {{ $c->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                @if($c->status === 'NORMAL')
                                    <span class="text-emerald-700">Dalam rentang normal, diizinkan bekerja penuh.</span>
                                @else
                                    <span class="font-medium text-rose-700">Tensi / suhu di luar ambang standar K3. Istirahat atau rujukan medis posko.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
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

