<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminShippingController extends Controller
{
    // List shipments with optional status filter
    public function index(Request $request)
    {
        try {
            $status = $request->query('status', 'all');

            $query = Shipping::with('order', 'customer'); // eager load relations if exist

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            $shippings = $query->orderBy('created_at', 'desc')->get();

            $formatted = $shippings->map(function ($ship) {
                return [
                    'id' => $ship->id,
                    'order_id' => $ship->order_id,
                    'customer_name' => $ship->customer ? $ship->customer->name : 'Customer #' . $ship->customer_id,
                    'shipping_address' => $ship->shipping_address,
                    'status' => strtolower(str_replace(' ', '_', $ship->status)),
                    'carrier' => $ship->carrier ?? 'N/A',
                    'tracking_number' => $ship->tracking_number ?? null,
                    'estimated_delivery_date' => $ship->estimated_delivery_date ? $ship->estimated_delivery_date->format('Y-m-d') : null,
                    'delivery_date' => $ship->delivery_date ? $ship->delivery_date->format('Y-m-d') : null,
                    'shipping_cost' => $ship->shipping_cost ?? 0,
                ];
            });

            return response()->json($formatted);

        } catch (\Throwable $e) {
            Log::error("Error fetching shipments: ".$e->getMessage());
            return response()->json(['error' => 'Failed to fetch shipments'], 500);
        }
    }

    // Get shipment stats summary
    public function stats()
    {
        try {
            $totalShipments = Shipping::count();
            $pendingShipments = Shipping::where('status', 'pending')->count();
            $deliveredShipments = Shipping::where('status', 'delivered')->count();
            $delayedShipments = Shipping::where('status', 'delayed')->count();
            $totalShippingCost = Shipping::sum('shipping_cost');

            return response()->json([
                'totalShipments' => $totalShipments,
                'pendingShipments' => $pendingShipments,
                'deliveredShipments' => $deliveredShipments,
                'delayedShipments' => $delayedShipments,
                'totalShippingCost' => round($totalShippingCost, 2),
            ]);
        } catch (\Throwable $e) {
            Log::error("Error fetching shipping stats: ".$e->getMessage());
            return response()->json(['error' => 'Failed to fetch shipping stats'], 500);
        }
    }

    // Shipment status distribution for pie chart
    public function statusChart()
    {
        try {
            $data = Shipping::select('status', DB::raw('COUNT(*) as value'))
                ->groupBy('status')
                ->get();

            if ($data->isEmpty()) {
                // fallback dummy data
                $data = collect([
                    ['status' => 'delivered', 'value' => 10],
                    ['status' => 'pending', 'value' => 5],
                    ['status' => 'in_transit', 'value' => 4],
                    ['status' => 'delayed', 'value' => 1],
                ]);
            }

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error("Error fetching shipping status chart data: ".$e->getMessage());
            return response()->json(['error' => 'Failed to fetch shipping status chart'], 500);
        }
    }

    // Delivery trend data for line chart (last 30 days)
    public function deliveryTrend()
    {
        try {
            $data = Shipping::select(
                DB::raw('DATE(delivery_date) as date'),
                DB::raw('COUNT(*) as delivered_count')
            )
            ->where('status', 'delivered')
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '>=', now()->subDays(30)->toDateString())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

            if ($data->isEmpty()) {
                $data = collect([
                    ['date' => now()->subDays(6)->toDateString(), 'delivered_count' => 2],
                    ['date' => now()->subDays(5)->toDateString(), 'delivered_count' => 4],
                    ['date' => now()->subDays(4)->toDateString(), 'delivered_count' => 3],
                    ['date' => now()->subDays(3)->toDateString(), 'delivered_count' => 1],
                    ['date' => now()->subDays(2)->toDateString(), 'delivered_count' => 5],
                    ['date' => now()->subDay()->toDateString(), 'delivered_count' => 2],
                    ['date' => now()->toDateString(), 'delivered_count' => 3],
                ]);
            }

            return response()->json($data);

        } catch (\Throwable $e) {
            Log::error("Error fetching delivery trend data: ".$e->getMessage());
            return response()->json(['error' => 'Failed to fetch delivery trend data'], 500);
        }
    }
}
