@php
    $errorsBag = (isset($errors) && is_object($errors) && method_exists($errors, 'any')) ? $errors : session('errors');
    $firstError = ($errorsBag && method_exists($errorsBag, 'any') && $errorsBag->any()) ? $errorsBag->first() : null;
    $flashSuccess = session('status') ?? session('ok') ?? session('success');
    $flashError = session('error') ?? session('gagal') ?? $firstError;
    $initialType = $flashError ? 'error' : 'success';
    $initialMsg = $flashError ?? $flashSuccess;
    $hasInitial = !empty($initialMsg);
@endphp

<!-- Global Reusable Toast Notification (Fixed Top-Right) -->
<div 
    id="global_toast" 
    class="fixed top-5 right-5 z-[99999] flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3.5 shadow-xl ring-1 ring-slate-900/5 transition-all duration-300 ease-out max-w-sm pointer-events-none"
    style="transform: translateX(120%); opacity: 0;"
    role="alert"
    aria-live="polite"
>
    <!-- Icon Container: Success -->
    <div id="global_toast_icon_success" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20 {{ $initialType === 'success' ? '' : 'hidden' }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>

    <!-- Icon Container: Error -->
    <div id="global_toast_icon_error" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-500/20 {{ $initialType === 'error' ? '' : 'hidden' }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
    </div>

    <!-- Message Text -->
    <div class="text-xs font-semibold text-slate-800 leading-snug select-none flex-1" id="global_toast_msg">
        {!! $initialMsg !!}
    </div>

    <!-- Close Button (X) -->
    <button 
        type="button" 
        id="global_toast_close"
        class="ml-auto rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition cursor-pointer shrink-0"
        title="Tutup Notifikasi"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </button>
</div>

<script>
(function() {
    let toastTimer = null;
    const toastEl = document.getElementById('global_toast');
    const toastMsg = document.getElementById('global_toast_msg');
    const iconSuccess = document.getElementById('global_toast_icon_success');
    const iconError = document.getElementById('global_toast_icon_error');
    const closeBtn = document.getElementById('global_toast_close');

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

    function showToast(msg, type = 'success', duration = 3000) {
        if (!toastEl) return;
        if (toastMsg && msg) toastMsg.innerHTML = msg;

        if (type === 'error') {
            if (iconSuccess) iconSuccess.classList.add('hidden');
            if (iconError) iconError.classList.remove('hidden');
        } else {
            if (iconSuccess) iconSuccess.classList.remove('hidden');
            if (iconError) iconError.classList.add('hidden');
        }

        toastEl.style.transition = 'transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease';
        toastEl.style.transform = 'translateX(0)';
        toastEl.style.opacity = '1';
        toastEl.style.pointerEvents = 'auto';

        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(function() {
            hideToast();
        }, duration);
    }

    window.showToast = showToast;
    window.hideToast = hideToast;

    if (closeBtn) {
        closeBtn.addEventListener('click', hideToast);
    }

    @if($hasInitial)
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                showToast({!! json_encode($initialMsg) !!}, '{{ $initialType }}');
            }, 100);
        });
    @endif
})();
</script>
