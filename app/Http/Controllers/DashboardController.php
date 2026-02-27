<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Colocation;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        
    // Get the currently authenticated user
        
        $user = Auth::user();
        $categories = Category::all();


        //calculate total to pay where (is_paid = false)
        $totalToPay = Payment::where('sender_id', $user->id)->where('is_paid',false)->sum('amount');
        
        $totalToCollect = Payment::where('receiver_id', $user->id)
                                ->where('is_paid', false)
                                ->sum('amount');


        $paymentsToCollect = Payment::with('sender')
                                    ->where('receiver_id', $user->id)
                                    ->where('is_paid', false)
                                    ->get();


                                //here we called the relationship in the user model to check if the left_it?
        $activeColocation = $user->colocations()->wherePivot('left_at', null)->with('members')->first(); //first took first result cuz we said u can join only one activve coloc


        $totalToPay = Payment::where('sender_id', $user->id)->where('is_paid', false)->sum('amount');
        $totalToCollect = Payment::where('receiver_id', $user->id)->where('is_paid', false)->sum('amount');


        $recentExpenses = [];
            if($activeColocation) {
                                        //Eager Loading usage
                $recentExpenses = Expense::with (['payer', 'category'])
                                        ->where('colocation_id', $activeColocation->id)
                                        ->latest()->take(10)->get();
            }


        //only if user is admin
            $admin_Stats = [];
                if ($user->role === 'admin') {
                    $admin_Stats = [
                        'total_users' => User::count(),
                        'total_colocations' => Colocation::count(),
                        'banned_users' => User::where('is_banned', true)->count(),
                    ];
                }


                $debtsIOwe = Payment::with('receiver')
                        ->where('sender_id', $user->id)
                        // ->where('is_paid', false)
                        ->get();



                        $debtsToMe = Payment::with('sender')
                        ->where('receiver_id', $user->id)
                        // ->where('is_paid', false)
                        ->get();


            return view('dashboard', compact('user', 'totalToPay', 'totalToCollect', 'recentExpenses', 'activeColocation','categories','admin_Stats', 'paymentsToCollect', 'debtsIOwe', 'debtsToMe'));
        }
}
