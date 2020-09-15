<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends Repository
{
    /**
     * ProductRepository constructor.
     * @param Product $model
     */
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}