@extends('layouts.app')

@section('title', 'My Profile - Fixtora')

@section('styles')
<style>
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
}
.page-header h1 {
    font-size: 22px;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.5px;
    margin-bottom: 3px;
}
.page-header .subtitle {
    font-size: 13px;
    font-weight: 400;
    color: var(--muted);
}

/* profile layout */
.profile-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    align-items: start;
}

/* card base */
.pcard {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

/* avatar card */
.avatar-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32px 24px;
    text-align: center;
}

.avatar-wrap {
    position: relative;
    margin-bottom: 16px;
}

.avatar-circle {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--blue), #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 800;
    color: #fff;
    box-shadow: 0 4px 20px rgba(37,99,235,0.3);
}

.avatar-online {
    position: absolute;
    bottom: 4px; right: 4px;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: var(--green);
    border: 2px solid var(--surface);
}

.avatar-name {
    font-size: 17px;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.3px;
    margin-bottom: 3px;
}

.avatar-role {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 16px;
}

.avatar-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--blue-bg);
    border: 1px solid rgba(37,99,235,0.15);
    color: var(--blue);
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 100px;
    margin-bottom: 24px;
}

.avatar-divider {
    width: 100%;
    height: 1px;
    background: var(--border);
    margin-bottom: 20px;
}

.avatar-stat {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.a-stat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.a-stat-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--muted);
}

.a-stat-val {
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
}

.a-stat-val.green { color: var(--green); }
.a-stat-val.blue  { color: var(--blue); }

/* info card */
.pcard-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.pcard-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -0.2px;
}

.pcard-title-sub {
    font-size: 12px;
    font-weight: 400;
    color: var(--muted);
    margin-top: 2px;
}

.pcard-body { padding: 24px; }

/* form fields */
.pf-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 18px;
}

.pf-group { display: flex; flex-direction: column; gap: 6px; }
.pf-group.full { grid-column: 1 / -1; }

.pf-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    color: var(--text-2);
}

.pf-value {
    height: 44px;
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 0 14px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--text);
    display: flex;
    align-items: center;
}

.pf-input {
    height: 44px;
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 0 14px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--text);
    outline: none;
    transition: all 0.15s;
    width: 100%;
}
.pf-input:focus {
    background: #fff;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.08);
}
.pf-input::placeholder { color: var(--muted-lt); font-weight: 400; }

/* readonly tag */
.pf-readonly {
    font-size: 10px; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    color: var(--muted); background: var(--border);
    padding: 2px 8px; border-radius: 4px;
}

/* activity list */
.activity-list { display: flex; flex-direction: column; gap: 0; }

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid var(--border);
}
.activity-item:last-child { border-bottom: none; }

.activity-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
}
.dot-blue   { background: var(--blue); }
.dot-green  { background: var(--green); }
.dot-orange { background: var(--orange); }
.dot-red    { background: var(--red); }

.activity-text {
    font-size: 13px;
    font-weight: 500;
    color: var(--text);
    line-height: 1.4;
    flex: 1;
}
.activity-text span { color: var(--blue); font-weight: 600; }

.activity-time {
    font-size: 11px;
    font-weight: 500;
    color: var(--muted-lt);
    white-space: nowrap;
    margin-top: 1px;
}

/* btn */
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    background: var(--blue);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(37,99,235,0.3);
}
.btn-primary:hover { background: var(--blue-2); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,0.4); color: #fff; }
.btn-primary:active { transform: translateY(0); }

.btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    background: transparent;
    color: var(--muted);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-ghost:hover { background: var(--bg); color: var(--text); border-color: var(--border-2); }

.pcard-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--bg);
}

/* security items */
.sec-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 0;
    border-bottom: 1px solid var(--border);
}
.sec-item:last-child { border-bottom: none; }

.sec-left { display: flex; align-items: center; gap: 12px; }

.sec-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: var(--blue-bg);
    display: flex; align-items: center; justify-content: center;
    color: var(--blue); flex-shrink: 0;
}

.sec-name { font-size: 13px; font-weight: 700; color: var(--text); }
.sec-desc { font-size: 12px; font-weight: 400; color: var(--muted); margin-top: 2px; }

.sec-status {
    font-size: 11px; font-weight: 700;
    padding: 4px 10px; border-radius: 100px;
}
.sec-status.enabled  { background: var(--green-bg); color: var(--green); }
.sec-status.disabled { background: var(--bg); color: var(--muted); border: 1px solid var(--border); }
</style>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1>My Profile</h1>
        <p class="subtitle">Manage your account information and preferences</p>
    </div>
</div>

