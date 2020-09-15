<?php

namespace App\Http\Controllers;

use App\Services\AverageOrderValueService;

class CustomerController extends Controller
{
    /**
     * @var AverageOrderValueService
     */
    private $aovService;

    /**
     * CustomerController constructor.
     *
     * @param AverageOrderValueService $service
     */
    public function __construct(AverageOrderValueService $service)
    {
        $this->aovService = $service;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function customersAverage()
    {
        return response()->json($this->aovService->customersAverageOrderValue());
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerAverage($id)
    {
        return response()->json($this->aovService->customerAverageOrderValue($id));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function variantAverage($id)
    {
        return response()->json($this->aovService->variantAverageOrderValue($id));
    }
}
