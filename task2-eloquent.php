<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        $orderData = [];
        
        $orderData = Order::with('cartItems')
        					->where('status', 'completed')
        					->orderByDesc('completed_at')
        					->get();
        					
        $orderData = $orderData->map(function($order) {
        	$itemsCount = $order->cartItems->count();
        	$totalAmount = $order->cartItems->reduce(function (int $carry, CartItem $cartItem) {
			    return $carry + ($cartItem->price * $cartItem->quantity);
			}, 0);
									
					
			$lastAddedToCart = $order->cartItems()->orderByDesc('created_at')->first()?->created_at;
			$completedOrderExists = $order->status === 'completed';
			
			return [
				'order_id' => $order->id,
                'customer_name' => $order->customer,
                'total_amount' => $totalAmount,
                'items_count' => $itemsCount,
                'last_added_to_cart' => $lastAddedToCart,
                'completed_order_exists' => $completedOrderExists,
                'created_at' => $order->created_at,
			];
        });
        
        return view('orders.index', ['orders' => $orderData]);
    }
}

