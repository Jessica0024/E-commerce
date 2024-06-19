<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
// use App\Models\Order;

class WishlistController extends Controller
{

    /**
     * Display the shopping wishlist view
     *
     * @return \Illuminate\View\View
     */
// WishlistController.php

public function productwishlist()
{
    // 从会话中获取愿望清单数据
    $wishlist = session()->get('wishlist', []);

    // 获取愿望清单中产品的 ID
    $productIds = array_keys($wishlist);

    // 从数据库中检索产品数据
    $products = Product::whereIn('id', $productIds)->get();

    // 将愿望清单数据传递给视图
    return view('wishlist', ['products' => $products]);
}

    
    /**
     * Add a product to the wishlist
     *
     * @param int $id
     * @param Request $req
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addTowishlist($id, Request $request)
    {
        // 找到要添加到购物车的产品
        $product = Product::findOrFail($id);
    
        // 获取当前的购物车或初始化为空数组
        $wishlist = session()->get('wishlist', []);
    
        // 检查产品是否已经在购物车中，并更新其数量，或者将其添加到购物车中
        if (isset($wishlist[$id])) {
            $wishlist[$id]['quantity'] += $request->quantity;
        } else {
            $wishlist[$id] = [
                "image" => $product->image,
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $product->price,
            ];
        }
    
        // 将更新后的购物车保存到会话中
        session()->put('wishlist', $wishlist);
    
        // 返回到前一页，并携带一个成功消息
        return redirect()->back()->with('success', 'Product has been added to wishlist!');
    }
    
    /**
     * Update the quantity of a product in the wishlist
     *
     * @param Request $request
     * @return void
     */
    public function updatewishlist(Request $request)
    {
        if ($request->id && $request->quantity) {
            // Get the current wishlist
            $wishlist = session()->get('wishlist');

            // Update the quantity of the product with the given ID
            $wishlist[$request->id]["quantity"] = $request->quantity;

            // Save the updated wishlist to the session
            session()->put('wishlist', $wishlist);

            // Flash a success message
            session()->flash('success', 'Product added to wishlist.');
        }
    }

    /**
     * Remove a product from the wishlist
     *
     * @param Request $request
     * @return void
     */
    public function deletewishlist(Request $request)
    {
        if ($request->id) {
            // Get the current wishlist
            $wishlist = session()->get('wishlist');

            // Remove the product with the given ID from the wishlist
            if (isset($wishlist[$request->id])) {
                unset($wishlist[$request->id]);
                session()->put('wishlist', $wishlist);
            }

            // Flash a success message
            session()->flash('success', 'Product successfully deleted.');
        }
    }

}
