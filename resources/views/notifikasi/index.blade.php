@extends('layouts.app')

@section('title', 'Pusat Notifikasi')
@section('page_title', 'Pusat Notifikasi')
@section('page_subtitle', 'Semua peringatan dan pesan sistem')

@section('content')
<div class="container-fluid px-0">

    {{-- Header Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-between px-4 py-3"
                 style="background: var(--accent-gradient, linear-gradient(135deg,#6366f1,#8b5cf6)); color:#fff;">
                <div class="d-flex align-items-center gap-3">
                    <div style="background:rgba(255,255,255,0.2); border-radius:12px; padding:10px 14px;">
                        <i class="bi bi-bell-fill fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-0 fw-bold" style="font-size:1.15rem;">Pusat Notifikasi</h2>
                        <p class="mb-0 opacity-75" style="font-size:12px;">{{ count($allNotifications) }} total notifikasi</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('notifikasi.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm fw-semibold"
                            style="background:rgba(255,255,255,0.2); color:#fff; border:1px solid rgba(255,255,255,0.4); border-radius:10px;">
                        <i class="bi bi-check2-all me-1"></i> Tandai Semua Dibaca
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Notification List --}}
    @if(count($allNotifications) === 0)
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
                <h4 class="mt-3 fw-bold">Semua sudah dibaca!</h4>
                <p class="text-muted">Tidak ada notifikasi aktif saat ini.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                    <i class="bi bi-grid-1x2-fill me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($allNotifications as $notif)
                @php
                    $iconClass = $notif['icon'] ?? 'bi-bell text-secondary';
                    $barColor  = '#6366f1';
                    if (str_contains($iconClass, 'danger'))  $barColor = '#ef4444';
                    elseif (str_contains($iconClass, 'warning')) $barColor = '#f59e0b';
                    elseif (str_contains($iconClass, 'success')) $barColor = '#22c55e';
                    elseif (str_contains($iconClass, 'info'))    $barColor = '#3b82f6';
                    $isRead = $notif['is_read'] ?? false;
                    $isSchedule = $notif['source'] === 'jadwal_perawatan';
                @endphp

                <div class="card border-0 shadow-sm notif-card {{ $isRead ? 'opacity-60' : '' }}"
                     style="border-radius:14px; overflow:hidden; transition: box-shadow 0.2s, transform 0.2s;"
                     id="{{ !$isSchedule && isset($notif['id']) ? 'notif-card-'.$notif['id'] : '' }}">
                    <div class="d-flex align-items-stretch">
                        {{-- Color bar --}}
                        <div style="width: 5px; flex-shrink:0; background: {{ $barColor }};"></div>

                        <div class="d-flex align-items-center gap-3 p-3 w-100">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                 style="width:44px; height:44px; background: {{ $barColor }}18; font-size:1.3rem;">
                                <i class="bi {{ $iconClass }}"></i>
                            </div>

                            {{-- Content --}}
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="fw-bold" style="font-size:14px;">{{ $notif['title'] ?? 'Notifikasi' }}</span>
                                    @if(!$isRead && !$isSchedule)
                                        <span class="badge rounded-pill bg-primary" style="font-size:9px; padding: 3px 7px;">Baru</span>
                                    @endif
                                    @if($isSchedule)
                                        <span class="badge rounded-pill" style="font-size:9px; background:#f59e0b; color:#fff; padding:3px 7px;">Jadwal Perawatan</span>
                                    @elseif(isset($notif['source']) && $notif['source'] === 'dokumen_kendaraan')
                                        <span class="badge rounded-pill bg-danger text-white" style="font-size:9px; padding:3px 7px;">Dokumen Armada</span>
                                    @endif
                                </div>
                                <p class="mb-0 text-muted" style="font-size:13px;">{{ $notif['message'] ?? '' }}</p>
                                @if(isset($notif['created_at']))
                                    <div class="text-muted mt-1" style="font-size:11px;">
                                        <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex-shrink-0 d-flex align-items-center gap-2">
                                @if($notif['link'])
                                    @if(!$isSchedule && isset($notif['id']) && !$isRead)
                                        <a href="#"
                                           onclick="markReadAndGo(event, {{ $notif['id'] }}, '{{ $notif['link'] }}')"
                                           class="btn btn-sm btn-primary"
                                           style="border-radius:8px; font-size:12px;">
                                            <i class="bi bi-arrow-right-circle me-1"></i>Lihat
                                        </a>
                                    @else
                                        <a href="{{ $notif['link'] }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           style="border-radius:8px; font-size:12px;">
                                            <i class="bi bi-arrow-right-circle me-1"></i>Lihat
                                        </a>
                                    @endif
                                @endif

                                @if(!$isSchedule && isset($notif['id']) && !$isRead)
                                    <button class="btn btn-sm btn-outline-success"
                                            style="border-radius:8px; font-size:12px;"
                                            onclick="markReadOnly({{ $notif['id'] }})">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<style>
.notif-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
    transform: translateY(-1px);
}
.opacity-60 { opacity: 0.62; }
</style>
@endsection

@section('scripts')
<script>
function markReadAndGo(event, notifId, link) {
    event.preventDefault();
    fetch(`/notifikasi/${notifId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).finally(() => {
        window.location.href = link;
    });
}

function markReadOnly(notifId) {
    fetch(`/notifikasi/${notifId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        const card = document.getElementById('notif-card-' + notifId);
        if (card) {
            card.classList.add('opacity-60');
            // Remove "Baru" badge and action buttons
            const badge = card.querySelector('.badge.bg-primary');
            const btns  = card.querySelectorAll('.btn-primary, .btn-outline-success');
            if (badge) badge.remove();
            btns.forEach(b => b.remove());
        }
        // Refresh bell badge
        if (typeof fetchNotifCount === 'function') fetchNotifCount();
    });
}
</script>
@endsection
