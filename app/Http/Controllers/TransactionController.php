<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientFundsException;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $transactions = Transaction::with('account')
            ->whereHas('account', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create(Request $request): View
    {
        $accounts = Account::where('user_id', $request->user()->id)->get();

        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            // Must exist AND belong to the logged-in user.
            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', $request->user()->id),
            ],
            'type' => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Custom validation: sufficient funds check before anything is saved.
        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->isNotEmpty() || $request->input('type') !== 'withdrawal') {
                return;
            }

            $account = Account::find($request->input('account_id'));
            $balance = (float) $account->balance;
            $minimum = (float) $account->minimum_balance;
            $amount = (float) $request->input('amount');

            if ($balance - $amount < $minimum) {
                $validator->errors()->add('amount', sprintf(
                    'Insufficient funds. You can withdraw at most %s (a minimum balance of %s must remain).',
                    number_format(max(0, $balance - $minimum), 2),
                    number_format($minimum, 2)
                ));
            }
        });

        $data = $validator->validate();

        try {
            // If the observer throws, or the insert fails, everything rolls back together.
            DB::transaction(function () use ($data) {
                Transaction::create([
                    'account_id' => $data['account_id'],
                    'type' => $data['type'],
                    'amount' => $data['amount'],
                    'reference_number' => 'TXN-' . strtoupper(Str::random(10)),
                    'notes' => $data['notes'] ?? null,
                ]);
            });
        } catch (InsufficientFundsException $e) {
            // Safety net for the race where the balance changed after validation.
            return back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction completed successfully.');
    }
}