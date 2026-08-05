<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SystemSettingController extends Controller
{
    /**
     * Get all system settings as key-value pairs (Public API)
     */
    public function publicIndex(): JsonResponse
    {
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();

        // Ensure default_primary_color fallback
        if (! isset($settings['default_primary_color']) || empty($settings['default_primary_color'])) {
            $settings['default_primary_color'] = 'orangered';
        }

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Update or create a system setting (Auth SuperAdmin / Admin)
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:100',
            'value' => 'nullable|string|max:255',
        ]);

        $setting = SystemSetting::updateOrCreate(
            ['key' => $validated['key']],
            ['value' => $validated['value']]
        );

        return response()->json([
            'message' => 'System setting updated successfully.',
            'data' => [
                'key' => $setting->key,
                'value' => $setting->value,
            ],
        ]);
    }
}
