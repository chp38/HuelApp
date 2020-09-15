<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository extends Repository
{
    /**
     * CustomerRepository constructor.
     * @param Customer $model
     */
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }
}