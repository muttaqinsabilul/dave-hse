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

            @php
                $selectedId = old('worker_id', $selected);
                $selectedWorker = $selectedId ? $workers->firstWhere('id', $selectedId) : null;
            @endphp

            <!-- Worker Selection with Real-time Search -->
            <div class="relative" id="worker_combobox_container">
                <div class="flex items-center justify-between">
                    <label for="worker_search_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Pilih Pekerja <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400 font-medium">Ketik untuk mencari pekerja</span>
                </div>

                <!-- Hidden select for form submission -->
                <select name="worker_id" id="worker_id" class="hidden" required>
                    <option value="" disabled @selected(!$selectedWorker)>Pilih Pekerja...</option>
                    @foreach($workers as $w)
                        <option value="{{ $w->id }}" @selected(($selectedWorker?->id ?? '') === $w->id)>
                            {{ $w->id }} — {{ $w->nama }} (Site {{ $w->site_code }})
                        </option>
                    @endforeach
                </select>

                <!-- Search Input Trigger -->
                <div class="relative mt-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        id="worker_search_input" 
                        autocomplete="off"
                        placeholder="Pilih atau cari nama / ID pekerja..." 
                        value="{{ $selectedWorker ? $selectedWorker->id . ' — ' . $selectedWorker->nama . ' (Site ' . $selectedWorker->site_code . ')' : '' }}"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-10 pl-9 text-xs font-normal text-slate-800 transition-all placeholder:text-slate-400 placeholder:font-normal focus:border-slate-900 focus:bg-white focus:outline-hidden"
                    >
                    <button 
                        type="button" 
                        id="worker_combobox_toggle"
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-hidden"
                    >
                        <svg id="worker_combobox_arrow" class="h-4 w-4 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                </div>

                <!-- Dropdown Menu List -->
                <div 
                    id="worker_dropdown_menu" 
                    class="hidden absolute z-30 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 shadow-lg divide-y divide-slate-100 text-xs"
                >
                    @foreach($workers as $w)
                        <div 
                            class="worker-option flex cursor-pointer items-center justify-between px-3.5 py-2.5 transition-colors hover:bg-slate-50 active:bg-slate-100"
                            data-id="{{ $w->id }}"
                            data-nama="{{ strtolower($w->nama) }}"
                            data-code="{{ strtolower($w->id) }}"
                            data-site="{{ $w->site_code }}"
                            data-display="{{ $w->id }} — {{ $w->nama }} (Site {{ $w->site_code }})"
                        >
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-800 shrink-0">{{ $w->id }}</span>
                                <span class="truncate font-bold text-slate-900">{{ $w->nama }}</span>
                            </div>
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 shrink-0">Site {{ $w->site_code }}</span>
                        </div>
                    @endforeach
                    <div id="worker_empty_state" class="hidden px-4 py-3 text-center text-xs text-slate-400 font-medium">
                        Tidak ada pekerja ditemukan
                    </div>
                </div>
            </div>

            <!-- Blood Pressure (Sistol / Diastol) -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Tekanan Darah (mmHg) <span class="text-rose-500">*</span>
                </label>

                <div class="mt-1.5 grid grid-cols-2 gap-3">
                    <div>
                        <div class="relative">
                            <input 
                                type="number" 
                                id="sistol_input"
                                name="sistol" 
                                value="{{ old('sistol') }}" 
                                min="70" 
                                max="220" 
                                placeholder="cth: 120" 
                                required
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-14 pl-3 text-sm font-normal text-slate-900 transition-all placeholder:text-slate-400 placeholder:font-normal focus:border-slate-900 focus:bg-white focus:outline-hidden"
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
                                value="{{ old('diastol') }}" 
                                min="40" 
                                max="130" 
                                placeholder="cth: 80" 
                                required
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-14 pl-3 text-sm font-normal text-slate-900 transition-all placeholder:text-slate-400 placeholder:font-normal focus:border-slate-900 focus:bg-white focus:outline-hidden"
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
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Suhu Tubuh (°C) <span class="text-rose-500">*</span>
                </label>
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
                        value="{{ old('suhu') }}" 
                        min="34" 
                        max="42" 
                        placeholder="cth: 36.8" 
                        required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-10 pl-9 text-sm font-normal text-slate-900 transition-all placeholder:text-slate-400 placeholder:font-normal focus:border-slate-900 focus:bg-white focus:outline-hidden"
                    >
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-slate-400">
                        °C
                    </span>
                </div>
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
// --- Searchable Worker Combobox ---
const workerSearchIn = document.getElementById('worker_search_input');
const workerSelect = document.getElementById('worker_id');
const workerDropdown = document.getElementById('worker_dropdown_menu');
const workerToggle = document.getElementById('worker_combobox_toggle');
const workerArrow = document.getElementById('worker_combobox_arrow');
const workerOptions = document.querySelectorAll('.worker-option');
const workerEmpty = document.getElementById('worker_empty_state');

function openDropdown() {
    workerDropdown.classList.remove('hidden');
    workerArrow.classList.add('rotate-180');
}

function closeDropdown() {
    workerDropdown.classList.add('hidden');
    workerArrow.classList.remove('rotate-180');
}

function selectWorker(id, display) {
    workerSelect.value = id;
    workerSearchIn.value = display;
    closeDropdown();
}

function filterWorkers(q) {
    q = q.trim().toLowerCase();
    let visibleCount = 0;
    workerOptions.forEach(opt => {
        const id = opt.getAttribute('data-code');
        const nama = opt.getAttribute('data-nama');
        const site = opt.getAttribute('data-site');
        const match = !q || id.includes(q) || nama.includes(q) || site.includes(q);
        if (match) {
            opt.classList.remove('hidden');
            visibleCount++;
        } else {
            opt.classList.add('hidden');
        }
    });
    if (visibleCount === 0) {
        workerEmpty.classList.remove('hidden');
    } else {
        workerEmpty.classList.add('hidden');
    }
}

workerSearchIn.addEventListener('focus', () => {
    openDropdown();
    filterWorkers(workerSearchIn.value);
});

workerSearchIn.addEventListener('input', (e) => {
    openDropdown();
    filterWorkers(e.target.value);
});

workerToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    if (workerDropdown.classList.contains('hidden')) {
        openDropdown();
        workerSearchIn.focus();
    } else {
        closeDropdown();
    }
});

workerOptions.forEach(opt => {
    opt.addEventListener('click', () => {
        const id = opt.getAttribute('data-id');
        const display = opt.getAttribute('data-display');
        selectWorker(id, display);
    });
});

document.addEventListener('click', (e) => {
    const container = document.getElementById('worker_combobox_container');
    if (container && !container.contains(e.target)) {
        closeDropdown();
        if (workerSelect.value) {
            const currentOption = workerSelect.querySelector(`option[value="${workerSelect.value}"]`);
            if (currentOption && (!workerSearchIn.value || !workerSearchIn.value.includes(workerSelect.value))) {
                workerSearchIn.value = currentOption.textContent.trim();
            }
        } else {
            workerSearchIn.value = '';
        }
    }
});

workerSearchIn.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        const firstVisible = Array.from(workerOptions).find(opt => !opt.classList.contains('hidden'));
        if (firstVisible) {
            selectWorker(firstVisible.getAttribute('data-id'), firstVisible.getAttribute('data-display'));
        }
    } else if (e.key === 'Escape') {
        closeDropdown();
    }
});
</script>
@endsection
