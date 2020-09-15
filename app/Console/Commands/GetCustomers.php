<?php

namespace App\Console\Commands;

use App\Services\ShopifyService;
use Illuminate\Console\Command;

class GetCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:get:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the customers from Shopify';

    /**
     * @var ShopifyService
     */
    protected $service;

    /**
     * Create a new command instance.
     *
     * @param ShopifyService $service
     */
    public function __construct(ShopifyService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle()
    {
        $this->service->getCustomers();
        return 0;
    }
}