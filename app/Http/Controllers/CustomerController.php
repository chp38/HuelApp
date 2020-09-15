<?php

namespace App\Http\Controllers;

use App\Services\AverageOrderValueService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $aovService;

    public function __construct(AverageOrderValueService $service)
    {
        $this->aovService = $service;
    }

    public function customersAverage()
    {
        return response()->json([
            'average' => $this->aovService->customersAverageOrderValue(),
        ]);
    }

    public function customerAverage($id)
    {
        return response()->json([
            'average' => $this->aovService->customerAverageOrderValue($id),
        ]);
    }

    public function variantAverage($id)
    {
        return response()->json([
            'average' => $this->aovService->variantAverageOrderValue($id),
        ]);
    }
}