<div class="profile-grid">

    <!-- LEFT: Avatar Card -->
    <div>
        <div class="pcard avatar-card">
            <div class="avatar-wrap">
                <div class="avatar-circle">{{ strtoupper(substr(Auth::user()->name ?? 'AC', 0, 2)) }}</div>
                <div class="avatar-online"></div>
            </div>
            <div class="avatar-name">{{ Auth::user()->name ?? 'Alex Chen' }}</div>
            <div class="avatar-role">Senior Architect</div>
            <div class="avatar-badge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Active Account
            </div>
            <div class="avatar-divider"></div>
            <div class="avatar-stat">
                <div class="a-stat-row">
                    <span class="a-stat-label">Email</span>
                    <span class="a-stat-val">{{ Auth::user()->email ?? 'alex@fixtora.com' }}</span>
                </div>
                <div class="a-stat-row">
                    <span class="a-stat-label">Member since</span>
                    <span class="a-stat-val">{{ Auth::user()->created_at ? Auth::user()->created_at->format('M Y') : 'Jan 2024' }}</span>
                </div>
                <div class="a-stat-row">
                    <span class="a-stat-label">Tickets raised</span>
                    <span class="a-stat-val blue">{{ \App\Models\Ticket::where('user_id', Auth::id())->count() }}</span>
                </div>
                <div class="a-stat-row">
                    <span class="a-stat-label">Status</span>
                    <span class="a-stat-val green">● Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Info + Activity + Security -->
    <div style="display:flex;flex-direction:column;gap:20px;">

        <!-- Account Info -->
        <div class="pcard">
            <div class="pcard-header">
                <div>
                    <div class="pcard-title">Account Information</div>
                    <div class="pcard-title-sub">Your personal details</div>
                </div>
            </div>
            <div class="pcard-body">
                <div class="pf-grid">
                    <div class="pf-group">
                        <div class="pf-label">Full Name</div>
                        <div class="pf-value">{{ Auth::user()->name ?? 'Alex Chen' }}</div>
                    </div>
                    <div class="pf-group">
                        <div class="pf-label" style="display:flex;align-items:center;justify-content:space-between;">
                            Email Address
                            <span class="pf-readonly">Readonly</span>
                        </div>
                        <div class="pf-value">{{ Auth::user()->email ?? 'alex@fixtora.com' }}</div>
                    </div>
                    <div class="pf-group">
                        <div class="pf-label">Role</div>
                        <div class="pf-value">Senior Architect</div>
                    </div>
                    <div class="pf-group">
                        <div class="pf-label">Department</div>
                        <div class="pf-value">Architecture & Design</div>
                    </div>
                </div>
                <p style="font-size:12px;color:var(--muted);font-weight:400;">
                    Profile editing is coming soon. Contact your administrator to update your details.
                </p>
            </div>
            <div class="pcard-footer">
                <button class="btn-primary" disabled style="opacity:0.5;cursor:not-allowed;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Save Changes
                </button>
                <button class="btn-ghost">Cancel</button>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="pcard">
            <div class="pcard-header">
                <div>
                    <div class="pcard-title">Recent Activity</div>
                    <div class="pcard-title-sub">Your latest actions on the platform</div>
                </div>
            </div>
            <div class="pcard-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-dot dot-blue"></div>
                        <div style="flex:1">
                            <div class="activity-text">Logged in to Fixtora dashboard</div>
                            <div class="activity-time">Just now</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot dot-green"></div>
                        <div style="flex:1">
                            <div class="activity-text">Account created successfully</div>
                            <div class="activity-time">{{ Auth::user()->created_at ? Auth::user()->created_at->diffForHumans() : '2 days ago' }}</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot dot-orange"></div>
                        <div style="flex:1">
                            <div class="activity-text">Viewing profile settings</div>
                            <div class="activity-time">A moment ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="pcard">
            <div class="pcard-header">
                <div>
                    <div class="pcard-title">Security</div>
                    <div class="pcard-title-sub">Manage your account security settings</div>
                </div>
            </div>
            <div class="pcard-body">
                <div class="sec-item">
                    <div class="sec-left">
                        <div class="sec-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sec-name">Password</div>
                            <div class="sec-desc">Last changed — never</div>
                        </div>
                    </div>
                    <a href="{{ route('password.request') }}" class="btn-ghost" style="font-size:12px;padding:7px 14px;">Change</a>
                </div>
                <div class="sec-item">
                    <div class="sec-left">
                        <div class="sec-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sec-name">Two-Factor Auth</div>
                            <div class="sec-desc">Add an extra layer of security</div>
                        </div>
                    </div>
                    <span class="sec-status disabled">Not enabled</span>
                </div>
                <div class="sec-item">
                    <div class="sec-left">
                        <div class="sec-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M21 2H3v16h5v4l4-4h5l4-4V2zM11 11V7m0 8v.01"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sec-name">Active Sessions</div>
                            <div class="sec-desc">1 active session on this device</div>
                        </div>
                    </div>
                    <span class="sec-status enabled">Active</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
