<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Comment;
use App\Models\qnas;

use Illuminate\Support\Facades\File;

class ProductController extends Controller
{// In your ProductController.php

public function index(Request $request)
{
    $productsQuery = Product::query();
    
    // Check if price range parameters are present in the request
    if ($request->has('min_price') && $request->has('max_price')) {
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        
        // Filter products by price range
        $productsQuery->whereBetween('price', [$minPrice, $maxPrice]);
    }
    
    // Fetch FAQ data
    $products = $productsQuery->orderBy('id', 'DESC')->paginate(10);
    $qnas = qnas::all();

    // Return the view with the products data and FAQ data
    return view('products.show', ['products' => $products, 'qnas' => $qnas]);
}


    public function create()
    {
        // Return the view for creating a new product
        return view('products.create');
    }

   public function store(Request $request)
{
    // Validate the input data
    $validator = Validator::make($request->all(), [
        'image' => 'required|image:gif,png,jpeg,jpg',
        'name' => 'required',
        'style' => 'required',
        'price' => 'required|numeric',
        'category' => 'required',
    ]);

    // If validation fails, redirect back to the create page with errors and old input
    if ($validator->fails()) {
        return redirect()->route('products.create')->withErrors($validator)->withInput(); // return w errors
    } else {
        // Create a new product with the input data
        $product = Product::create($request->post());

        // Upload image
        if ($request->image) {
            $fileName = uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path() . '/uploads/products/', $fileName); // save img in a folder

            // Update the product image filename in the database
            $product->image = $fileName;
            $product->save();
        }

        // Redirect to the products index page with a success message
        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }
}

public function update(Product $product, Request $request)
{
    // Validate the input data
    $validator = Validator::make($request->all(), [
        'image' => 'image:gif,png,jpeg,jpg',
        'name' => 'required',
        'style' => 'required', // changed from 'category'
        'price' => 'required|numeric',
        'category' => 'required',
    ]);

    // If validation fails, redirect back to the edit page with errors and old input
    if ($validator->fails()) {
        return redirect()->route('products.edit', $product->id)->withErrors($validator)->withInput(); // return errors
    } else {
        // Update product fields except image
        $product->update($request->except('image'));

        // Upload image if provided
        if ($request->hasFile('image')) {
            $oldImage = $product->image;

            $fileName = uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/products'), $fileName); // Move new image to uploads folder

            // Update the product image filename in the database
            $product->image = $fileName;
            $product->save();

            // Delete old image if exists
            if ($oldImage && file_exists(public_path('uploads/products/' . $oldImage))) {
                File::delete(public_path('uploads/products/' . $oldImage));
            }
        }

        // Redirect to the products index page with a success message
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }
}

    public function edit(Product $product)
    {
        // Return the view for editing a product with the specified ID
        // $product = product::findOrFail($id);       
        return view('products.edit', ['product' => $product]);
    }

    
    
    public function destroy(Product $product, Request $request)
    {
        //$product = product::findOrFail($id);                
        File::delete(public_path() . '/uploads/products/' . $product->image);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    // User side function
    public function showByCategory(Request $request)
{
    // Get the search query, category filter, and style filter from the request
    $query = $request->input('query');
    $category = $request->input('category') ?? null;
    $style = $request->input('style') ?? null;
    $minPrice = $request->input('min_price')?? null;
    $maxPrice = $request->input('max_price')?? null;
    
    // Start building the query to fetch products
    $productsQuery = Product::query();
    
    // Apply the search query to the product query
    if ($query) {
        $productsQuery->where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%');
    }
    
    // Apply the price range filter to the product query
    if ($minPrice  && $maxPrice ) {
        $productsQuery->whereBetween('price', [$minPrice, $maxPrice]);
    }
    
    // Apply the category filter to the product query
    if ($category) {
        $productsQuery->where('category', $category);
    }

    // Apply the style filter to the product query
// Apply the style filter to the product query
if ($style) {
    $productsQuery->where('style', $style);
}
$styles = Product::distinct()->pluck('style');

    
    // Paginate the results
    $products = $productsQuery->paginate(9);
    
    // Get distinct categories to display in the sidebar
    $categories = Product::distinct()->pluck('category');
    
    // Return the view with the products, search query, categories, and selected category
    return view('home', [
        'products' => $products,
        'category' => $category,
        'style' => $style,
        'query' => $query,
        'categories' => $categories,
        'styles' => $styles, // Pass styles to the view
        'selectedCategory' => $category,
    ]);
}

    public function add(Request $request, Product $product)
    {
        // Add the product to the cart and redirect back to the previous page with a success message
        return redirect()->back()->with('success', 'Product added to cart.');
    }


    public function search(Request $request)
    {
        // Search for products based on a query string and paginate the results
        $query = $request->input('query');
        $products = Product::where('name', 'LIKE', "%{$query}%")->paginate(9);
        return view('search', compact('products', 'query'));
    }



public function addComment(Request $request, $productId) {
    $request->validate([
        'comment' => 'required|string|max:255',
    ]);

    $comment = new Comment();
    $comment->user_id = auth()->check() ? auth()->user()->id : null;
    $comment->user_name = auth()->check() ? auth()->user()->name : 'Anonymous';
    $comment->product_id = $productId;
    $comment->comment = $request->comment;
    $comment->save();

    return redirect()->back()->with('success', 'Comment added successfully.');
}

    
public function show($id)
{
    $product = Product::find($id);
    
    if (!$product) {
        abort(404);
    }
    
    $comments = Comment::where('product_id', $id)->get();
    
    // Fetch FAQ data
    $qnas = qnas::all();
    
    return view('products.showDetail', compact('product', 'comments', 'qnas'));
}
}
    

