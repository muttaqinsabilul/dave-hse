@extends('layouts.app')

@section('title', 'Input Tensi & Suhu Harian — HSE Pantau')

@section('content')
<div class="mx-auto max-w-xl py-2">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Pemeriksaan Tensi & Vitals</h1>
        <p class="mt-1 text-xs text-slate-500">
            Pencatatan tekanan darah dan suhu tubuh harian sebelum memasuki area kerja. Data disimpan untuk tanggal hari ini: <b class="text-slate-800">{{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</b>.
        </p>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs sm:p-8">
        <form method="POST" action="{{ route('tensi.store') }}" class="space-y-5">
            @csrf

            <!-- Worker Selection -->
            <div>
                <label for="worker_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Pilih Pekerja <span class="text-rose-500">*</span>
                </label>
                <div class="relative mt-1">
                    <select name="worker_id" id="worker_id" class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-8 pl-3 text-xs font-semibold text-slate-800 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden">
                        @foreach($workers as $w)
                            <option value="{{ $w->id }}" @selected($selected === $w->id)>
                                {{ $w->id }} — {{ $w->nama }} (Site {{ $w->site_code }} • {{ $w->jenis_pekerjaan }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            </div>

            <!-- Blood Pressure (Sistol / Diastol) -->
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Tekanan Darah (mmHg) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400 font-medium">Rentang normal: 90–120 / 60–80</span>
                </div>

                <div class="mt-1.5 grid grid-cols-2 gap-3">
                    <div>
                        <div class="relative">
                            <input 
                                type="number" 
                                id="sistol_input"
                                name="sistol" 
                                value="{{ old('sistol', 120) }}" 
                                min="70" 
                                max="220" 
                                placeholder="120" 
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-14 pl-3 text-sm font-bold text-slate-900 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                            >
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400">
                                Sistol
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="relative">
                            <input 
                                type="number" 
                                id="diastol_input"
                                name="diastol" 
                                value="{{ old('diastol', 80) }}" 
                                min="40" 
                                max="130" 
                                placeholder="80" 
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-14 pl-3 text-sm font-bold text-slate-900 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                            >
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400">
                                Diastol
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Temperature -->
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Suhu Tubuh (°C) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400 font-medium">Rentang normal: 36.5°C – 37.5°C</span>
                </div>
                <div class="relative mt-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/>
                        </svg>
                    </div>
                    <input 
                        type="number" 
                        step="0.1" 
                        id="suhu_input"
                        name="suhu" 
                        value="{{ old('suhu', 36.8) }}" 
                        min="34" 
                        max="42" 
                        placeholder="36.8" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-10 pl-9 text-sm font-bold text-slate-900 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                    >
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400">
                        °C
                    </span>
                </div>
            </div>

            <!-- Real-time Status Preview Indicator -->
            <div id="status_preview_card" class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-3.5 transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span id="status_indicator_dot" class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-slate-700">Evaluasi Otomatis Sistem:</span>
                    </div>
                    <span id="status_badge_text" class="rounded-md bg-emerald-600 px-2 py-0.5 text-xs font-extrabold text-white">
                        NORMAL
                    </span>
                </div>
                <p id="status_description" class="mt-1 text-[11px] text-emerald-800">
                    Kondisi fisiologis dalam batas toleransi standar K3. Tenaga kerja diizinkan menjalankan aktivitas lapangan.
                </p>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-slate-800 active:scale-[0.98]">
                    <span>Simpan Rekam Vitals Hari Ini</span>
                    <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const sistolIn = document.getElementById('sistol_input');
const diastolIn = document.getElementById('diastol_input');
const suhuIn = document.getElementById('suhu_input');
const prevCard = document.getElementById('status_preview_card');
const dot = document.getElementById('status_indicator_dot');
const badge = document.getElementById('status_badge_text');
const desc = document.getElementById('status_description');

function updateStatus() {
    const s = parseFloat(sistolIn.value) || 0;
    const d = parseFloat(diastolIn.value) || 0;
    const t = parseFloat(suhuIn.value) || 0;

    const normal = (s >= 90 && s <= 120) && (d >= 60 && d <= 80) && (t >= 36.5 && t <= 37.5);

    if (normal) {
        prevCard.className = 'rounded-xl border border-emerald-200 bg-emerald-50/70 p-3.5 transition-all';
        dot.className = 'h-2.5 w-2.5 rounded-full bg-emerald-500';
        badge.className = 'rounded-md bg-emerald-600 px-2 py-0.5 text-xs font-extrabold text-white';
        badge.textContent = 'NORMAL';
        desc.className = 'mt-1 text-[11px] text-emerald-800';
        desc.textContent = 'Kondisi fisiologis dalam batas toleransi standar K3. Tenaga kerja diizinkan menjalankan aktivitas lapangan.';
    } else {
        prevCard.className = 'rounded-xl border border-rose-200 bg-rose-50/70 p-3.5 transition-all';
        dot.className = 'h-2.5 w-2.5 rounded-full bg-rose-500 animate-pulse';
        badge.className = 'rounded-md bg-rose-600 px-2 py-0.5 text-xs font-extrabold text-white';
        badge.textContent = 'FLAG (PERINGATAN)';
        desc.className = 'mt-1 text-[11px] text-rose-800';
        desc.textContent = 'Tensi atau suhu tubuh berada di luar ambang standar normal. Pekerja wajib diistirahatkan atau dirujuk evaluasi medis.';
    }
}

sistolIn.addEventListener('input', updateStatus);
diastolIn.addEventListener('input', updateStatus);
suhuIn.addEventListener('input', updateStatus);
updateStatus();
</script>
@endsection
