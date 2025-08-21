<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    // Create Razorpay order
    public function createOrder(Request $request)
    {
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $amount = $request->total * 100; // ✅ total from frontend in rupees, convert to paise

            $order = $api->order->create([
                'receipt' => uniqid(),
                'amount' => $amount,
                'currency' => 'INR',
            ]);

            // ✅ Return in format React expects
            return response()->json([
                'id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
            ]);
        } catch (\Exception $e) {
            Log::error("Razorpay Create Order Error: " . $e->getMessage());
            return response()->json(['error' => 'Order creation failed'], 500);
        }
    }

    // Verify Razorpay payment
    public function verifyPayment(Request $request)
    {
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);

            // ✅ Example save order
            $orderData = [
                'user_id' => Auth::id(),
                'total' => $request->total,
                'payment_method' => 'razorpay',
                'payment_id' => $request->razorpay_payment_id,
                'status' => 'paid',
            ];

            return response()->json(['success' => true, 'order' => $orderData]);
        } catch (\Exception $e) {
            Log::error("Razorpay Verify Error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Verification failed'], 500);
        }
    }

    // COD order
    public function codOrder(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

            $orderData = [
                'user_id' => $user->id,
                'total' => $request->total,
                'address_id' => $request->address_id, // ✅ match frontend
                'payment_method' => 'cod',
                'status' => 'pending',
            ];

            return response()->json([
                'success' => true,
                'message' => 'COD order placed successfully',
                'order' => $orderData,
            ]);
        } catch (\Exception $e) {
            Log::error('COD Order Error: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['error' => 'COD order failed'], 500);
        }
    }
}
