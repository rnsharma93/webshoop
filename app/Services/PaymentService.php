<?php

namespace App\Services;

use App\Interfaces\PaymentProcessor;
use App\Models\Order;

class PaymentService
{
    private $processor;

    public function __construct(PaymentProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function ProceedToPay(Order $order)
    {
        return $this->processor->pay($order);
    }
}
