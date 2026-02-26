<?php

namespace App\Http\Controllers;

namespace App\Models;
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

        $expense = Expense::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_id' => $validated['category_id'],
            'colocation_id' => $validated['colocation_id'],
            'payer_id' => auth()->id(),  // the expense creator = the payer
       
        ]);

        // we get the active memebers so we splite the amount between them
        $colocation = Colocation::find($validated['colocation_id']);
        $activeMembers = $colocation->members()->whereNull('left_at')->get();

        $activeMembersCount = $activeMembers->count();

        if ($activeMembersCount > 1) {
            
        // each individual's share
        $amount_Per_Member = $validated['amount'] / $activeMembersCount;

        foreach($activeMembers as $member) {
            if ($member->id !== auth()->id()) {
                Payment::create([
                    'colocation_id' => $colocation->id,
                    'sender_id' => $member->id,
                    'receiver_id' => auth()->id(), //the person who made the colocation is automaticlly the payer of it
                    'amount' => $amount_Per_Member,
                    'is_paid' => false,
                ]);
            }
        }

        }

            return response()->json(['message' => 'Expense added and split successfully!']);

    }
}
