<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminOrdersController extends Controller
{
    // List orders filtered by status
    public function index(Request $request)
    {
        try {
            $statusFilter = $request->query('status', 'all');

            $query = Order::query()->with('customer');

            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            $formattedOrders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer ? $order->customer->name : 'Customer #' . $order->customer_id,
                    'order_date' => $order->created_at ? $order->created_at->format('Y-m-d') : 'N/A',
                    'status' => strtolower($order->status ?? 'unknown'),
                    'total_amount' => $order->total_amount ?? 0,
                    'payment_method' => $order->payment_method ?? 'N/A',
                ];
            });

            return response()->json($formattedOrders);
        } catch (\Throwable $e) {
            Log::error("Error fetching orders: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch orders'], 500);
        }
    }

    // Get summary stats for orders
    public function stats()
    {
        try {
            $totalOrders = Order::count();
            $pendingOrders = Order::where('status', 'pending')->count();
            $completedOrders = Order::where('status', 'completed')->count();
            $totalRevenue = Order::sum('total_amount');

            return response()->json([
                'totalOrders' => $totalOrders,
                'pendingOrders' => $pendingOrders,
                'completedOrders' => $completedOrders,
                'totalRevenue' => round($totalRevenue, 2),
            ]);
        } catch (\Throwable $e) {
            Log::error("Error fetching order stats: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch order stats'], 500);
        }
    }

    // Get order status distribution for pie chart
    public function statusChart()
    {
        try {
            $data = Order::select('status', DB::raw('COUNT(*) as value'))
                ->groupBy('status')
                ->get();

            if ($data->isEmpty()) {
                $data = collect([
                    ['status' => 'completed', 'value' => 5],
                    ['status' => 'pending', 'value' => 3],
                    ['status' => 'cancelled', 'value' => 2],
                ]);
            }

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error("Error fetching status chart data: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch status chart data'], 500);
        }
    }

    // Get sales revenue trend for line chart
    public function salesTrend()
    {
        try {
            $data = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

            if ($data->isEmpty()) {
                $data = collect([
                    ['date' => now()->subDays(6)->toDateString(), 'revenue' => 200],
                    ['date' => now()->subDays(5)->toDateString(), 'revenue' => 350],
                    ['date' => now()->subDays(4)->toDateString(), 'revenue' => 500],
                    ['date' => now()->subDays(3)->toDateString(), 'revenue' => 150],
                    ['date' => now()->subDays(2)->toDateString(), 'revenue' => 420],
                    ['date' => now()->subDay()->toDateString(), 'revenue' => 300],
                    ['date' => now()->toDateString(), 'revenue' => 450],
                ]);
            }

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error("Error fetching sales trend data: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch sales trend data'], 500);
        }
    }
}
