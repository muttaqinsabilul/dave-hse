@extends('layouts.app')

@section('title', 'Input Laporan IBPR — HSE Pantau')

@section('content')
<div class="mx-auto max-w-xl py-2">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Input Matriks IBPR</h1>
        <p class="mt-1 text-xs text-slate-500">
            Identifikasi bahaya dan penilaian risiko realtime. Waktu dicatat otomatis saat formulir dikirimkan.
        </p>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs sm:p-8">
        <form method="POST" action="{{ route('ibpr.store') }}" class="space-y-5">
            @csrf

            <!-- Site Selection -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Lokasi Penilaian <span class="text-rose-500">*</span>
                </label>
                @if($isAdmin)
                    <div class="relative mt-1">
                        <select name="site_code" class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-8 pl-3 text-xs font-semibold text-slate-800 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden">
                            @foreach($sites as $s)
                                <option value="{{ $s->code }}" @selected(old('site_code') === $s->code)>{{ $s->code }} — {{ $s->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="site_code" value="{{ $lockedSite }}">
                    <div class="mt-1 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-xs font-semibold text-slate-700">
                        <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Site {{ $lockedSite }} (Terkunci pada wilayah wewenang Anda)</span>
                    </div>
                @endif
            </div>

            <!-- Kegiatan -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Kegiatan / Aktivitas Pekerjaan <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    name="kegiatan" 
                    rows="2" 
                    placeholder="cth: Pemasangan bekisting kolom di ketinggian 4 meter"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                >{{ old('kegiatan') }}</textarea>
            </div>

            <!-- Bahaya -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Potensi Bahaya Teridentifikasi <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    name="bahaya" 
                    rows="2" 
                    placeholder="cth: Perancah belum terkunci sempurna, angin kencang"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                >{{ old('bahaya') }}</textarea>
            </div>

            <!-- Risiko -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Dampak Risiko Yang Mungkin Terjadi <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    name="risiko" 
                    rows="2" 
                    placeholder="cth: Tenaga kerja terjatuh dari ketinggian, fraktur tulang"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                >{{ old('risiko') }}</textarea>
            </div>

            <!-- Likelihood & Severity -->
            <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-700">
                    Penilaian Matriks Risiko 5×5
                </div>

                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600">
                            Peluang / Likelihood (1–5) <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="l_input"
                            name="likelihood" 
                            value="{{ old('likelihood', 2) }}" 
                            min="1" 
                            max="5" 
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm font-bold text-slate-900 transition-all focus:border-slate-900 focus:outline-hidden"
                        >
                        <span class="text-[10px] text-slate-400">1: Sangat Jarang s/d 5: Sangat Sering</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600">
                            Keparahan / Severity (1–5) <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="s_input"
                            name="severity" 
                            value="{{ old('severity', 3) }}" 
                            min="1" 
                            max="5" 
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm font-bold text-slate-900 transition-all focus:border-slate-900 focus:outline-hidden"
                        >
                        <span class="text-[10px] text-slate-400">1: Ringan s/d 5: Fatal / Kematian</span>
                    </div>
                </div>

                <!-- Live Risk Calculator Box -->
                <div id="risk_calc_box" class="mt-4 flex items-center justify-between rounded-xl border border-slate-200 bg-white p-3 shadow-2xs">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-500">Skor Terhitung:</div>
                        <div class="text-xl font-black text-slate-900">
                            <span id="score_value">6</span> 
                            <span class="text-xs font-normal text-slate-400">(<span id="l_val">2</span> × <span id="s_val">3</span>)</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[11px] font-semibold text-slate-500">Tingkat Risiko:</div>
                        <span id="level_badge" class="mt-0.5 inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-extrabold text-amber-700 ring-1 ring-amber-600/20">
                            MEDIUM
                        </span>
                    </div>
                </div>
            </div>

            <!-- Pengendalian -->
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Langkah Pengendalian & Mitigasi
                    </label>
                    <span id="req_warn" class="text-[11px] font-semibold text-slate-500">
                        Wajib bila level HIGH / EXTREME
                    </span>
                </div>
                <textarea 
                    name="pengendalian" 
                    rows="2" 
                    placeholder="cth: Pemasangan full body harness, safety net, dan inspeksi scaffold oleh ahli K3"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                >{{ old('pengendalian') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-slate-800 active:scale-[0.98]">
                    <span>Simpan & Catat Laporan IBPR</span>
                    <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const matrix = {
    1: ['LOW', 'LOW', 'LOW', 'MEDIUM', 'MEDIUM'],
    2: ['LOW', 'MEDIUM', 'MEDIUM', 'HIGH', 'HIGH'],
    3: ['LOW', 'MEDIUM', 'HIGH', 'HIGH', 'EXTREME'],
    4: ['MEDIUM', 'HIGH', 'HIGH', 'HIGH', 'EXTREME'],
    5: ['MEDIUM', 'HIGH', 'EXTREME', 'EXTREME', 'EXTREME']
};

const badgeClasses = {
    'LOW': 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
    'MEDIUM': 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
    'HIGH': 'bg-orange-50 text-orange-700 ring-1 ring-orange-600/20',
    'EXTREME': 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20'
};

const lInput = document.getElementById('l_input');
const sInput = document.getElementById('s_input');
const scoreVal = document.getElementById('score_value');
const lVal = document.getElementById('l_val');
const sVal = document.getElementById('s_val');
const levelBadge = document.getElementById('level_badge');
const reqWarn = document.getElementById('req_warn');

function recalcRisk() {
    let l = parseInt(lInput.value) || 1;
    let s = parseInt(sInput.value) || 1;
    l = Math.max(1, Math.min(5, l));
    s = Math.max(1, Math.min(5, s));

    const score = l * s;
    const level = (matrix[l] && matrix[l][s - 1]) || 'MEDIUM';

    lVal.textContent = l;
    sVal.textContent = s;
    scoreVal.textContent = score;
    levelBadge.textContent = level;
    levelBadge.className = 'mt-0.5 inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-extrabold ' + (badgeClasses[level] || '');

    if (level === 'HIGH' || level === 'EXTREME') {
        reqWarn.className = 'text-[11px] font-bold text-rose-600';
        reqWarn.textContent = '★ Wajib diisi (Level ' + level + ')';
    } else {
        reqWarn.className = 'text-[11px] font-semibold text-slate-500';
        reqWarn.textContent = 'Wajib bila level HIGH / EXTREME';
    }
}

lInput.addEventListener('input', recalcRisk);
sInput.addEventListener('input', recalcRisk);
recalcRisk();
</script>
@endsection
