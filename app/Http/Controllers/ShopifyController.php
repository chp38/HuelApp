<?php

namespace App\Http\Controllers;

use App\Services\ShopifyService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShopifyController extends Controller
{
    /**
     * @var ShopifyService
     */
    private $service;

    /**
     * ShopifyController constructor.
     *
     * @param ShopifyService $service
     */
    public function __construct(ShopifyService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function createCustomer(Request $request)
    {
        $hmac = $request->server('HTTP_X_SHOPIFY_HMAC_SHA256');

        if ($this->verifyWebhook($hmac)) {
            $this->service->createCustomer($request->all());

            return response('OK', 200);
        }

        return response('Unauthorized', 401);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function createProduct(Request $request)
    {
        $hmac = $request->server('HTTP_X_SHOPIFY_HMAC_SHA256');

        if ($this->verifyWebhook($hmac)) {
            $this->service->createProduct($request->all());

            return response('OK', 200);
        }

        return response('Unauthorized', 401);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function createOrder(Request $request)
    {
        $hmac = $request->server('HTTP_X_SHOPIFY_HMAC_SHA256');

        if ($this->verifyWebhook($hmac)) {
            $this->service->createOrder($request->all());

            return response('OK', 200);
        }

        return response('Unauthorized', 401);
    }

    /**
     * @param $hmac
     * @return bool
     */
    private function verifyWebhook($hmac)
    {
        $data = file_get_contents('php://input');

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, config('shopify.app_secret'), true));
        return hash_equals($hmac, $calculated_hmac);
    }
}
