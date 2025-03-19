<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payement extends Model
{
    protected $fillable = [
        "order_id",
        "payment_type",
        "status",
        "transaction_id",
    ];
}
