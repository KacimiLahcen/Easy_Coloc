<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'colocation_id', 
        'sender_id', 
        'receiver_id', 
        'amount', 
        'is_paid',
        ];


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }
}
