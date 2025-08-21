<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = DB::table('addresses')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($addr) {
                $addr->isDefault = (bool) $addr->is_default;
                $addr->address = trim("{$addr->address_line1}, {$addr->address_line2}, {$addr->city}, {$addr->state} - {$addr->pincode}", ", ");
                return $addr;
            });

        return response()->json($addresses);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'addressLine1' => 'required|string|max:255',
            'addressLine2' => 'nullable|string|max:255',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'pincode'      => 'required|string|max:20',
            'isDefault'    => 'boolean'
        ]);

        if ($data['isDefault'] ?? false) {
            DB::table('addresses')
                ->where('user_id', Auth::id())
                ->update(['is_default' => 0]);
        }

        $id = DB::table('addresses')->insertGetId([
            'user_id'       => Auth::id(),
            'name'          => $data['name'],
            'address_line1' => $data['addressLine1'],
            'address_line2' => $data['addressLine2'],
            'city'          => $data['city'],
            'state'         => $data['state'],
            'pincode'       => $data['pincode'],
            'is_default'    => $data['isDefault'] ?? 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json(DB::table('addresses')->where('id', $id)->first(), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'addressLine1' => 'required|string|max:255',
            'addressLine2' => 'nullable|string|max:255',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'pincode'      => 'required|string|max:20',
            'isDefault'    => 'boolean'
        ]);

        if ($data['isDefault'] ?? false) {
            DB::table('addresses')
                ->where('user_id', Auth::id())
                ->update(['is_default' => 0]);
        }

        DB::table('addresses')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'name'          => $data['name'],
                'address_line1' => $data['addressLine1'],
                'address_line2' => $data['addressLine2'],
                'city'          => $data['city'],
                'state'         => $data['state'],
                'pincode'       => $data['pincode'],
                'is_default'    => $data['isDefault'] ?? 0,
                'updated_at'    => now(),
            ]);

        return response()->json(['message' => 'Address updated successfully']);
    }

    public function destroy($id)
    {
        DB::table('addresses')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
