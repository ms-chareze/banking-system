@extends('layouts.bank')

@section('title', 'New Transaction')

@section('content')
    <div class="card">
        <h2>New Transaction</h2>

        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf

            <label for="account_id">Account</label>
            <select name="account_id" id="account_id" required>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>
                        {{ $account->account_number }} (Balance: {{ number_format($account->balance, 2) }})
                    </option>
                @endforeach
            </select>
            @error('account_id') <div class="text-danger">{{ $message }}</div> @enderror

            <label for="type">Type</label>
            <select name="type" id="type" required>
                <option value="deposit" @selected(old('type') === 'deposit')>Deposit</option>
                <option value="withdrawal" @selected(old('type') === 'withdrawal')>Withdrawal</option>
            </select>
            @error('type') <div class="text-danger">{{ $message }}</div> @enderror

            <label for="amount">Amount</label>
            <input type="number" step="0.01" min="1" name="amount" id="amount" value="{{ old('amount') }}" required>
            @error('amount') <div class="text-danger">{{ $message }}</div> @enderror

            <label for="notes">Notes (optional)</label>
            <textarea name="notes" id="notes" rows="3">{{ old('notes') }}</textarea>
            @error('notes') <div class="text-danger">{{ $message }}</div> @enderror

            <button type="submit">Submit</button>
        </form>
    </div>
@endsection