<?php


namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class AverageOrderValueService
{
    /**
     * Get the average order value of all the customers orders.
     *
     * @return mixed
     */
    public function customersAverageOrderValue()
    {
        $lastOrder = Order::orderBy('ordered_at', 'desc')->first();
        $key = 'last_order' . $lastOrder->ordered_at;

        $orders = Order::all();
        $average = $this->getCachedAverage($key, $orders);

        return [
            'average' => '£' . number_format($average, 2),
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
        $lastOrder = Order::where('customer_id', $id)->orderBy('ordered_at', 'desc')->first();
        $key = 'customer_' . $id . 'last_order' . $lastOrder->ordered_at;

        $customer = Customer::find($id);

        $average = $this->getCachedAverage($key, $customer->orders);

        return [
            'average' =>'£' . number_format($average, 2),
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
        $orders = $product->orders;

        return [
            'average' => "£" . number_format($orders->avg('total_price'), 2),
            'variantName' => $product->title
        ];
    }

    /**
     * Attempt to get a cached average order value. If it's not set, calculate
     * it from the given orders, and save it.
     *
     * @param string $key
     * @param Collection $orders
     * @return mixed
     */
    private function getCachedAverage(string $key, Collection $orders)
    {
        if (Cache::has($key)) {
            $average = Cache::get($key);
        } else {
            $average = $orders->avg('total_price');
            Cache::forever($key, $orders->avg('total_price'));
        }

        return $average;
    }
}