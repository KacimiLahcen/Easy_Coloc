<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{

    public function markAsPaid(Payment $payment)
    {
        if ($payment->sender_id !== Auth::id()) {
            abort(403, 'action failed!');
        }

        $payment->update([
            'is_paid' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Payment marked as paid!');
    }
}