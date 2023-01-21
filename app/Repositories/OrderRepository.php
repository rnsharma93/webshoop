<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;

class OrderRepository
{

    /**
     * get orders for customer
     * @param Int $customer_id
     */
    public function orders($customer_id)
    {
        $orders = Order::with('order_products')->where('customer_id',$customer_id)->paginate(10);

        return $orders;
    }

    /**
     * get order with order_id
     * @param Int $order_id
     */
    public function order($order_id, $customer_id = 0)
    {
        $order = Order::with(['order_products','customer'])->where('id',$order_id);
        if($customer_id) {
            $order = $order->where('customer_id', $customer_id);
        }
        $order = $order->First();

        return $order;
    }

    /**
     * Create empty order for customer_id, return order_id
     * @param Int $customer_id
     * @return App\Models\Order $order
     */
    public function add($customer_id)
    {
        $order = new Order;
        $order->customer_id = $customer_id;
        $order->paid = 0;
        $order->total = 0;
        $order->save();

        return $order;
    }

    /**
     * Add product to order
     * @param int $order_id
     * @param int $product_id
     * @param int $quantity
     * @return App\Models\OrderProduct
     */
    public function addProduct($order_id, $product_id, $quantity = 1)
    {
        $product = Product::find($product_id);
        //check if product_id already exists for the order
        $orderProduct = OrderProduct::where(['order_id' => $order_id,
                                        'product_id' => $product_id
                                        ])->First();

        if(!empty($orderProduct)) {
            //update old entry
            $qty = $orderProduct->quantity + $quantity;
            $orderProduct->quantity = $qty;
            $orderProduct->price = $product->price;
            $orderProduct->product_name = $product->product_name;
            $orderProduct->total = ($qty * $product->price);
            $orderProduct->save();

            return $orderProduct;
        } else {
            //create new entry
            $order_product = new OrderProduct;
            $order_product->order_id = $order_id;
            $order_product->product_id = $product_id;
            $order_product->price = $product->price;
            $order_product->quantity = $quantity;
            $order_product->total = ($quantity * $product->price);
            $order_product->product_name = $product->product_name;
            $order_product->save();

            return $order_product;
        }
    }

    /**
     * Delete order
     */
    public function delete($order_id)
    {
        $order = Order::find($order_id);

        if(!empty($order)) {
            $order->delete();
            return ['status'=>1, "message" => 'order deleted'];
        } else {
            return ['status'=>0, "message" => 'order not found'];
        }
    }

    /**
     * Update order
     * @param Int $order_id
     * @param Array $data
     */
    public function update($order_id, $data)
    {
        $order = Order::where('id', $order_id)->update($data);

        return $order;
    }

    /**
     * Delete product from order
     * @param Int $order_id
     * @param Int $product_id
     */
    public function deleteProduct($order_id, $product_id)
    {
        $orderProduct = OrderProduct::where(['order_id' => $order_id,
                                        'product_id' => $product_id
                                        ]);

        if(!empty($orderProduct->get())) {
            $orderProduct->delete();
            return ["status" => 1 , "message" => "Product deleted from order"];
        } else {
            return ["status" => 0, "message" => "Product not found"];
        }

    }
}
