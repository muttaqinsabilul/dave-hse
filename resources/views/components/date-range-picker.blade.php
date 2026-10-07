@props([
    'from' => '',
    'to' => '',
    'nameFrom' => 'from',
    'nameTo' => 'to',
    'align' => 'right',
])

@php
    $id = 'drp_' . str_replace('.', '', uniqid('', true));
    $todayStr = now()->toDateString();
    
    // Default mode adalah 'today' jika parameter filter kosong atau sama dengan hari ini
    $isToday = (empty($from) && empty($to)) || ($from === $todayStr && $to === $todayStr);
    
    // Format teks display trigger di navbar/header
    if ($isToday) {
        $displayText = 'Hari Ini (' . now()->translatedFormat('d M Y') . ')';
    } elseif (!empty($from) && !empty($to)) {
        try {
            $f = \Illuminate\Support\Carbon::parse($from);
            $t = \Illuminate\Support\Carbon::parse($to);
            if ($f->isSameDay($t)) {
                $displayText = $f->translatedFormat('d M Y');
            } elseif ($f->year === $t->year && $f->year === now()->year) {
                $displayText = $f->translatedFormat('d M') . ' - ' . $t->translatedFormat('d M');
            } elseif ($f->year === $t->year) {
                $displayText = $f->translatedFormat('d M') . ' - ' . $t->translatedFormat('d M Y');
            } else {
                $displayText = $f->translatedFormat('d M Y') . ' - ' . $t->translatedFormat('d M Y');
            }
        } catch (\Throwable $e) {
            $displayText = "{$from} - {$to}";
        }
    } elseif (!empty($from)) {
        $displayText = "Dari {$from}";
    } elseif (!empty($to)) {
        $displayText = "Sampai {$to}";
    } else {
        $displayText = 'Hari Ini (' . now()->translatedFormat('d M Y') . ')';
    }
@endphp

