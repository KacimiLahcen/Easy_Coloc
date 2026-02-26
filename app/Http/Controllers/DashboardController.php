<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $user = auth()->user();
        //calculate total to pay where (is_paid = false)
        $totalToPay = Payment::where('sender_id', $user->id)->where('is_paid',false)->sum('amount');
        
        $totalToCollect = Payment::where('receiver_id', $user->id)
                                ->where('is_paid', false)
                                ->sum('amount');

                                //here we called the relationship in the user model to check if the left_it?
        $activeColocation = $user->colocation()->whereNull('left_at')->first(); //first took first result cuz we said u can join only one activve coloc

        $recentExpenses = [];
            if($activeColocation) {
                                        //Eager Loading usage
                $recentExpenses = Expense::with (['payer', 'category'])
                                        ->where('colocation_id', $activeColocation->id)
                                        ->latest()->take(5)->get();
            }

            return view('dashboard', compact('user', 'totalToPay', 'totalToCollect', 'recentExpenses', 'activeColocation'));
        }
}
