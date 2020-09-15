<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\Shopify\ShopifyCustomerRepository;
use App\Repositories\Shopify\ShopifyOrderRepository;
use App\Repositories\Shopify\ShopifyProductRepository;

class ShopifyService
{
    /**
     * @var ShopifyCustomerRepository
     */
    private $shopifyCustomers;

    /**
     * @var ShopifyProductRepository
     */
    private $shopifyProducts;

    /**
     * @var ShopifyOrderRepository
     */
    private $shopifyOrders;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * ShopifyService constructor.
     *
     * @param ShopifyCustomerRepository $shopifyCustomers
     * @param ShopifyProductRepository $shopifyProducts
     * @param ShopifyOrderRepository $shopifyOrders
     * @param CustomerRepository $customerRepository
     * @param ProductRepository $productRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        ShopifyCustomerRepository $shopifyCustomers,
        ShopifyProductRepository $shopifyProducts,
        ShopifyOrderRepository $shopifyOrders,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository
    )
    {
        $this->shopifyCustomers = $shopifyCustomers;
        $this->shopifyProducts = $shopifyProducts;
        $this->shopifyOrders = $shopifyOrders;
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
        $customers = $this->shopifyCustomers->getCustomers([
            'limit' => 250,
            'page_info' => $pageInfo
        ]);

        foreach ($customers['customers'] as $customer) {
            $this->createCustomer($customer);
        }

        $links = $this->shopifyCustomers->getNextLink();

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
        $orders = $this->shopifyOrders->getOrders([
            'limit' => 250,
            'fields' => 'id, total_price, customer, currency, line_items, created_at',
            'page_info' => $pageInfo
        ]);

        foreach ($orders['orders'] as $order) {

            if (!array_key_exists('customer', $order)) {
                continue;
            }

            $this->createOrder($order);
        }

        $links = $this->shopifyOrders->getNextLink();

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
        $products = $this->shopifyProducts->getProducts([
            'page_info' => $pageInfo,
            'limit' => 250
        ]);

        foreach ($products['products'] as $product) {
            $newProductId = $this->createProduct($product);

            foreach ($product["variants"] as $variant) {
                $this->createProduct($variant, $newProductId);
            }
        }

        $links = $this->shopifyProducts->getNextLink();

        if (isset($links['next'])){
            $this->getProducts($links['next']);
        }
    }

    /**
     * Create single customer
     *
     * @param array $customer
     * @return mixed
     */
    public function createCustomer(array $customer)
    {
        $newCustomer = [
            'ext_id' => $customer['id'],
            'first_name' => $customer['first_name'],
            'last_name' => $customer['last_name']
        ];

        return $this->customerRepository->createIfNotExists($newCustomer, 'ext_id');
    }

    /**
     * Create single product
     *
     * @param array $product
     * @param null $productId
     * @return mixed
     */
    public function createProduct(array $product, $productId = null)
    {
        $newProduct = [
            'ext_id' => $product['id'],
            'title' => $product['title'],
            'sku' => $product['sku'] ?? null,
            'price' => $product['price'] ?? null,
            'product_id' => $productId
        ];

        return $this->productRepository->createIfNotExists($newProduct, 'ext_id');
    }

    /**
     * Create single order
     *
     * @param array $order
     * @return mixed
     */
    public function createOrder(array $order)
    {
        if (!$this->orderRepository->findBy('ext_id', $order['id'])) {
            $createdAt = $order['created_at'];

            $newOrder = [
                'ext_id' => $order['id'],
                'customer_id' => $this->customerRepository->findBy('ext_id', $order['customer']['id'])->id,
                'total_price' => $order['total_price'],
                'currency' => $order['currency'],
                'ordered_at' => date("Y-m-d H:i:s", strtotime($createdAt)),
            ];

            $newOrderId = $this->orderRepository->createIfNotExists($newOrder, 'ext_id');

            foreach ($order['line_items'] as $lineItem) {
                if (isset($lineItem['product_id'])) {
                    $orderProduct = new OrderProduct();
                    $orderProduct->order_id = $newOrderId;
                    $orderProduct->product_id = $this->productRepository->findBy('ext_id', $lineItem['variant_id'])->id;
                    $orderProduct->save();
                }
            }
        }

        return $newOrderId;
    }
}