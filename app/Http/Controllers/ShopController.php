<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at','DESC')->paginate(12);
        return view('site.shop',compact('products'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug',$product_slug)->first();
        $related_products = Product::where('slug','<>',$product_slug)->get()->take(8);
        return view('site.product-details',compact('product','related_products'));
    }

} #End Of ShopController