<div class="relative inline-block text-left" id="{{ $id }}_wrapper">
    <!-- Hidden Inputs for Form Submission -->
    <input type="hidden" name="{{ $nameFrom }}" value="{{ $from ?: ($isToday ? $todayStr : '') }}" id="{{ $id }}_from">
    <input type="hidden" name="{{ $nameTo }}" value="{{ $to ?: ($isToday ? $todayStr : '') }}" id="{{ $id }}_to">

    <!-- Toast Notifikasi Simple di Kanan Atas (Tampil hanya setelah klik 'Terapkan Filter' jika tanggal tidak valid) -->
    <div 
        id="{{ $id }}_toast" 
        class="fixed top-5 right-5 z-[99999] flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3.5 shadow-xl ring-1 ring-slate-900/5 transition-all duration-300 ease-out max-w-sm pointer-events-none"
        style="transform: translateX(120%); opacity: 0;"
        role="alert"
    >
        <!-- Icon Alert Simple -->
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-500/20">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>

        <!-- Pesan Inti Sederhana -->
        <div class="text-xs font-semibold text-slate-800 leading-snug" id="{{ $id }}_toast_msg">
            Tanggal <strong>Dari</strong> tidak boleh lebih akhir dari tanggal <strong>Sampai</strong>!
        </div>

        <!-- Tombol Tutup (X) -->
        <button 
            type="button" 
            id="{{ $id }}_toast_close"
            class="ml-auto rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition cursor-pointer shrink-0"
            title="Tutup Notifikasi"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <!-- Trigger Button -->
    <button 
        type="button" 
        id="{{ $id }}_trigger" 
        class="inline-flex items-center gap-2.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-2xs transition-all hover:border-slate-400 hover:bg-slate-50 focus:outline-hidden focus:ring-2 focus:ring-slate-900/10 active:scale-[0.98] cursor-pointer"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <svg class="h-3.5 w-3.5 text-slate-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span id="{{ $id }}_label" class="font-semibold text-slate-800">{{ $displayText }}</span>
        <svg class="h-3 w-3 text-slate-400 shrink-0 transition-transform duration-200" id="{{ $id }}_chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </button>

    <!-- Dropdown Popover Modal -->
    <div 
        id="{{ $id }}_popover" 
        class="hidden absolute {{ $align === 'right' ? 'right-0' : 'left-0' }} top-full mt-2 z-50 rounded-2xl border border-slate-200/90 bg-white shadow-2xl transition-all"
        style="width: max-content; max-width: calc(100vw - 2rem);"
    >
        <!-- Top Switcher Bar: Tinggi 56px -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-2.5 h-14 min-h-[56px] bg-slate-50/70 rounded-t-2xl">
            <!-- Segmented Control Tabs -->
            <div class="inline-flex rounded-xl bg-slate-200/80 p-1 text-xs font-semibold">
                <button 
                    type="button" 
                    id="{{ $id }}_tab_today" 
                    class="rounded-lg px-4 py-2 transition cursor-pointer font-bold bg-white text-slate-900 shadow-xs"
                >
                    Hari Ini
                </button>
                <button 
                    type="button" 
                    id="{{ $id }}_tab_custom" 
                    class="rounded-lg px-4 py-2 transition cursor-pointer font-medium text-slate-600 hover:text-slate-900"
                >
                    Kustom Tanggal
                </button>
            </div>

            <!-- Active Mode Hint / Status (Kosong di tab Hari Ini) -->
            <div class="text-[11px] font-medium text-slate-500">
                <span id="{{ $id }}_mode_desc"></span>
            </div>
        </div>

        <!-- Main Calendar Area -->
        <div class="p-5 bg-white overflow-x-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:divide-x sm:divide-slate-100" id="{{ $id }}_calendar_grid_wrapper">
                <!-- Kalender Pertama: DARI (Mulai) -->
                <div class="sm:pr-4" id="{{ $id }}_col_start">
                    <!-- Penanda Kolom 'Dari' (Ditinggikan, tanpa titik & tanpa background) -->
                    <div id="{{ $id }}_header_start" class="flex items-center justify-between mb-3 pb-2.5 pt-1 border-b border-slate-100">
                        <span id="{{ $id }}_badge_start" class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                            Dari (Mulai)
                        </span>
                        <span id="{{ $id }}_hint_start" class="text-xs font-bold text-slate-800 select-none">-</span>
                    </div>

                    <!-- Navigation Month Header -->
                    <div class="flex items-center justify-between mb-3.5 px-1 py-1">
                        <button type="button" id="{{ $id }}_prev1" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer" title="Bulan sebelumnya">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <span id="{{ $id }}_title1" class="text-xs sm:text-sm font-bold text-slate-800 select-none">Oktober 2026</span>
                        <button type="button" id="{{ $id }}_next1" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer" title="Bulan berikutnya">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>

                    <!-- Days of Week Header -->
                    <div class="grid grid-cols-7 mb-1 text-center select-none">
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Su</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Mo</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Tu</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">We</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Th</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Fr</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Sa</div>
                    </div>

                    <!-- Days Grid -->
                    <div id="{{ $id }}_grid1" class="grid grid-cols-7 text-center"></div>
                </div>

                <!-- Kalender Kedua: SAMPAI (Selesai) -->
                <div class="sm:pl-4" id="{{ $id }}_col_end">
                    <!-- Penanda Kolom 'Sampai' (Ditinggikan, tanpa titik & tanpa background) -->
                    <div id="{{ $id }}_header_end" class="flex items-center justify-between mb-3 pb-2.5 pt-1 border-b border-slate-100">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                            Sampai (Selesai)
                        </span>
                        <span id="{{ $id }}_hint_end" class="text-xs font-bold text-slate-800 select-none">-</span>
                    </div>

                    <!-- Navigation Month Header -->
                    <div class="flex items-center justify-between mb-3.5 px-1 py-1">
                        <button type="button" id="{{ $id }}_prev2" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer" title="Bulan sebelumnya">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <span id="{{ $id }}_title2" class="text-xs sm:text-sm font-bold text-slate-800 select-none">Oktober 2026</span>
                        <button type="button" id="{{ $id }}_next2" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer" title="Bulan berikutnya">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>

                    <!-- Days of Week Header -->
                    <div class="grid grid-cols-7 mb-1 text-center select-none">
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Su</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Mo</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Tu</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">We</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Th</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Fr</div>
                        <div class="text-[11px] font-semibold text-slate-400 py-1">Sa</div>
                    </div>

                    <!-- Days Grid -->
                    <div id="{{ $id }}_grid2" class="grid grid-cols-7 text-center"></div>
                </div>
            </div>
        </div>

        <!-- Footer Action Bar -->
        <div class="border-t border-slate-100 px-5 py-3.5 bg-white flex items-center justify-end gap-2.5 rounded-b-2xl">
            <button type="button" id="{{ $id }}_cancel" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs cursor-pointer active:scale-95">
                Batal
            </button>
            <button type="button" id="{{ $id }}_apply" class="rounded-xl bg-slate-900 px-6 py-2 text-xs font-semibold text-white shadow-xs hover:bg-slate-800 active:scale-[0.98] transition cursor-pointer">
                Terapkan Filter
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const id = @json($id);
    const initialFromStr = @json($from);
    const initialToStr = @json($to);
    const todayStr = @json($todayStr);
    const isInitiallyToday = @json($isToday);

    const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    // DOM Elements
    const wrapper = document.getElementById(id + '_wrapper');
    const trigger = document.getElementById(id + '_trigger');
    const popover = document.getElementById(id + '_popover');
    const label = document.getElementById(id + '_label');
    const chevron = document.getElementById(id + '_chevron');
    const fromInput = document.getElementById(id + '_from');
    const toInput = document.getElementById(id + '_to');

    const tabToday = document.getElementById(id + '_tab_today');
    const tabCustom = document.getElementById(id + '_tab_custom');
    const modeDesc = document.getElementById(id + '_mode_desc');
    const headerStart = document.getElementById(id + '_header_start');
    const hintStart = document.getElementById(id + '_hint_start');
    const hintEnd = document.getElementById(id + '_hint_end');

    const toastEl = document.getElementById(id + '_toast');
    const toastMsg = document.getElementById(id + '_toast_msg');
    const toastCloseBtn = document.getElementById(id + '_toast_close');

    const calGridWrapper = document.getElementById(id + '_calendar_grid_wrapper');
    const colStart = document.getElementById(id + '_col_start');
    const colEnd = document.getElementById(id + '_col_end');

    const grid1 = document.getElementById(id + '_grid1');
    const grid2 = document.getElementById(id + '_grid2');
    const title1 = document.getElementById(id + '_title1');
    const title2 = document.getElementById(id + '_title2');
    const prev1 = document.getElementById(id + '_prev1');
    const next1 = document.getElementById(id + '_next1');
    const prev2 = document.getElementById(id + '_prev2');
    const next2 = document.getElementById(id + '_next2');

    const cancelBtn = document.getElementById(id + '_cancel');
    const applyBtn = document.getElementById(id + '_apply');

    if (!wrapper || !trigger || !popover) return;

    // Pindahkan elemen toast langsung ke body agar selalu menempel presisi di kanan atas layar (viewport) tanpa terpengaruh posisi kontainer
    if (toastEl && toastEl.parentElement !== document.body) {
        document.body.appendChild(toastEl);
    }

    // Toast Timer & Helper Functions
    let toastTimer = null;

    function hideToast() {
        if (!toastEl) return;
        toastEl.style.transform = 'translateX(120%)';
        toastEl.style.opacity = '0';
        toastEl.style.pointerEvents = 'none';
        if (toastTimer) {
            clearTimeout(toastTimer);
            toastTimer = null;
        }
    }

    function showToast(msg) {
        if (!toastEl) return;
        if (toastMsg && msg) toastMsg.innerHTML = msg;

        // Animasi slide-in halus dari kanan atas
        requestAnimationFrame(() => {
            toastEl.style.transition = 'transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease';
            toastEl.style.transform = 'translateX(0)';
            toastEl.style.opacity = '1';
            toastEl.style.pointerEvents = 'auto';
        });

        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            hideToast();
        }, 3000); // Tepat 3 Detik
    }

    if (toastCloseBtn) {
        toastCloseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            hideToast();
        });
    }

    // Helper: format Date to YYYY-MM-DD
    function formatYMD(date) {
        if (!date) return '';
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    // Helper: parse YYYY-MM-DD to local Date
    function parseYMD(str) {
        if (!str || typeof str !== 'string') return null;
        const parts = str.trim().split('-');
        if (parts.length !== 3) return null;
        const y = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10) - 1;
        const d = parseInt(parts[2], 10);
        if (isNaN(y) || isNaN(m) || isNaN(d)) return null;
        return new Date(y, m, d);
    }

    function formatPill(date) {
        if (!date) return '-';
        return `${date.getDate()} ${MONTHS_SHORT[date.getMonth()]} ${date.getFullYear()}`;
    }

    // State
    const todayDate = parseYMD(todayStr) || new Date();
    todayDate.setHours(0, 0, 0, 0);

    let currentMode = isInitiallyToday ? 'today' : 'custom';
    let selectedStart = isInitiallyToday ? new Date(todayDate) : (parseYMD(initialFromStr) || new Date(todayDate));
    let selectedEnd = isInitiallyToday ? new Date(todayDate) : (parseYMD(initialToStr) || new Date(todayDate));

    let tempStart = new Date(selectedStart);
    let tempEnd = new Date(selectedEnd);

    // Navigasi bulan INDEPENDEN untuk kalender 1 ("Dari") dan kalender 2 ("Sampai")
    let leftYear = tempStart.getFullYear();
    let leftMonth = tempStart.getMonth();

    let rightYear = tempEnd.getFullYear();
    let rightMonth = tempEnd.getMonth();

    function renderMonthGrid(gridEl, year, month, isLeftCol) {
        gridEl.innerHTML = '';

        const firstDay = new Date(year, month, 1).getDay(); // 0 is Sunday
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrev = new Date(year, month, 0).getDate();

        const sYMD = tempStart ? formatYMD(tempStart) : null;
        const eYMD = tempEnd ? formatYMD(tempEnd) : null;
        const isLocked = (currentMode === 'today');

        // 1. Leading days dari bulan sebelumnya
        for (let i = firstDay - 1; i >= 0; i--) {
            const cell = document.createElement('div');
            cell.className = 'h-9 w-9 flex items-center justify-center text-xs text-slate-300 font-medium select-none';
            cell.textContent = daysInPrev - i;
            gridEl.appendChild(cell);
        }

        // 2. Days bulan saat ini
        for (let d = 1; d <= daysInMonth; d++) {
            const curDate = new Date(year, month, d);
            const curYMD = formatYMD(curDate);

            // PURE SELECTION:
            // - Di Kalender Kiri (isLeftCol = true): HANYA sorot tanggal tempStart (sYMD)!
            // - Di Kalender Kanan (isLeftCol = false): HANYA sorot tanggal tempEnd (eYMD)!
            let isSelected = false;
            if (isLocked) {
                isSelected = (curYMD === todayStr);
            } else if (isLeftCol) {
                isSelected = (sYMD && curYMD === sYMD);
            } else {
                isSelected = (eYMD && curYMD === eYMD);
            }

            const cell = document.createElement('div');
            cell.className = 'relative h-9 w-9 flex items-center justify-center select-none text-xs';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = d;

            if (isSelected) {
                // Sesuai tema button website: bg-slate-900 (Hitam / Dark Slate)
                btn.className = 'relative z-10 h-8 w-8 rounded-lg bg-slate-900 text-white font-bold flex items-center justify-center shadow-xs transition';
                if (isLocked) {
                    btn.classList.add('ring-2', 'ring-slate-400');
                }
            } else if (isLocked) {
                // State terkunci saat tab Hari Ini aktif
                btn.className = 'relative z-10 h-8 w-8 rounded-lg text-slate-400 font-medium flex items-center justify-center cursor-default';
            } else {
                // State normal saat tab Kustom Tanggal aktif
                btn.className = 'relative z-10 h-8 w-8 rounded-lg text-slate-700 font-medium hover:bg-slate-100 hover:text-slate-900 flex items-center justify-center cursor-pointer transition';
            }

            // PURE INPUT TANGGAL (Tanpa realtime toast saat klik)
            if (!isLocked) {
                btn.addEventListener('click', (ev) => {
                    ev.stopPropagation();
                    if (isLeftCol) {
                        tempStart = new Date(curDate);
                        hintStart.textContent = formatPill(tempStart);
                    } else {
                        tempEnd = new Date(curDate);
                        hintEnd.textContent = formatPill(tempEnd);
                    }
                    renderAll();
                });
            }

            cell.appendChild(btn);
            gridEl.appendChild(cell);
        }

        // 3. Trailing days dari bulan berikutnya
        const totalRendered = firstDay + daysInMonth;
        const trailing = (7 - (totalRendered % 7)) % 7;
        for (let d = 1; d <= trailing; d++) {
            const cell = document.createElement('div');
            cell.className = 'h-9 w-9 flex items-center justify-center text-xs text-slate-300 font-medium select-none';
            cell.textContent = d;
            gridEl.appendChild(cell);
        }
    }

    function renderAll() {
        if (currentMode === 'today') {
            // Mode 'Hari Ini': Tampilkan hanya 1 kalender, terkunci, tidak bisa diubah-ubah
            tabToday.className = 'rounded-lg px-4 py-2 transition cursor-pointer font-bold bg-white text-slate-900 shadow-xs';
            tabCustom.className = 'rounded-lg px-4 py-2 transition cursor-pointer font-medium text-slate-600 hover:text-slate-900';
            
            // Hapus teks 'Mode Hari Ini (Otomatis terkunci)'
            modeDesc.textContent = '';
            
            // Sembunyikan header 'Dari / Hari ini (Terkunci)' di atas kalender 1
            headerStart.classList.add('hidden');

            // Sembunyikan kalender kanan ("Sampai")
            colEnd.classList.add('hidden');
            colStart.className = 'w-full max-w-[340px] mx-auto';
            calGridWrapper.className = 'grid grid-cols-1';

            // Kunci navigasi kalender
            prev1.classList.add('invisible');
            next1.classList.add('invisible');

            // Render kalender bulan hari ini
            leftYear = todayDate.getFullYear();
            leftMonth = todayDate.getMonth();
            title1.textContent = `${MONTHS[leftMonth]} ${leftYear}`;
            renderMonthGrid(grid1, leftYear, leftMonth, true);

        } else {
            // Mode 'Kustom Tanggal': Tampilkan 2 kalender independen (Dari & Sampai)
            tabToday.className = 'rounded-lg px-4 py-2 transition cursor-pointer font-medium text-slate-600 hover:text-slate-900';
            tabCustom.className = 'rounded-lg px-4 py-2 transition cursor-pointer font-bold bg-white text-slate-900 shadow-xs';

            // Tampilkan kembali header kolom 'Dari' (tanpa titik & tanpa background)
            headerStart.classList.remove('hidden');

            // Tampilkan kembali kalender kanan
            colEnd.classList.remove('hidden');
            colStart.className = 'sm:pr-4';
            calGridWrapper.className = 'grid grid-cols-1 sm:grid-cols-2 gap-6 sm:divide-x sm:divide-slate-100 min-w-[560px] sm:min-w-0';

            // Aktifkan navigasi bulan
            prev1.classList.remove('invisible');
            next1.classList.remove('invisible');
            prev2.classList.remove('invisible');
            next2.classList.remove('invisible');

            hintStart.textContent = tempStart ? formatPill(tempStart) : 'Pilih tanggal';
            hintEnd.textContent = tempEnd ? formatPill(tempEnd) : 'Pilih tanggal';

            if (tempStart && tempEnd && tempStart <= tempEnd) {
                modeDesc.textContent = `Rentang: ${tempStart.getDate()} ${MONTHS_SHORT[tempStart.getMonth()]} s/d ${tempEnd.getDate()} ${MONTHS_SHORT[tempEnd.getMonth()]} ${tempEnd.getFullYear()}`;
            } else {
                modeDesc.textContent = '';
            }

            // Update judul bulan masing-masing
            title1.textContent = `${MONTHS[leftMonth]} ${leftYear}`;
            title2.textContent = `${MONTHS[rightMonth]} ${rightYear}`;

            // Render kedua kalender secara independen
            renderMonthGrid(grid1, leftYear, leftMonth, true);
            renderMonthGrid(grid2, rightYear, rightMonth, false);
        }
    }

    // Switcher Click: Tab Hari Ini
    tabToday.addEventListener('click', (e) => {
        e.stopPropagation();
        currentMode = 'today';
        tempStart = new Date(todayDate);
        tempEnd = new Date(todayDate);
        renderAll();
    });

    // Switcher Click: Tab Kustom Tanggal
    tabCustom.addEventListener('click', (e) => {
        e.stopPropagation();
        currentMode = 'custom';
        leftYear = tempStart.getFullYear();
        leftMonth = tempStart.getMonth();
        rightYear = tempEnd.getFullYear();
        rightMonth = tempEnd.getMonth();
        renderAll();
    });

    // Navigasi Bulan Kalender Kiri ("Dari") - INDEPENDEN
    prev1.addEventListener('click', (e) => {
        e.stopPropagation();
        leftMonth--;
        if (leftMonth < 0) { leftMonth = 11; leftYear--; }
        title1.textContent = `${MONTHS[leftMonth]} ${leftYear}`;
        renderMonthGrid(grid1, leftYear, leftMonth, true);
    });
    next1.addEventListener('click', (e) => {
        e.stopPropagation();
        leftMonth++;
        if (leftMonth > 11) { leftMonth = 0; leftYear++; }
        title1.textContent = `${MONTHS[leftMonth]} ${leftYear}`;
        renderMonthGrid(grid1, leftYear, leftMonth, true);
    });

    // Navigasi Bulan Kalender Kanan ("Sampai") - INDEPENDEN
    prev2.addEventListener('click', (e) => {
        e.stopPropagation();
        rightMonth--;
        if (rightMonth < 0) { rightMonth = 11; rightYear--; }
        title2.textContent = `${MONTHS[rightMonth]} ${rightYear}`;
        renderMonthGrid(grid2, rightYear, rightMonth, false);
    });
    next2.addEventListener('click', (e) => {
        e.stopPropagation();
        rightMonth++;
        if (rightMonth > 11) { rightMonth = 0; rightYear++; }
        title2.textContent = `${MONTHS[rightMonth]} ${rightYear}`;
        renderMonthGrid(grid2, rightYear, rightMonth, false);
    });

    // Toggle Popover
    function openPopover() {
        popover.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
        chevron.classList.add('rotate-180');
        renderAll();
    }

    function closePopover() {
        popover.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
        chevron.classList.remove('rotate-180');
        // Reset working state back to committed selections
        tempStart = selectedStart ? new Date(selectedStart) : new Date(todayDate);
        tempEnd = selectedEnd ? new Date(selectedEnd) : new Date(todayDate);
        currentMode = isInitiallyToday ? 'today' : 'custom';
    }

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        if (popover.classList.contains('hidden')) {
            openPopover();
        } else {
            closePopover();
        }
    });

    cancelBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closePopover();
    });

    applyBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        let startYMD = '';
        let endYMD = '';

        if (currentMode === 'today') {
            startYMD = todayStr;
            endYMD = todayStr;
        } else {
            // Normalisasi tanggal ke jam 00:00:00 untuk perbandingan akurat
            const tStart = tempStart ? new Date(tempStart.getFullYear(), tempStart.getMonth(), tempStart.getDate()).getTime() : null;
            const tEnd = tempEnd ? new Date(tempEnd.getFullYear(), tempEnd.getMonth(), tempEnd.getDate()).getTime() : null;

            // Validasi: hanya muncul notifikasi toast di kanan atas SETELAH klik tombol Terapkan Filter
            if (tStart !== null && tEnd !== null && tEnd < tStart) {
                showToast('Tanggal <strong>Dari</strong> tidak boleh lebih akhir dari tanggal <strong>Sampai</strong>!');
                return;
            }
            startYMD = tempStart ? formatYMD(tempStart) : todayStr;
            endYMD = tempEnd ? formatYMD(tempEnd) : startYMD;
        }

        fromInput.value = startYMD;
        toInput.value = endYMD;

        // Submit form filter
        const parentForm = wrapper.closest('form');
        if (parentForm) {
            parentForm.submit();
        } else {
            closePopover();
        }
    });

    // Click Outside detection
    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            if (!popover.classList.contains('hidden')) {
                closePopover();
            }
        }
    });

    // Escape Key support
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !popover.classList.contains('hidden')) {
            closePopover();
        }
    });

    // Initial render
    renderAll();
})();
</script>
