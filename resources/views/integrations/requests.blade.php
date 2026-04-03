@extends('layouts.app')

@section('title', $title . ' – Fixtora')

@section('styles')
<style>
.req-page { max-width: 1000px; }
.req-breadcrumb { display:flex; align-items:center; gap:8px; color: var(--muted); margin-bottom:18px; font-size:13px; font-weight: 500; }
.req-breadcrumb a { color: var(--muted); text-decoration:none; }
.req-breadcrumb a:hover { color: var(--navy); }
.req-hero { margin-bottom: 24px; }
.req-hero h1 { font-size:28px; font-weight:800; color: var(--navy); margin-bottom:6px; letter-spacing:-0.5px; }
.req-hero p { font-size:13px; color: var(--muted); line-height:1.6; margin:0; }

.int-table { width: 100%; border-collapse: collapse; background: var(--surface); }
.int-table th { background: var(--bg); font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--muted); padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); letter-spacing: 0.5px; }
.int-table td { padding: 16px; font-size: 13px; color: var(--text); border-bottom: 1px solid var(--border); font-weight: 500; }
.int-table tr:last-child td { border-bottom: none; }
.req-status { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; border: 1px solid transparent; }
.req-status.pending { background: var(--orange-bg); color: var(--orange); border-color: rgba(249,115,22,.2); }
.req-status.accepted { background: var(--green-bg); color: var(--green); border-color: rgba(22,163,74,.2); }
.req-status.rejected { background: var(--red-bg); color: var(--red); border-color: rgba(220,38,38,.2); }

.status-form { display:flex; gap:8px; align-items:center; }
.status-select { padding:6px 10px; font-size:12.5px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-weight:600; outline:none; }
.status-select:focus { border-color:var(--navy); }
.status-btn { padding:6px 12px; font-size:12px; font-weight:800; border-radius:6px; background:var(--navy); color:#fff; cursor:pointer; border:none; transition: opacity 0.15s; }
.status-btn:hover { opacity:0.9; }
</style>
@endsection

@section('content')
<div class="req-page">
  <div class="req-breadcrumb">
    <a href="{{ route('integrations.index') }}">Connected Ecosystem</a>
    <span style="color:var(--muted-lt)">&rsaquo;</span>
    <span style="color:var(--navy); font-weight:800;">Requests</span>
  </div>

  <div class="req-hero">
    <h1>{{ $title }}</h1>
    <p>Track the status of requested custom connectors for your organization.</p>
  </div>

  @if($requests->isEmpty())
    <div style="text-align:center; padding:40px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface); color:var(--muted); font-size:13px; font-weight:600;">
      No custom integration requests found.
    </div>
  @else
    <div style="border-radius: var(--radius); overflow-x: auto; border: 1px solid var(--border); box-shadow: var(--shadow);">
      <table class="int-table">
        <thead>
          <tr>
            <th>Integration</th>
            <th>Message / Need</th>
            <th>Date Requested</th>
            @if(Auth::user()->isSuperAdmin())
              <th>Requested By</th>
              <th>Manage Status</th>
            @else
              <th>Status</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @foreach($requests as $req)
            <tr>
              <td>
                <div style="font-weight:800;">{{ $req->requested_integration }}</div>
                <div style="font-size:11.5px; color:var(--muted); margin-top:3px;">{{ $req->contact_name }} ({{ $req->company ?? 'No Company' }})</div>
              </td>
              <td style="width: 30%;">
                <div style="font-size:12.5px; line-height: 1.5; color:var(--text);">
                  {{ $req->message ?: 'No message provided.' }}
                </div>
              </td>
              <td style="color:var(--muted);">{{ $req->created_at->format('M d, Y') }}</td>
              
              @if(Auth::user()->isSuperAdmin())
                <td>
                  <div style="font-weight:600;">{{ $req->user ? $req->user->name : 'Guest' }}</div>
                  <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">{{ $req->email }}</div>
                </td>
                <td>
                  <form method="POST" action="{{ route('integrations.requests.update', $req->id) }}" class="status-form">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="status-select">
                      <option value="pending" {{ $req->status === 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="accepted" {{ $req->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                      <option value="rejected" {{ $req->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="status-btn">Update</button>
                  </form>
                </td>
              @else
                <td>
                  <span class="req-status {{ strtolower($req->status) }}">
                    {{ ucfirst($req->status) }} 
                    @if($req->status === 'accepted')
                      <span style="opacity:0.8;">(In Dev)</span>
                    @endif
                  </span>
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
