<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Gate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class OrderController extends Controller
{
    // Display the checkout page with the products in the cart
    public function checkoutProduct()
    {
        $cart = session()->get('cart'); // Retrieve the products from the cart stored in the session
        return view('checkout');
    }

    // Create a new order
    public function createOrder(Request $request)
    {

    
        // Validate the form data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phoneNo' => ['required', 'regex:/^(01\d-\d{7}|011-\d{8})$/'],
            'address_1' => ['required', 'string'],
            'state' => ['required'],
            'zipcode' => ['required'],
            'payment_method' => ['required'],
        ]);
    
        // If address line 2 is not set, set it to an empty string
        $address_2 = $request->address_2 ?? '';
    
        // Retrieve the products from the cart stored in the session
        $cart = session()->get('cart');
    
        // Get the ID of the currently authenticated user
        $user_id = auth()->id();
    
        // For each product in the cart, create a new order
        foreach ($cart as $item => $id) {
            // Create a new order object
            $order = new Order();
    
            // Set the order details including user_id
            $order->user_id = $user_id; // Set the user_id here
            $order->name = $request->name;
            $order->email = $request->email;
            $order->phoneNo = $request->phoneNo;
            $order->address_1 = $request->address_1;
            $order->address_2 = $address_2;
            $order->state = $request->state;
            $order->zipcode = $request->zipcode;
            $order->payment_method = $request->payment_method;
    
            // Set the product details
            $order->product_image = $cart[$item]["image"];
            $order->product_name = $cart[$item]["name"];
            $order->product_quantity = $cart[$item]["quantity"];
            $order->product_price = $cart[$item]["price"];
            $order->total_price = $cart[$item]["quantity"] * $cart[$item]["price"]; // Calculate total price
    
            // Save the order to the database
            $order->save();
        }
    
        // Clear the cart stored in the session
        $request->session()->forget('cart');
    
        // Redirect to the thank you page with a success message
        return redirect('thankyou')->with('success', 'Order has been created!');
    }
    
    public function markAsCompleted(order $order)
    {
        $order->status = 'completed';
        $order->save();
    
        return redirect()->back()->with('success', 'Order marked as completed successfully.');
    }
    
    public function showOrder()
{
    if (Gate::allows('view-order-history')) {
        $orders = Order::orderBy('created_at', 'DESC')->paginate(10);
    } else {
        $user_id = auth()->id();
        $orders = Order::where('user_id', $user_id)->orderBy('created_at', 'DESC')->paginate(10);
    }

    return view('orderHistory', ['orders' => $orders]);
}

    

}
