<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends Repository
{
    /**
     * OrderRepository constructor.
     * @param Order $model
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}