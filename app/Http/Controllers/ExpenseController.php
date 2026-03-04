<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Colocation;
use App\Models\Payment;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required | string | max:200',
            'amount' => 'required | numeric',
            'category_id' => 'required | exists:categories,id',
            'colocation_id' => 'required | exists:colocations,id',
            'date' => 'required | date',
        ]);

        $user = auth()->user();

        // $colocation = $user->colocations()->whereNull('left_at')->first();
        $colocation = Colocation::findOrFail($validated['colocation_id']);

        $expense = Expense::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_id' => $validated['category_id'],
            'colocation_id' => $colocation->id,
            'payer_id' => $user->id,  // the expense creator = the payer

        ]);

        // we get the active memebers so we splite the amount between them
        // $colocation = Colocation::find($validated['colocation_id']);

        $activeMembers = $colocation->members()->whereNull('left_at')->get();

        $activeMembersCount = $activeMembers->count();

        if ($activeMembersCount > 1) {

            // each individual's share
            $amount_Per_Member = $validated['amount'] / $activeMembersCount;

            foreach ($activeMembers as $member) {
                if ($member->id !== $user->id) {
                    Payment::create([
                        'colocation_id' => $colocation->id,
                        'sender_id' => $member->id,
                        'receiver_id' => $user->id, //the person who made the expense is automaticlly the payer of it
                        'amount' => $amount_Per_Member,
                        'is_paid' => false,
                        'expense_id' => $expense->id,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard')->with('success', 'Expense split successfully!');
    }




    
    public function destroy(Expense $expense)
    {
        
        if (auth()->id() !== $expense->created_by && auth()->id() !== $expense->colocation->created_by) {
            return back()->with('error', 'Unauthorized action.');
        }

        
        $expense->delete();

        return back()->with('success', 'Expense deleted successfully!');
    }
}
