<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');

    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }



    
    protected $fillable = [
        'title',
          'amount',
          'date',
           'payer_id',
        'colocation_id',
        'category_id',
             ];

}

