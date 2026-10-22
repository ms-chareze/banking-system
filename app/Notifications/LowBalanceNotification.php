<?php

namespace App\Notifications;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    use Queueable;

    public function __construct(public Account $account)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'account_id' => $this->account->id,
            'account_number' => $this->account->account_number,
            'balance' => (string) $this->account->balance,
            'minimum_balance' => (string) $this->account->minimum_balance,
            'message' => sprintf(
                'Low balance alert: account %s has %s, at or below your minimum of %s.',
                $this->account->account_number,
                number_format((float) $this->account->balance, 2),
                number_format((float) $this->account->minimum_balance, 2)
            ),
        ];
    }
}