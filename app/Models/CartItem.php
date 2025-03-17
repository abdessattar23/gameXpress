<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $table = 'card_items';
    protected $fillable = [
        'user_id',
        'product_id',
        'session_id',
        'quantity'
    ];
    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
