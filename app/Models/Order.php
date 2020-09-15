<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'ext_id',
        'customer_id',
        'currency',
        'total_price',
        'ordered_at'
    ];

    /**
     * The Products that belongs to the order
     */
    public function product()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    /**
     * Get the customer that made the order.
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}
