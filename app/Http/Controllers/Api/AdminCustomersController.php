<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminCustomersController extends Controller
{
    // List customers with optional filters, pagination
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            // Filter by search term (name or email)
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status') && $request->input('status') !== 'all') {
                $query->where('status', $request->input('status'));
            }

            // Eager load orders count and sum total_amount
            $query->withCount('orders')
                  ->withSum('orders', 'total_amount');

            // Pagination parameters
            $pageSize = $request->input('pageSize', 10);

            $customers = $query->orderBy('created_at', 'desc')->paginate($pageSize);

            return response()->json($customers);
        } catch (Exception $e) {
            \Log::error('Error fetching customers list: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function stats()
    {
        try {
            $totalCustomers = Customer::count();
            $newThisMonth = Customer::whereYear('created_at', now()->year)
                                   ->whereMonth('created_at', now()->month)
                                   ->count();
            $active = Customer::where('status', 'active')->count();
            $inactive = Customer::where('status', 'inactive')->count();
            $banned = Customer::where('status', 'banned')->count();

            return response()->json([
                'totalCustomers' => $totalCustomers,
                'newThisMonth' => $newThisMonth,
                'active' => $active,
                'inactive' => $inactive,
                'banned' => $banned,
            ]);
        } catch (Exception $e) {
            \Log::error('Error in stats(): ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function statusDistribution()
    {
        try {
            $distribution = Customer::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();

            return response()->json($distribution);
        } catch (Exception $e) {
            \Log::error('Error in statusDistribution(): ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function growth()
    {
        try {
            $growth = Customer::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

            return response()->json($growth);
        } catch (Exception $e) {
            \Log::error('Error in growth(): ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    // New method: Top 5 customers by total spend
    public function topSpenders()
    {
        try {
            $topSpenders = Customer::select(
                    'customers.id',
                    'customers.name as customer_name',
                    DB::raw('SUM(orders.total_amount) as total_spent')
                )
                ->join('orders', 'customers.id', '=', 'orders.customer_id')
                ->groupBy('customers.id', 'customers.name')
                ->orderByDesc('total_spent')
                ->limit(5)
                ->get();

            return response()->json($topSpenders);
        } catch (Exception $e) {
            \Log::error('Error in topSpenders(): ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
