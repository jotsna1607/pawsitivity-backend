<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function codOrder(Request $request)
    {
        $orderId = DB::table('orders')->insertGetId([
            'customer_id' => $request->customer_id,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            'razorpay_order_id' => null,
            'razorpay_payment_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'order_id' => $orderId]);
    }

    public function createRazorpayOrder(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $razorpayOrder = $api->order->create([
            'receipt' => uniqid(),
            'amount' => $request->total_amount * 100, // in paise
            'currency' => 'INR'
        ]);

        // Save in DB with pending status
        DB::table('orders')->insert([
            'customer_id' => $request->customer_id,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            'razorpay_order_id' => $razorpayOrder['id'],
            'razorpay_payment_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount' => $request->total_amount,
            'currency' => 'INR',
            'key' => env('RAZORPAY_KEY') // backend key
        ]);
    }

    public function verifyRazorpayPayment(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Update DB
            DB::table('orders')
                ->where('razorpay_order_id', $request->razorpay_order_id)
                ->update([
                    'status' => 'completed',
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'updated_at' => now()
                ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
