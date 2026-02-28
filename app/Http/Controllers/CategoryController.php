<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    
    public function store (Request $request) {
        $request->validate ([
            'name' => 'required | string ',
            'colocation_id' => 'required | exists:colocation,id'
        ]);

        Category::create([
            'name' => $request->name,
            'colocation_id' => $request->colocation_id
        ]);

        return back()->with('success', 'made successfully');
    }
}
