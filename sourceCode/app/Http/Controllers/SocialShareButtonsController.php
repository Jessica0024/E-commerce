<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Share;

class SocialShareButtonsController extends Controller
{
    public function share()
    {
        // Assuming you have product data available
        $product = [
            'id' => 1,
            'name' => 'Sample Product',
            'category' => 'Sample Category',
            'price' => 100,
            'image' => 'sample.jpg',
        ];
    
        // Generating social share buttons
        $shareButtons = Share::page(url('/product/' . $product['id']), $product['name'])
            ->facebook()
            ->twitter()
            ->linkedin()
            ->telegram()
            ->whatsapp()
            ->reddit();
    
        // Returning the view with both product and shareButtons
        return view('product_details', compact('product', 'shareButtons'));
    }
    
}
