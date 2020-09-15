<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
        'customer_id'
    ];

    /**
     * The table to use for this model.
     *
     * @var string
     */
    protected $table = "order_product";
}
