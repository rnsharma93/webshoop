<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LoopService
{
    protected $USERNAME, $PASSWORD;
    protected $product_csv = "https://backend-developer.view.agentur-loop.com/products.csv";
    protected $customer_csv = "https://backend-developer.view.agentur-loop.com/customers.csv";
    public $request;


    public function __construct()
    {
        $this->USERNAME = env('LOOP_USERNAME');
        $this->PASSWORD = env('LOOP_PASSWORD');

        //initiliaze basic authentication request with loop username and password
        $this->request = Http::withBasicAuth($this->USERNAME, $this->PASSWORD);
    }

    /**
     * get data from product or customer csv files from server
     * @param String $type = enum("product" , "customer")
     */
    public function getCSV($type = "product")
    {
        if($type == "product") {
            $url = $this->product_csv;
        } else if($type == "customer") {
            $url = $this->customer_csv;
        } else {
            return ["status" => 0, "data" => "only customer or product type available"];
        }

        $response = $this->request->get($url);

        //check if csv fetched successfully
        if($response->ok()) {
            return ["status" => 1, "data" =>$response->body()];
        } else {
            return ["status" => 0, "data" =>$response->body()];
        }
    }


}
