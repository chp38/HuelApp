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

        return [
            'average' => '£' . number_format($orders->avg('total_price'), 2),
            'totalOrders' => $orders->count(),
        ];
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

        return [
            'average' =>'£' . number_format($orders->avg('total_price'), 2),
            'customerName' => $customer->first_name . ' ' . $customer->last_name
        ];
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

        return [
            'average' => "£" . number_format($orders->avg('total_price'), 2),
            'variantName' => $product->title
        ];
    }
}