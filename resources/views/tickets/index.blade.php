@extends('layouts.app')

@section('title', 'Tickets - Fixtora')

@section('content')
<div class="dashboard-container">
    <div class="page-header">
        <div>
            <h1>All Tickets</h1>
            <p class="subtitle">Manage and track all support tickets</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn-primary" style="padding: 10px 16px; text-decoration: none; color: white; background: var(--primary-light); border-radius: 6px; font-weight: 600;">+ Create New Ticket</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="padding: 12px 16px; background: rgba(5, 150, 105, 0.1); color: #059669; border-radius: 6px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--bg-light);">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 12px;">ID</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 12px;">TITLE</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 12px;">SYSTEM</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 12px;">PRIORITY</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 12px;">STATUS</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 12px;">CREATED</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr style="border-top: 1px solid var(--border-color); hover: { background: var(--bg-light); }">
                        <td style="padding: 16px;"><strong>#{{ $ticket->id }}</strong></td>
                        <td style="padding: 16px;">
                            <a href="{{ route('tickets.show', $ticket) }}" style="color: var(--primary-light); text-decoration: none;">
                                {{ $ticket->title }}
                            </a>
                        </td>
                        <td style="padding: 16px;">{{ ucfirst($ticket->system) }}</td>
                        <td style="padding: 16px;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                @if($ticket->priority === 'critical') background: rgba(220, 38, 38, 0.1); color: #dc2626;
                                @elseif($ticket->priority === 'high') background: rgba(217, 119, 6, 0.1); color: #d97706;
                                @else background: rgba(37, 99, 235, 0.1); color: #2563eb;
                                @endif">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;
                                @if($ticket->status === 'open') background: rgba(220, 38, 38, 0.1); color: #dc2626;
                                @elseif($ticket->status === 'in-progress') background: rgba(217, 119, 6, 0.1); color: #d97706;
                                @else background: rgba(5, 150, 105, 0.1); color: #059669;
                                @endif">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </td>
                        <td style="padding: 16px; color: var(--text-secondary); font-size: 13px;">
                            {{ $ticket->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px 16px; text-align: center; color: var(--text-secondary);">
                            No tickets yet. <a href="{{ route('tickets.create') }}" style="color: var(--primary-light); text-decoration: none;">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tickets->hasPages())
        <div style="margin-top: 20px;">
            {{ $tickets->links() }}
        </div>
    @endif
</div>

<style scoped>
    .dashboard-container {
        padding: 32px 24px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .subtitle {
        color: var(--text-secondary);
        margin: 0;
        font-size: 14px;
    }

    .card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0;
        box-shadow: var(--shadow);
    }

    .alert-success {
        border: 1px solid rgba(5, 150, 105, 0.2);
    }
</style>
@endsection