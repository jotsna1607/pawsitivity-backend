<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    private $filePath = 'storage/app/admin_settings.json';

    public function getSettings()
    {
        if (!file_exists($this->filePath)) {
            return response()->json([
                "siteName" => "My Website",
                "siteDescription" => "Best platform for pets.",
                "email" => "admin@example.com",
                "phone" => "+91 9876543210",
                "notifications" => true,
                "theme" => "light",
                "timezone" => "Asia/Kolkata",
                "adminName" => "Admin",
                "adminBio" => "This is the admin profile."
            ]);
        }

        $data = json_decode(file_get_contents($this->filePath), true);
        return response()->json($data);
    }

    public function saveSettings(Request $request)
    {
        file_put_contents($this->filePath, json_encode($request->all(), JSON_PRETTY_PRINT));
        return response()->json(["message" => "Settings saved successfully"]);
    }
}
