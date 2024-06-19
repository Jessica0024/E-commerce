<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Display the shopping cart view
     *
     * @return \Illuminate\View\View
     */
    public function productCart()
    {
        return view('cart');
    }
    
    /**
     * Add a product to the cart
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToCart($id, Request $request)
    {
        $product = Product::findOrFail($id);
        
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $request->quantity;
        } else {
            $cart[$id] = [
                "image" => $product->image,
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $product->price,
            ];
        }
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product has been added to cart!');
    }

    /**
     * Update the quantity of a product in the cart
     *
     * @param Request $request
     * @return void
     */
    public function updateCart(Request $request)
    {
        // Update cart item in session
        $cart = session()->get('cart', []);
        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        session()->flash('success', 'Cart updated successfully.');
    }

    /**
     * Delete a product from the cart
     *
     * @param Request $request
     * @return void
     */
    public function deleteCart(Request $request)
    {
        // Delete cart item from session
        $cart = session()->get('cart', []);
        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }

        session()->flash('success', 'Product successfully removed from cart.');
    }
}
