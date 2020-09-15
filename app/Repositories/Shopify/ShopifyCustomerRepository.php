<?php

namespace App\Repositories\Shopify;

class ShopifyCustomerRepository extends ShopifyRepository
{
    /**
     * Get the customers from the Shopify API
     *
     * @param array $options
     * @return array
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getCustomers(array $options = [])
    {
        return $this->get("customers.json", $options);
    }
}