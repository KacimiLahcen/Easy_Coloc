<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Show all users for the admin to manage
    public function users() {
        if (auth()->user()->role !== 'admin') abort(403);
        
        $users = User::where('id', '!=', auth()->id())->get();
        return view('admin.users', compact('users'));
    }

    // Toggle ban/unban status
    public function toggleBan(User $user) {
        if (auth()->user()->role !== 'admin') abort(403);

        $user->is_banned = !$user->is_banned;
        $user->save();

        return back()->with('success', $user->is_banned ? 'User banned.' : 'User unbanned.');
    }
}