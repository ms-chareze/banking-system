<?php

namespace App\Observers;

use App\Exceptions\InsufficientFundsException;
use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Illuminate\Support\Facades\DB;

/**
 * Observes the Transaction model and keeps the owning Account's balance in sync.
 * Registered through the #[ObservedBy] attribute on App\Models\Transaction.
 */
class AccountObserver
{
    /**
     * Runs BEFORE the transaction row is inserted.
     * Applies the balance change and blocks the insert if the rules are broken.
     */
    public function creating(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Lock the account row so two simultaneous requests cannot both
            // read the same old balance (prevents lost updates / overdrafts).
            $account = Account::whereKey($transaction->account_id)
                ->lockForUpdate()
                ->firstOrFail();

            $amount = (float) $transaction->amount;
            $current = (float) $account->balance;
            $minimum = (float) $account->minimum_balance;

            // Deposit adds; Withdrawal / Transfer subtract.
            $newBalance = round(
                $transaction->type === 'deposit' ? $current + $amount : $current - $amount,
                2
            );

            // Critical rule: no negative balance and never below the minimum balance.
            if ($transaction->type !== 'deposit' && ($newBalance < 0 || $newBalance < $minimum)) {
                throw new InsufficientFundsException(
                    'Insufficient funds: this withdrawal would drop the balance below the minimum of '
                    . number_format($minimum, 2) . '.'
                );
            }

            $account->balance = $newBalance;

            // Balance has recovered, so allow a future low-balance alert again.
            if ($newBalance > $minimum) {
                $account->low_balance_notified = false;
            }

            $account->save();
        });
    }

    /**
     * Runs AFTER the transaction row is saved.
     * Sends a low-balance alert once per "low balance episode".
     */
    public function created(Transaction $transaction): void
    {
        if ($transaction->type === 'deposit') {
            return;
        }

        $account = $transaction->account;

        if ((float) $account->balance <= (float) $account->minimum_balance
            && ! $account->low_balance_notified) {
            $account->user->notify(new LowBalanceNotification($account));

            $account->forceFill(['low_balance_notified' => true])->saveQuietly();
        }
    }
}