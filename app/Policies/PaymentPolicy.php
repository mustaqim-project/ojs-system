<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine if the user can view the payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole(['super-admin', 'system-admin', 'finance'])) {
            return true;
        }

        // Author can view their own payment
        return $payment->author_id === $user->id;
    }

    /**
     * Determine if the user can verify the payment.
     */
    public function verify(User $user, Payment $payment): bool
    {
        return $user->hasRole(['super-admin', 'system-admin', 'finance', 'journal-manager']);
    }

    /**
     * Determine if the user can reject the payment.
     */
    public function reject(User $user, Payment $payment): bool
    {
        return $user->hasRole(['super-admin', 'system-admin', 'finance', 'journal-manager']);
    }
}
