<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Repositories\OrderRepository;
use App\Services\PaymentService;
use App\Services\Payments\Superpay;

use App\Models\Order;

class OrderController extends Controller
{
    protected $orderRepository;
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    /**
     * Get all orders for customers
     */
    public function index(Request $request)
    {
        $customer_id = Auth::id();

        //get all order with pagination from order repository
        $orders = $this->orderRepository->orders($customer_id);

        return response()->json(['status'=>1 ,'data' => $orders]);
    }

    /**
     * Get single order
     */
    public function get(Request $request, $order_id)
    {
        $customer_id = Auth::id();

        $order = $this->orderRepository->order($order_id, $customer_id);

        return response()->json(["status" => 1, 'data' => $order]);

    }

    /**
     * Create an order
     */
    public function store(Request $request)
    {
        $customer_id = Auth::id();
        $order = $this->orderRepository->add($customer_id);

        return $order;
    }

    /**
     * delete an order
     */
    public function destroy(Request $request, Order $order)
    {
        $this->authorize('updateOrder', $order);

        $data = $this->orderRepository->delete($order->id);

        $status = 200;

        if($data['status'] == 0) {
            $status = 404;
        }

        return response()->json($data, $status);
    }

    /**
     * Add product to order
     */
    public function addProduct(Request $request, Order $order)
    {
        //authorization using policy
        $this->authorize('updateOrder', $order);

        //validation
        $this->validate($request, ["product_id" => 'required|exists:products,id']);

        $status = 200;

        //add product
        if($order->paid) {
            $status = 400;
            $data = ["status" => 0, "message" => "Order already paid, Can't add product"];
        } else {
            $this->orderRepository->addProduct($order->id, $request->get('product_id'));
            $data = ["status" => 1, "message" => "Product addedd successfully", "order" => $this->orderRepository->order($order->id)];
        }

        return response()->json($data, $status);

    }

    /**
     * Delete product from order
     */
    public function deleteProduct(Request $request, Order $order, $product_id)
    {
        //authorization using policy
        $this->authorize("updateOrder", $order);

        //validation
        //$this->validate($request, ["product_id" => 'required|exists:products,id']);

        $status = 200;

        //delete product
        if($order->paid) {
            $status = 400;
            $data = ["status" => 0, "message" => "Order already paid, Can't update order"];
        } else {
            $data = $this->orderRepository->deleteProduct($order->id, $product_id);
            $data["order"] = $this->orderRepository->order($order->id);
        }

        return response()->json($data, $status);
    }

    /**
     * Update Payment Method
     */
    public function paymentMethod(Request $request, Order $order)
    {
        //authorization using policy
        $this->authorize("updateOrder", $order);

        $this->validate($request , ['payment_code' => 'required' ]);

        $payment_method = $request->get('payment_code');

        $class = "\\App\\Services\\Payments\\".ucfirst($payment_method);
        if(!class_exists($class)) {
            return response()->json(["status" => 0, "message" => "Payment method not available"]);
        }

        $data['payment_method'] = $payment_method;
        $order = $this->orderRepository->update($order->id, $data);

        return response()->json(['status' => 1, 'message' => "Payment method updated"]);

    }

    /**
     * Order payment
     */
    public function pay(Request $request, Order $order)
    {
        if ($order->paid) {
            $data = ["status" => 0, "message" => "Order already paid"];
            return response()->json($data);
        }
        $payment_method = '\\App\\Services\\Payments\\'.ucfirst($order->payment_method);
        $payment = new PaymentService(new $payment_method);
        //return $order->customer->email;
        $response = $payment->ProceedToPay($order);

        return response()->json($response);
    }
}
