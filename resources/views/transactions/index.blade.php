@extends('layouts.bank')

@section('title', 'Transactions')

@section('content')
    <div class="card">
        <h2>Transaction History</h2>
        <table>
            <thead>
            <tr>
                <th>Reference</th>
                <th>Account</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->reference_number }}</td>
                    <td>{{ $transaction->account->account_number }}</td>
                    <td>{{ ucfirst($transaction->type) }}</td>
                    <td>{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No transactions yet.</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $transactions->links() }}
    </div>
@endsection