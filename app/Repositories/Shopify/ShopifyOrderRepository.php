<?php

namespace App\Repositories\Shopify;

class ShopifyOrderRepository extends ShopifyRepository
{
    /**
     * Get the orders from the Shopify API
     *
     * @param array $options
     * @return array
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getOrders(array $options = []): array
    {
        return $this->get("orders.json", $options);
    }
}