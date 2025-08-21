<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found.'], 404);
        }

        // Modify image_url to include full URL path
        $products->transform(function ($product) {
            $product->image_url = url('images/' . $product->image_url);
            return $product;
        });

        return response()->json(['products' => $products]);
    }
}
