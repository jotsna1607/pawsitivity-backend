<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Refund;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function notifications()
    {
        $lowStockProducts = Product::where('stock', '<=', 5)->get(['id', 'name', 'stock']);
        $pendingRefundsCount = Refund::where('status', 'pending')->count();

        $notifications = [];

        foreach ($lowStockProducts as $product) {
            $notifications[] = [
                'id' => 'lowstock-' . $product->id,
                'type' => 'warning',
                'message' => "Product \"{$product->name}\" stock is low ({$product->stock}).",
            ];
        }

        if ($pendingRefundsCount > 0) {
            $notifications[] = [
                'id' => 'refund-pending',
                'type' => 'info',
                'message' => "There are {$pendingRefundsCount} pending refund request(s).",
            ];
        }

        return response()->json($notifications);
    }

    public function summary()
    {
        return response()->json([
            'totalProducts' => Product::count(),
            'lowStockItems' => Product::where('stock', '<=', 5)->count(),
            'totalSales' => Order::where('created_at', '>=', Carbon::now()->subYear())->sum('total_amount'),
            'pendingRefunds' => Refund::where('status', 'pending')->count(),
        ]);
    }

    public function salesData()
    {
        $sales = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(total_amount) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subYear())
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return response()->json($sales);
    }

    public function productList(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search', '');

        $query = Product::query();

        if ($search) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
        }

        $products = $query->orderBy('name')->paginate($perPage);

        return response()->json($products);
    }

    public function orderList(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search', '');

        $query = Order::with('customer');

        if ($search) {
            $query->where('id', 'like', "%$search%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($orders);
    }
}
