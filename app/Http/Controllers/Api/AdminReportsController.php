<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminReportsController extends Controller
{
    public function summary(Request $request)
    {
        try {
            // Product Data Sample (replace with DB query in real app)
            $products = [
                ['id' => 1, 'name' => 'Switch Smart Collar', 'stock' => 50, 'category' => 'Collar'],
                ['id' => 2, 'name' => 'Go Explore', 'stock' => 40, 'category' => 'Tracker'],
                ['id' => 3, 'name' => 'Go Explore 2.0', 'stock' => 35, 'category' => 'Tracker'],
                ['id' => 4, 'name' => 'Go Health', 'stock' => 25, 'category' => 'Health Monitor'],
                ['id' => 5, 'name' => 'Go Health 2.0', 'stock' => 20, 'category' => 'Health Monitor'],
                ['id' => 6, 'name' => 'Switch Battery', 'stock' => 100, 'category' => 'Accessory'],
                ['id' => 7, 'name' => 'USB Cable', 'stock' => 200, 'category' => 'Accessory'],
                ['id' => 8, 'name' => 'Tote Bag', 'stock' => 75, 'category' => 'Merchandise'],
            ];

            // Stock stats from products
            $inStock = 0;
            $lowStock = 0;
            $outOfStock = 0;
            foreach ($products as $p) {
                if ($p['stock'] > 5) $inStock++;
                else if ($p['stock'] > 0) $lowStock++;
                else $outOfStock++;
            }

            // Payment methods dummy data
            $paymentMethods = [
                ['name' => 'Credit Card', 'value' => 210],
                ['name' => 'PayPal', 'value' => 80],
                ['name' => 'Cash on Delivery', 'value' => 45],
                ['name' => 'Bank Transfer', 'value' => 15],
            ];

            // Customer status dummy data
            $customerStatus = [
                ['name' => 'Active', 'value' => 230],
                ['name' => 'Inactive', 'value' => 50],
                ['name' => 'Pending Verification', 'value' => 20],
                ['name' => 'Banned', 'value' => 3],
            ];

            // Shipping status dummy data
            $shippingStatus = [
                ['name' => 'Delivered', 'value' => 150],
                ['name' => 'Pending', 'value' => 40],
                ['name' => 'In Transit', 'value' => 30],
                ['name' => 'Delayed', 'value' => 10],
                ['name' => 'Cancelled', 'value' => 5],
            ];

            // Date range filtering for delivery trends
            $startDate = $request->query('start_date', now()->subDays(13)->toDateString());
            $endDate = $request->query('end_date', now()->toDateString());
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $interval = $start->diff($end)->days;

            $deliveryTrend = [];
            for ($i = 0; $i <= $interval; $i++) {
                $date = (clone $start)->modify("+$i days")->format('Y-m-d');
                $deliveryTrend[] = [
                    'date' => $date,
                    'delivered' => rand(5, 20),
                    'pending' => rand(0, 10),
                ];
            }

            // Product sales and returns trend over last 6 months
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            $productSalesTrend = [];
            foreach ($months as $month) {
                $productSalesTrend[] = [
                    'month' => $month,
                    'sales' => rand(1000, 7000),
                    'returns' => rand(10, 100),
                ];
            }

            // Top 5 selling products (dummy unitsSold, using your product names)
            $topProducts = [
                ['name' => 'Switch Smart Collar', 'unitsSold' => 300],
                ['name' => 'Go Explore', 'unitsSold' => 220],
                ['name' => 'Go Explore 2.0', 'unitsSold' => 180],
                ['name' => 'Switch Battery', 'unitsSold' => 140],
                ['name' => 'USB Cable', 'unitsSold' => 120],
            ];

            // Product stock distribution for Pie Chart
            $productStock = [
                ['name' => 'In Stock', 'value' => $inStock],
                ['name' => 'Low Stock', 'value' => $lowStock],
                ['name' => 'Out of Stock', 'value' => $outOfStock],
            ];

            return response()->json([
                'shippingStatus' => $shippingStatus,
                'deliveryTrend' => $deliveryTrend,
                'customerStatus' => $customerStatus,
                'productStock' => $productStock,
                'productSalesTrend' => $productSalesTrend,
                'topProducts' => $topProducts,
                'paymentMethods' => $paymentMethods,
            ]);
        } catch (\Throwable $e) {
            \Log::error("AdminReportsController@summary error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to load report data'], 500);
        }
    }
}
