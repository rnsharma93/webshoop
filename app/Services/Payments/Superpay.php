<?php

namespace App\Services\Payments;

use App\Interfaces\PaymentProcessor;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Http;


class Superpay implements PaymentProcessor
{
    protected $payment_url = "https://superpay.view.agentur-loop.com/pay";
    //protected $orderRepository;

    public function pay($order)
    {
        $data = ["order_id" => $order->id,
                 "customer_email" => $order->customer->email_address,
                 "value" => $order->orderAmount()
                ];

        $response = Http::withBody(json_encode($data), "application/json")->post($this->payment_url);
        $data = $response->json();
        if($response->ok()) {
            if($data['message'] == "Insufficient Funds") {
                $status = 0;
                $message = "Not Paid";
            } else if($data['message'] == "Payment Successful") {
                $status = 1;
                $message = "Order paid successfully";

                //update order
                $orderRepository = new OrderRepository;
                $orderRepository->update($order->id, ["paid" => 1]);
            }

        } else {
            $status = 0;
            $message = "Not paid";
        }

        return ["status" => $status, "message" => $message, 'data' => $data ];
    }
}
