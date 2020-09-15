<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Get the orders this product is used in
     */
    public function orders()
    {
        return $this->belongsToMany('App\Models\Order');
    }
}