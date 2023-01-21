<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;

use App\Models\Customer;

class CustomerRepository
{
    /**
     * Import cutomers data
     * @param String $data => csv data from customer csv
     */
    public function importCustomers($data)
    {
        $row_count = 0;
        $not_imported = 0;
        $lines = explode(PHP_EOL, $data);
        foreach ($lines as $key => $line) {
            if($key>0) {
                try {
                    $row = str_getcsv($line);
                    $customer = new Customer;
                    $customer->job_title = $row[1];
                    $customer->email_address = $row[2];
                    $customer->name = $row[3];
                    $customer->registered_since = $row[4];
                    $customer->phone = $row[5];
                    $customer->save();
                    $row_count++;
                } catch(\Exception $e) {
                    Log::error("Customer Not imported, data = $line, error = ".$e->getMessage());
                    $not_imported++;
                }

            }
        }

        $response = "Total customers imported = $row_count .";
        if($not_imported) {
            $response .= "Not imported = $not_imported. Check logs for more details";
        }
        return $response;
    }
}
