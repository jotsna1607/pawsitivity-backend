<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ClientActivityController extends Controller
{
    public function getPetActivity($petName)
    {
        $petKey = strtolower($petName);

        $data = [
            'maxi' => [
                'pet' => [
                    'age' => 5,
                    'breed' => 'Golden Retriever',
                    'weight' => 12,
                ],
                'healthScore' => [
                    ['name' => 'Nutrition', 'value' => 40],
                    ['name' => 'Exercise', 'value' => 35],
                    ['name' => 'Sleep', 'value' => 25],
                ],
                'healthTrends' => [
                    ['date' => '2025-08-01', 'score' => 75],
                    ['date' => '2025-08-02', 'score' => 80],
                    ['date' => '2025-08-03', 'score' => 78],
                ],
                'exercise' => [
                    ['day' => 'Monday', 'minutes' => 30],
                    ['day' => 'Tuesday', 'minutes' => 45],
                    ['day' => 'Wednesday', 'minutes' => 40],
                ],
                'activities' => [
                    ['date' => '2025-08-05', 'description' => 'Morning walk for 30 minutes'],
                    ['date' => '2025-08-04', 'description' => 'Vet visit for vaccination'],
                ],
                'doctorNotes' => 'Maxi is healthy but needs more exercise.',
                'medications' => [
                    ['name' => 'Flea Medicine', 'dosage' => 'Once a month', 'start_date' => '2025-07-01', 'end_date' => '2026-07-01'],
                ],
            ],
            'stella' => [
                'pet' => [
                    'age' => 3,
                    'breed' => 'Beagle',
                    'weight' => 9,
                ],
                'healthScore' => [
                    ['name' => 'Nutrition', 'value' => 45],
                    ['name' => 'Exercise', 'value' => 30],
                    ['name' => 'Sleep', 'value' => 25],
                ],
                'healthTrends' => [
                    ['date' => '2025-08-01', 'score' => 70],
                    ['date' => '2025-08-02', 'score' => 74],
                    ['date' => '2025-08-03', 'score' => 72],
                ],
                'exercise' => [
                    ['day' => 'Monday', 'minutes' => 25],
                    ['day' => 'Tuesday', 'minutes' => 40],
                    ['day' => 'Wednesday', 'minutes' => 35],
                ],
                'activities' => [
                    ['date' => '2025-08-05', 'description' => 'Afternoon walk for 25 minutes'],
                    ['date' => '2025-08-04', 'description' => 'Training session'],
                ],
                'doctorNotes' => 'Stella needs a special diet for allergies.',
                'medications' => [
                    ['name' => 'Allergy Medicine', 'dosage' => 'Twice a day', 'start_date' => '2025-06-01', 'end_date' => '2025-12-01'],
                ],
            ],
            'ruby' => [
                'pet' => [
                    'age' => 4,
                    'breed' => 'Mixed Breed',
                    'weight' => 10,
                ],
                'healthScore' => [
                    ['name' => 'Nutrition', 'value' => 50],
                    ['name' => 'Exercise', 'value' => 30],
                    ['name' => 'Sleep', 'value' => 20],
                ],
                'healthTrends' => [
                    ['date' => '2025-08-01', 'score' => 65],
                    ['date' => '2025-08-02', 'score' => 68],
                    ['date' => '2025-08-03', 'score' => 70],
                ],
                'exercise' => [
                    ['day' => 'Monday', 'minutes' => 20],
                    ['day' => 'Tuesday', 'minutes' => 30],
                    ['day' => 'Wednesday', 'minutes' => 25],
                ],
                'activities' => [
                    ['date' => '2025-08-05', 'description' => 'Evening walk for 35 minutes'],
                    ['date' => '2025-08-04', 'description' => 'Grooming session'],
                ],
                'doctorNotes' => 'Ruby needs more grooming sessions.',
                'medications' => [
                    ['name' => 'Vitamin Supplements', 'dosage' => 'Once a day', 'start_date' => '2025-05-01', 'end_date' => '2025-11-01'],
                ],
            ],
        ];

        if (array_key_exists($petKey, $data)) {
            return response()->json($data[$petKey]);
        }

        return response()->json(['error' => 'Pet not found'], 404);
    }
}
