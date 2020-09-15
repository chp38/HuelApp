<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'ext_id',
        'price',
        'sku',
        'product_id'
    ];

    /**
     * Get the orders this product is used in
     */
    public function orders()
    {
        return $this->belongsToMany('App\Models\Order');
    }
}