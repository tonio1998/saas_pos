<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::firstOrCreate([]);

        return view(
            'pages.sa.system-settings.index',
            compact('settings')
        );
    }

    public function update(Request $request)
    {
        try {

            $settings = SystemSetting::find(1);

            if (!$settings) {

                $settings = SystemSetting::create([
                    'id' => 1,
                ]);
            }

            $validated = $request->validate([
                'cacert_path' => ['nullable'],
                'python_path' => ['nullable'],
                'port_com' => ['nullable', 'max:20'],
                'gsm_enabled' => ['nullable'],
                'sms_enabled' => ['nullable'],
            ]);

            $validated['gsm_enabled'] =
                $request->boolean('gsm_enabled');

            $validated['sms_enabled'] =
                $request->boolean('sms_enabled');

            $settings->update($validated);

            Cache::forget('system_settings');

            return back()->with(
                'success',
                'System settings updated successfully.'
            );

        } catch (\Throwable $e) {

            \Log::error($e);

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}
