<?php

namespace App\Repositories\Shopify;

class ShopifyProductRepository extends ShopifyRepository
{
    /**
     * Get the products from the Shopify API
     *
     * @param array $options
     * @return array
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getProducts(array $options = [])
    {
        return $this->get("products.json", $options);
    }
}