<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;

use App\Models\Product;

class ProductRepository
{
    /**
     * Import products data
     * @param String $data => csv data from product csv
     * @return String $response
     */
    public function importProducts($data)
    {
        $row_count = 0;
        $not_imported = 0;
        $lines = explode(PHP_EOL, $data);
        foreach ($lines as $key => $line) {
            if($key>0) {
                try {
                    $row = str_getcsv($line);
                    $product = new Product;
                    $product->product_name = $row[1];
                    $product->price = $row[2];
                    $product->save();
                    $row_count++;
                } catch(\Exception $e) {
                    Log::error("Product Not imported, data = $line, error = ".$e->getMessage());
                    $not_imported++;
                }
            }
        }
        $response = "Total products imported = $row_count .";
        if($not_imported) {
            $response .= "Not imported = $not_imported. Check logs for more details";
        }
        return $response;
    }
}
