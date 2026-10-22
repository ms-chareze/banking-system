@extends('layouts.bank')

@section('title', 'Dashboard')

@section('content')
    <h2>Bank Dashboard</h2>

    <div class="stats">
        <div class="card stat">
            <div>Total Accounts</div>
            <div class="value" id="stat-accounts">–</div>
        </div>
        <div class="card stat">
            <div>Total Money in Bank</div>
            <div class="value" id="stat-balance">–</div>
        </div>
        <div class="card stat">
            <div>Transactions Today</div>
            <div class="value" id="stat-transactions">–</div>
        </div>
    </div>

    <small id="stat-updated">Waiting for first update…</small>
@endsection

@push('scripts')
<script>
    // AJAX polling: fetch fresh stats every 30 seconds and update the DOM in place.
    const STATS_URL = "{{ url('/api/dashboard/stats') }}";
    const POLL_INTERVAL_MS = 30000;
    const money = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

    async function refreshStats() {
        try {
            const response = await fetch(STATS_URL, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const data = await response.json();

            document.getElementById('stat-accounts').textContent = data.total_accounts;
            document.getElementById('stat-balance').textContent = money.format(data.total_balance);
            document.getElementById('stat-transactions').textContent = data.transactions_today;
            document.getElementById('stat-updated').textContent = 'Last updated: ' + data.updated_at;
        } catch (error) {
            console.error('Could not refresh dashboard stats:', error);
        }
    }

    refreshStats();                              // load immediately on page open
    setInterval(refreshStats, POLL_INTERVAL_MS); // then repeat every 30 seconds
</script>
@endpush