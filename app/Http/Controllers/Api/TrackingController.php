<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    private $petsData = [
        1 => [
            'lastLocation' => 'Nungambakkam, Chennai',
            'battery' => '85%',
            'mode' => 'Standard',
            'coordinates' => [13.0587, 80.2376], // Ningambakkam coords
            'logs' => [
                ['time' => '2025-08-11 10:00', 'activity' => 'Maxi moved 200m north in Nungambakkam'],
                ['time' => '2025-08-10 15:30', 'activity' => 'Maxi resting near the park'],
            ],
            'guardian' => 'John Doe',
        ],
        2 => [
            'lastLocation' => 'Kolathur, Chennai',
            'battery' => '60%',
            'mode' => 'Power Saving',
            'coordinates' => [13.1151, 80.2164], // Kolathur coords
            'logs' => [
                ['time' => '2025-08-11 09:00', 'activity' => 'Stella played in Kolathur garden'],
                ['time' => '2025-08-10 14:00', 'activity' => 'Stella eating near the pond'],
            ],
            'guardian' => 'Jane Smith',
        ],
        3 => [
            'lastLocation' => 'Anna Nagar, Chennai',
            'battery' => '90%',
            'mode' => 'Standard',
            'coordinates' => [13.0827, 80.2043], // Anna Nagar coords
            'logs' => [
                ['time' => '2025-08-11 08:00', 'activity' => 'Ruby running around Anna Nagar park'],
                ['time' => '2025-08-10 12:00', 'activity' => 'Ruby sleeping near the lake'],
            ],
            'guardian' => 'Alex Johnson',
        ],
    ];

    public function index(Request $request, $petId = null)
    {
        $id = $petId ?? 1;

        if (!array_key_exists($id, $this->petsData)) {
            return response()->json(['error' => 'Pet not found'], 404);
        }

        return response()->json($this->petsData[$id]);
    }
}
