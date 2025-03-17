<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'card_items';
    protected $fillable = [
        'user_id',
        'product_id',
        'session_id',
        'quantity'
    ];

}
