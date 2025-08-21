<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AdminProductsController extends Controller
{
    public function index()
    {
        $products = Product::all()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category' => $p->category,
                'stock' => $p->stock,
                'price' => $p->sale_price ?? $p->regular_price,
                'total_sales' => rand(20, 300), // Demo data
                'rating' => rand(30, 50) / 10,  // Demo rating 3.0–5.0
                'image_url' => url('images/' . $p->image_url)
            ];
        });

        return response()->json(['products' => $products]);
    }

    public function stats()
    {
        return response()->json([
            'total_products' => Product::count(),
            'low_stock' => Product::where('stock', '<=', 5)->count(),
            'out_of_stock' => Product::where('stock', '=', 0)->count(),
            'total_units_sold' => rand(500, 2000) // Demo
        ]);
    }

    public function categoryDistribution()
    {
        $categories = Product::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get();

        return response()->json(['categories' => $categories]);
    }

    public function stockStatus()
    {
        $statuses = [
            ['status' => 'In Stock', 'value' => Product::where('stock', '>', 5)->count()],
            ['status' => 'Low Stock', 'value' => Product::where('stock', '>', 0)->where('stock', '<=', 5)->count()],
            ['status' => 'Out of Stock', 'value' => Product::where('stock', '=', 0)->count()],
        ];

        return response()->json(['statuses' => $statuses]);
    }

    public function salesTrend()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $trend = [];

        foreach ($months as $m) {
            $trend[] = [
                'month' => $m,
                'revenue' => rand(20000, 50000),
                'units' => rand(50, 200)
            ];
        }

        return response()->json(['trend' => $trend]);
    }
}
