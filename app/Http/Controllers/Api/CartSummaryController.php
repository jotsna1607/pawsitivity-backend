<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartSummaryController extends Controller
{
    public function summary()
    {
        $items = DB::table('cart_items')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'itemCount' => $items->sum('quantity'),
            'total'     => $items->sum(fn($item) => $item->price * $item->quantity)
        ]);
    }
}
