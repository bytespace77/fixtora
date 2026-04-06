@extends('layouts.app')

@section('title', 'Notifications - Fixtora')

@section('styles')
<style>
.noti-page { max-width: 1240px; }
.noti-header { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:22px; flex-wrap:wrap; }
.noti-header h1 { font-size:22px; font-weight:800; letter-spacing:-.5px; color:var(--navy); }
.noti-header p { font-size:13px; color:var(--muted); margin-top:4px; }
.noti-actions { display:flex; gap:8px; }
.btn-sm { padding:8px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:1px solid var(--border); background:var(--surface); color:var(--text-2); font-family:inherit; }
.btn-sm.primary { background:var(--blue); border-color:var(--blue); color:#fff; }

.noti-grid { display:grid; grid-template-columns: 1fr; gap:16px; }
.card-box { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); }
.card-head { display:flex; justify-content:space-between; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--border); }
.card-title { font-size:14px; font-weight:700; color:var(--navy); }
.card-sub { font-size:11.5px; color:var(--muted); margin-top:2px; }
.chip { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border-radius:999px; padding:4px 9px; }
.chip.blue { color:var(--blue-2); background:var(--blue-bg); border:1px solid #bfdbfe; }

.overview { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; padding:14px 16px; border-bottom:1px solid var(--border); }
.ov-item { border:1px solid var(--border); border-radius:9px; padding:12px; background:#fafcff; }
.ov-label { font-size:10.5px; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; font-weight:700; margin-bottom:4px; }
.ov-value { font-size:24px; font-weight:800; color:var(--navy-3); letter-spacing:-.5px; line-height:1; }
.ov-help { font-size:11px; color:var(--muted); margin-top:4px; }

.noti-list { padding:4px 0; }
.n-group { padding:8px 0 2px; }
.n-group-label { font-size:11px; font-weight:700; color:var(--muted-lt); text-transform:uppercase; letter-spacing:.6px; padding:0 16px 6px; }
.n-item { display:flex; gap:10px; align-items:flex-start; padding:12px 16px; border-top:1px solid #f1f5f9; }
.n-item:first-child { border-top:none; }
.n-item.unread { background:#f8fbff; }
.n-dot { width:9px; height:9px; border-radius:50%; margin-top:6px; flex-shrink:0; }
.n-dot.info { background:#3b82f6; }
.n-dot.warn { background:#f97316; }
.n-dot.success { background:#22c55e; }
.n-main { flex:1; min-width:0; }
.n-title { font-size:13px; font-weight:700; color:var(--text); line-height:1.3; }
.n-desc { font-size:12px; color:var(--muted); margin-top:3px; line-height:1.4; }
.n-meta { display:flex; align-items:center; gap:8px; margin-top:7px; font-size:11px; color:var(--muted-lt); }
.n-tag { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:3px 7px; border-radius:999px; border:1px solid var(--border); background:#fff; color:var(--text-2); }
.n-tag.tag-task-assigned  { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
.n-tag.tag-ticket-assigned { background:#f0fdf4; color:#15803d; border-color:#bbf7d0; }
.n-tag.tag-task-update    { background:#fff7ed; color:#c2410c; border-color:#fed7aa; }
.n-tag.tag-ticket-update  { background:#fff7ed; color:#c2410c; border-color:#fed7aa; }
.n-tag.tag-client-update  { background:#fdf4ff; color:#7e22ce; border-color:#e9d5ff; }
.n-tag.tag-workflow       { background:#fef9c3; color:#92400e; border-color:#fde68a; }
.n-tag.tag-reminder       { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
.n-tag.tag-system         { background:#f8fafc; color:var(--muted); border-color:var(--border); }

.pref-body { padding:14px 16px; }
.pref-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f1f5f9; gap:12px; }
.pref-row:last-child { border-bottom:none; }
.pref-name { font-size:12.5px; font-weight:700; color:var(--text); }
.pref-desc { font-size:11.5px; color:var(--muted); margin-top:2px; }
.pref-pill { font-size:10px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; border-radius:999px; padding:4px 9px; }
.pref-pill.on { background:#dcfce7; color:var(--green); border:1px solid #bbf7d0; }
.pref-pill.off { background:#f8fafc; color:var(--muted); border:1px solid var(--border); }

.empty-msg { text-align:center; padding:30px 20px; color:var(--muted); font-size:13px; font-weight:600; }

@media (max-width: 1080px) {
  .noti-grid { grid-template-columns:1fr; }
}
@media (max-width: 700px) {
  .overview { grid-template-columns:1fr; }
}
</style>
@endsection

@section('content')
@php
    $notifications = $allNotifications ?? collect();
    $todayNotifications = $notifications->filter(fn ($n) => isset($n['time']) && $n['time'] && $n['time']->isToday())->values();
    $earlierNotifications = $notifications->filter(fn ($n) => !isset($n['time']) || !$n['time'] || !$n['time']->isToday())->values();
    $unreadCount = $notifications->where('is_new', true)->count();
    $todayCount = $todayNotifications->count();
    $criticalCount = $notifications->filter(fn ($n) => ($n['type'] ?? '') === 'red')->count();
@endphp
<div class="noti-page">
    <div class="noti-header">
        <div>
            <h1>Notifications</h1>
            <p>Track alerts, ticket updates, and operational events in one place.</p>
        </div>
        <div class="noti-actions">
            <button id="markAllReadBtn" class="btn-sm primary">Mark all as read</button>
        </div>
    </div>

    <div class="noti-grid">
        <div class="card-box">
            <div class="card-head">
                <div>
                    <div class="card-title">Activity Feed</div>
                    <div class="card-sub">Latest system and ticket notifications</div>
                </div>
                <span class="chip blue">Live</span>
            </div>

            <div class="overview">
                <div class="ov-item">
                    <div class="ov-label">Unread</div>
                    <div class="ov-value" id="ovUnreadCount">{{ $unreadCount }}</div>
                    <div class="ov-help">Need attention</div>
                </div>
                <div class="ov-item">
                    <div class="ov-label">Today</div>
                    <div class="ov-value">{{ $todayCount }}</div>
                    <div class="ov-help">Notifications received</div>
                </div>
                <div class="ov-item">
                    <div class="ov-label">Critical</div>
                    <div class="ov-value">{{ $criticalCount }}</div>
                    <div class="ov-help">Urgent alerts</div>
                </div>
            </div>

            <div class="noti-list">
                <div class="n-group">
                    <div class="n-group-label">Today</div>
                    @forelse($todayNotifications as $notification)
                        @php
                            $catSlug = 'tag-' . strtolower(str_replace(' ', '-', $notification['category'] ?? 'system'));
                        @endphp
                        <a href="{{ $notification['url'] ?? route('notifications.index') }}" class="n-item {{ !empty($notification['is_new']) ? 'unread' : '' }}" style="text-decoration:none;color:inherit">
                            <span class="n-dot {{ ($notification['type'] ?? '') === 'red' ? 'warn' : (($notification['type'] ?? '') === 'green' ? 'success' : (($notification['type'] ?? '') === 'orange' ? 'warn' : 'info')) }}"></span>
                            <div class="n-main">
                                <div class="n-title">{{ $notification['title'] ?? 'Notification' }}</div>
                                <div class="n-desc">{{ $notification['description'] ?? '' }}</div>
                                <div class="n-meta">
                                    <span>{{ $notification['time_human'] ?? 'just now' }}</span>
                                    <span class="n-tag {{ $catSlug }}">{{ $notification['category'] ?? 'System' }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="empty-msg">No new notifications yet.</div>
                    @endforelse
                </div>

                <div class="n-group">
                    <div class="n-group-label">Earlier</div>
                    @forelse($earlierNotifications as $notification)
                        @php
                            $catSlug = 'tag-' . strtolower(str_replace(' ', '-', $notification['category'] ?? 'system'));
                        @endphp
                        <a href="{{ $notification['url'] ?? route('notifications.index') }}" class="n-item {{ !empty($notification['is_new']) ? 'unread' : '' }}" style="text-decoration:none;color:inherit">
                            <span class="n-dot {{ ($notification['type'] ?? '') === 'red' ? 'warn' : (($notification['type'] ?? '') === 'green' ? 'success' : (($notification['type'] ?? '') === 'orange' ? 'warn' : 'info')) }}"></span>
                            <div class="n-main">
                                <div class="n-title">{{ $notification['title'] ?? 'Notification' }}</div>
                                <div class="n-desc">{{ $notification['description'] ?? '' }}</div>
                                <div class="n-meta">
                                    <span>{{ $notification['time_human'] ?? 'earlier' }}</span>
                                    <span class="n-tag {{ $catSlug }}">{{ $notification['category'] ?? 'System' }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="empty-msg">No earlier notifications.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection