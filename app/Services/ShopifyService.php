<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Repositories\Shopify\ShopifyCustomerRepository;
use App\Repositories\Shopify\ShopifyOrderRepository;
use App\Repositories\Shopify\ShopifyProductRepository;

class ShopifyService
{
    /**
     * @var ShopifyCustomerRepository
     */
    private $customerRepository;

    /**
     * @var ShopifyProductRepository
     */
    private $productRepository;

    /**
     * @var ShopifyOrderRepository
     */
    private $orderRepository;

    /**
     * ShopifyService constructor.
     *
     * @param ShopifyCustomerRepository $customerRepository
     * @param ShopifyProductRepository $productRepository
     * @param ShopifyOrderRepository $orderRepository
     */
    public function __construct(
        ShopifyCustomerRepository $customerRepository,
        ShopifyProductRepository $productRepository,
        ShopifyOrderRepository $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get the customers from Shopify
     *
     * @param string $pageInfo
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getCustomers(string $pageInfo = '')
    {
        $customers = $this->customerRepository->getCustomers([
            'limit' => 250,
            'page_info' => $pageInfo
        ]);

        foreach ($customers['customers'] as $customer) {
            $newCustomer = new Customer();

            $newCustomer->ext_id = $customer['id'];
            $newCustomer->first_name = $customer['first_name'];
            $newCustomer->last_name = $customer['last_name'];

            $newCustomer->save();
        }

        $links = $this->orderRepository->getNextLink();

        if (isset($links['next'])){
            $this->getCustomers($links['next']);
        }
    }

    /**
     * Get the orders from Shopify
     *
     * @param string $pageInfo
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getOrders(string $pageInfo = '')
    {
        $orders = $this->orderRepository->getOrders([
            'limit' => 250,
            'fields' => 'id, total_price, customer, currency, line_items',
            'page_info' => $pageInfo
        ]);

        foreach ($orders['orders'] as $order) {
            $newOrder = new Order();

            // Needs to have a customer
            if (!array_key_exists('customer', $order)) {
                continue;
            }

            $newOrder->ext_id = $order['id'];
            $newOrder->customer_id = Customer::where('ext_id', $order['customer']['id'])->first()->id;
            $newOrder->total_price = $order['total_price'];
            $newOrder->currency = $order['currency'];
            $newOrder->save();

            foreach ($order['line_items'] as $lineItem) {
                if (isset($lineItem['product_id'])) {
                    $orderProduct = new OrderProduct();
                    $orderProduct->order_id = $newOrder->id;
                    $orderProduct->product_id = Product::where('ext_id', $lineItem['product_id'])->first()->id;
                    $orderProduct->save();
                }
            }
        }

        $links = $this->orderRepository->getNextLink();

        if (isset($links['next'])){
            $this->getOrders($links['next']);
        }
    }

    /**
     * Create the products from Shopify. Each product is setup as a parent
     * product, with the variants being child products
     *
     * @param string $pageInfo
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getProducts(string $pageInfo = '')
    {
        $products = $this->productRepository->getProducts([
            'page_info' => $pageInfo,
            'limit' => 250
        ]);

        foreach ($products['products'] as $product) {
            $newProduct = new Product();

            $newProduct->ext_id = $product['id'];
            $newProduct->title = $product['title'];
            $newProduct->save();

            foreach ($product["variants"] as $variant) {
                $newVariant = new Product();

                $newVariant->ext_id = $variant['id'];
                $newVariant->title = $variant['title'];
                $newVariant->sku = $variant['sku'];
                $newVariant->ext_id = $variant['id'];
                $newVariant->price = $variant['price'];
                $newVariant->product_id = $newProduct->id;

                $newVariant->save();
            }
        }

        $links = $this->orderRepository->getNextLink();

        if (isset($links['next'])){
            $this->getProducts($links['next']);
        }
    }
}