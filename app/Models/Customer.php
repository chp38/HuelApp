<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * Get the orders the user has made.
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
}