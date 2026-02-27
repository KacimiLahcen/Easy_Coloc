<?php
namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ColocationController extends Controller
{
    // Create a new colocation
    public function store(Request $request) {
        $request->validate(['name' => 'required|string']);

        $colocation = Colocation::create([
            'name' => $request->name,
            'invite_token' => Str::random(10), // gen unique 10char token
            'created_by' => auth()->id(),
        ]);

        // Automaticlly add the creator as a member
        $colocation->members()->attach(auth()->id(), ['role' => 'owner']);

        return back()->with('success', 'Colocation created successfully!');
    }


    // Join a colocation using the invite token
    public function join(Request $request) {

        $request->validate(['token' => 'required | string']);

        $colocation = Colocation::where('invite_token', $request->token)->first();

        if (!$colocation) {
            return back()->with('error', 'votre invitation token expirée');
        }


        // Check if user is already in this colocation
        if ($colocation->members()->where('user_id', auth()->id())->exists()) {
            
            return back()->with('error', 'you are already a member.');
        }

        $colocation->members()->attach(auth()->id(), ['role' => 'member']);

        return back()->with('success', 'Joined ' . $colocation->name);
    }
}
