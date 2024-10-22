<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('cartItems')
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->get();
                            
        $orders = $orders->map(function($order) {
            /**
             * We should add the relationship on the model. With that, it becomes easiear to read, and more efficient
             * since Laravel can pre load all the cart items of the orders.
             */
            $itemsCount = $order->cartItems->count();

            /**
             * We run one reduce here to make the sum of the price/quantity
             */
            $totalAmount = $order->cartItems->reduce(function (int $carry, CartItem $cartItem) {
                return $carry + ($cartItem->price * $cartItem->quantity);
            }, 0);
                                    
            /**
             * Again, use the model relationship to find the cartItems. By adding the () it returns a query builder,
             * and we can just add the necessary where clauses.
             */
            $lastAddedToCart = $order->cartItems()->orderByDesc('created_at')->first()?->created_at;
            $completedOrderExists = $order->status === 'completed';
            
            /**
             * Now we return the result of the map. The ordering was made before, by using Eloquent's methods 
             * to order and filter the relevant orders.
             */
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
        
        return view('orders.index', ['orders' => $orders]);
    }
}

