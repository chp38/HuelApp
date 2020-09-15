<?php


namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class AverageOrderValueService
{
    /**
     * Get the average order value of all the customers orders.
     *
     * @return mixed
     */
    public function customersAverageOrderValue()
    {
        $orders = Order::all();

        return $orders->avg('total_price');
    }

    /**
     * Get the average order value of all the orders for a given customer.
     *
     * @param int $id
     * @return mixed
     */
    public function customerAverageOrderValue(int $id)
    {
        $customer = Customer::find($id);

        $orders = $customer->orders;

        return $orders->avg('total_price');
    }

    /**
     * Get the average order value of all the orders for a given variant.
     *
     * @param int $id
     * @return mixed
     */
    public function variantAverageOrderValue(int $id)
    {
        $product = Product::find($id);

        $orders = $product->orders();

        return $orders->avg('total_price');
    }
}