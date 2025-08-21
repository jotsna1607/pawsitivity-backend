<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminRefundsController extends Controller
{
    // Get refunds list with optional filtering and search
    public function index(Request $request)
    {
        try {
            $statusFilter = $request->query('status', 'all');
            $searchTerm = $request->query('search', null);

            $query = Refund::with(['order.customer']); // eager load related order & customer

            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $query->whereHas('order.customer', function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
                })->orWhereHas('order', function($q) use ($searchTerm) {
                    $q->where('id', $searchTerm);
                });
            }

            $refunds = $query->orderBy('created_at', 'desc')
                ->limit($request->query('pageSize', 1000))
                ->get();

            $data = $refunds->map(function ($refund) {
                return [
                    'id' => $refund->id,
                    'order' => [
                        'id' => $refund->order->id ?? null,
                        'amount' => $refund->order->total_amount ?? 0,
                    ],
                    'customer' => [
                        'name' => $refund->order->customer->name ?? 'N/A',
                    ],
                    'amount' => $refund->order->total_amount ?? 0, // Assuming full refund
                    'status' => $refund->status,
                    'reason' => $refund->reason ?? null,
                    'evidence' => $refund->evidence ?? [], // array of URLs if any
                    'created_at' => $refund->created_at->toDateTimeString(),
                    'updated_at' => $refund->updated_at->toDateTimeString(),
                ];
            });

            return response()->json($data);

        } catch (\Throwable $e) {
            Log::error("Error fetching refunds: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch refunds'], 500);
        }
    }

    // Get refund statistics summary
    public function stats()
    {
        try {
            $totalRequests = Refund::count();
            $pending = Refund::where('status', 'pending')->count();
            $approved = Refund::where('status', 'approved')->count();
            $rejected = Refund::where('status', 'rejected')->count();
            $totalRefunded = Refund::where('status', 'approved')->join('orders', 'refunds.order_id', '=', 'orders.id')
                                ->sum('orders.total_amount');

            // Average processing time in hours (approved or rejected)
            $avgProcessingHours = Refund::whereIn('status', ['approved', 'rejected'])
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours'))
                ->value('avg_hours');

            return response()->json([
                'totalRequests' => $totalRequests,
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'totalRefunded' => round($totalRefunded, 2),
                'avgProcessingHours' => $avgProcessingHours ? round($avgProcessingHours, 2) : 0,
            ]);
        } catch (\Throwable $e) {
            Log::error("Error fetching refund stats: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch refund stats'], 500);
        }
    }

    // Refund requests trend for charting
    public function refundTrend()
    {
        try {
            $data = Refund::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM((SELECT total_amount FROM orders WHERE orders.id = refunds.order_id)) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error("Error fetching refund trend data: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch refund trend data'], 500);
        }
    }

    // Update refund status with optional admin note
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        try {
            $refund = Refund::findOrFail($id);
            $refund->status = $request->input('status');
            $refund->admin_note = $request->input('admin_note', null);
            $refund->save();

            return response()->json(['message' => 'Refund updated', 'refund' => $refund]);
        } catch (\Throwable $e) {
            Log::error("Error updating refund: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update refund'], 500);
        }
    }

    // Bulk update refund statuses
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        try {
            Refund::whereIn('id', $request->ids)
                ->update(['status' => $request->status]);

            return response()->json(['message' => 'Bulk update successful']);
        } catch (\Throwable $e) {
            Log::error("Error in bulk refund update: " . $e->getMessage());
            return response()->json(['error' => 'Failed bulk update'], 500);
        }
    }


    // New method: Status distribution for Pie Chart
    public function statusDistribution()
    {
        try {
            $distribution = Refund::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();

            return response()->json($distribution);
        } catch (\Throwable $e) {
            Log::error("Error fetching status distribution: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch status distribution'], 500);
        }
    }

    // New method: Top 5 customers by refund count for Bar Chart
    public function topCustomers()
    {
        try {
            $topCustomers = Refund::join('orders', 'refunds.order_id', '=', 'orders.id')
                ->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->select('customers.name as customer_name', DB::raw('COUNT(refunds.id) as count'))
                ->groupBy('customers.id', 'customers.name')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            return response()->json($topCustomers);
        } catch (\Throwable $e) {
            Log::error("Error fetching top customers: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch top customers'], 500);
        }
    }
}
