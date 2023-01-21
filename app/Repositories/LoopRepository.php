<?php

namespace App\Repositories;

use App\Services\LoopService;

use App\Repositories\ProductRepository;
use App\Repositories\CustomerRepository;

use Illuminate\Support\Facades\Log;

class LoopRepository
{
    protected $loopService, $productRepository, $customerRepository;

    public function __construct(LoopService $loopService,
                                ProductRepository $productRepository,
                                CustomerRepository $customerRepository
                                )
    {
        $this->loopService = $loopService;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Import products or customers data from csv
     * @param String $type = enum('product','customer')
     * @return String
     */
    public function importsData($type)
    {
        $data = $this->loopService->getCSV($type);
        if($data['status'] == 0) {
            Log::error("Products CSV fetched error. ".$data['data']);
            return "Error importing data, ".$data['data'];
        }

        //import products
        if($type == "product") {
            return  $this->productRepository->importProducts($data['data']);
        } else if($type == "customer") {
            return $this->customerRepository->importCustomers($data['data']);
        } else {
            return "Undefined import type";
        }
    }

}
