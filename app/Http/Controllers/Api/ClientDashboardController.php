<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ClientDashboardController extends Controller
{
    protected array $petData = [];

    public function __construct()
    {
        $this->petData = [
            'maxi' => $this->formatPet([
                'name' => 'Maxi',
                'age' => 3,
                'breed' => 'Labrador',
                'avatar_url' => '/images/maxi.jpg',
                'weight' => 22,
                'heart_rate' => 85,
                'sleep' => 7,
                'activity' => 120,
                'weekly_activity' => [
                    ['day' => 'Mon', 'minutes' => 70],
                    ['day' => 'Tue', 'minutes' => 60],
                    ['day' => 'Wed', 'minutes' => 80],
                    ['day' => 'Thu', 'minutes' => 65],
                    ['day' => 'Fri', 'minutes' => 90],
                ],
                'breakdown' => [
                    ['type' => 'Walking', 'value' => 40],
                    ['type' => 'Playing', 'value' => 35],
                    ['type' => 'Resting', 'value' => 25],
                ],
                'appointments' => [
                    ['date' => '2025-08-10', 'type' => 'Dental Check', 'vet' => 'Dr. Smith'],
                    ['date' => '2025-08-15', 'type' => 'Vaccination', 'vet' => 'Dr. Jones'],
                ],
                'health_checks' => [
                    ['date' => '2025-07-10', 'summary' => 'Vaccination completed'],
                    ['date' => '2025-07-20', 'summary' => 'Deworming due'],
                ],
                'recommendations' => [
                    ['name' => 'Joint Support', 'description' => 'Supports mobility', 'image' => asset('images/Joint1.png')],
                    ['name' => 'Dental Chew', 'description' => 'For skin health', 'image' => asset('images/Toy1.png')],
                ],
            ]),
            'stella' => $this->formatPet([
                'name' => 'Stella',
                'age' => 2,
                'breed' => 'Beagle',
                'avatar_url' => '/images/stella.jpg',
                'weight' => 18,
                'heart_rate' => 90,
                'sleep' => 8,
                'activity' => 110,
                'weekly_activity' => [
                    ['day' => 'Mon', 'minutes' => 60],
                    ['day' => 'Tue', 'minutes' => 70],
                    ['day' => 'Wed', 'minutes' => 65],
                    ['day' => 'Thu', 'minutes' => 55],
                    ['day' => 'Fri', 'minutes' => 75],
                ],
                'breakdown' => [
                    ['type' => 'Walking', 'value' => 50],
                    ['type' => 'Playing', 'value' => 30],
                    ['type' => 'Resting', 'value' => 20],
                ],
                'appointments' => [
                    ['date' => '2025-08-12', 'type' => 'Checkup', 'vet' => 'Dr. Moore'],
                ],
                'health_checks' => [
                    ['date' => '2025-06-18', 'summary' => 'Vaccination completed'],
                ],
                'recommendations' => [
                   ['name' => 'Dental Chew', 'description' => 'For oral health', 'image' => asset('images/Shampoo1.png')],
                ],
            ]),
            'ruby' => $this->formatPet([
                'name' => 'Ruby',
                'age' => 4,
                'breed' => 'Golden Retriever',
                'avatar_url' => '/images/ruby.jpg',
                'weight' => 25,
                'heart_rate' => 78,
                'sleep' => 6,
                'activity' => 135,
                'weekly_activity' => [
                    ['day' => 'Mon', 'minutes' => 85],
                    ['day' => 'Tue', 'minutes' => 95],
                    ['day' => 'Wed', 'minutes' => 80],
                    ['day' => 'Thu', 'minutes' => 70],
                    ['day' => 'Fri', 'minutes' => 100],
                ],
                'breakdown' => [
                    ['type' => 'Walking', 'value' => 30],
                    ['type' => 'Playing', 'value' => 45],
                    ['type' => 'Resting', 'value' => 25],
                ],
                'appointments' => [
                    ['date' => '2025-08-18', 'type' => 'Ear Check', 'vet' => 'Dr. Adams'],
                ],
                'health_checks' => [
                    ['date' => '2025-07-05', 'summary' => 'Ear Check completed'],
                ],
                'recommendations' => [
                    ['name' => 'Retriever Toy', 'description' => 'Chew Resistant', 'image' => asset('images/Toy1.png')],
                ],
            ]),
        ];
    }

    private function formatPet(array $data): array
    {
        return [
            'pet' => [
                'name' => $data['name'],
                'age' => $data['age'],
                'breed' => $data['breed'],
                'avatar_url' => $data['avatar_url'],
            ],
            'health' => [
                'weight' => $data['weight'],
                'heart_rate' => $data['heart_rate'],
                'sleep' => $data['sleep'],
                'activity' => $data['activity'],
                'weekly_activity' => $data['weekly_activity'],
                'breakdown' => $data['breakdown'],
            ],
            'appointments' => $data['appointments'],
            'health_checks' => $data['health_checks'],
            'recommendations' => $data['recommendations'],
        ];
    }

    public function getMetrics(string $pet): JsonResponse
    {
        if (!array_key_exists($pet, $this->petData)) {
            return response()->json(['error' => 'Pet not found'], 404);
        }

        return response()->json($this->petData[$pet]);
    }
}
