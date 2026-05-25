<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\School;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SchoolSettingsController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        $session = session('school_id');
        return redirect()->route('schools.edit',[encrypt($session)]);
    }

    public function store(Request $request)
    {

//        dd([
//            'hasFile' => $request->hasFile('Logo'),
//            'file' => $request->file('Logo'),
//            'error' => $request->file('Logo')?->getError(),
//            'errorMessage' => $request->file('Logo')?->getErrorMessage(),
//        ]);
        $validated = $request->validate([
            'SchoolName' => ['required', 'string', 'max:255'],
            'SchoolCode' => ['nullable', 'string', 'max:100'],
            'SystemTitle' => ['required', 'string', 'max:255'],
            'EducationLevel' => [
                'required',
                Rule::in(['JHS', 'SHS', 'INTEGRATED'])
            ],

            'Region' => ['nullable', 'string', 'max:100'],
            'Division' => ['nullable', 'string', 'max:100'],
            'Address' => ['nullable', 'string'],

            'ContactNumber' => [
                'nullable',
                'regex:/^\+639\d{9}$/'
            ],

            'EmailAddress' => [
                'nullable',
                'max:150'
            ],

            'PrincipalID' => [
                'nullable',
                'exists:employees,id'
            ],

            'RegistrarID' => [
                'nullable',
                'exists:employees,id'
            ],

            'OfficialTimeIn' => [
                'nullable',
                'date_format:H:i'
            ],

            'OfficialTimeOut' => [
                'nullable',
                'date_format:H:i'
            ],

            'LateGraceMinutes' => [
                'nullable',
                'integer',
                'min:0'
            ],

            'ThemeColor' => [
                'nullable',
                'string',
                'max:20'
            ],

            'Logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10000'
            ],
            'cacert_path' => [
                'nullable',
                'string',
                'max:255'
            ],
            'port_com' => [
                'nullable',
                'string',
                'max:255'
            ],
            'python_path' => [
                'nullable',
                'string',
                'max:100'
            ]
        ]);

//        dd($validated);

        $validated['EnableNFC'] = $request->boolean('EnableNFC');
        $validated['EnableQR'] = $request->boolean('EnableQR');
        $validated['EnableOfflineAttendance'] = $request->boolean('EnableOfflineAttendance');

        $setting = School::first();

        if (!$setting) {

            $setting = new School();

            $this->setCommonFields($setting);
        }

        if ($request->hasFile('Logo')) {

            if (
                $setting->Logo &&
                Storage::disk('public')->exists($setting->Logo)
            ) {

                Storage::disk('public')->delete($setting->Logo);
            }

            $validated['Logo'] = $request
                ->file('Logo')
                ->store('school/logo', 'public');
        } else {

            unset($validated['Logo']);
        }
        $setting->SystemTitle = $validated['SystemTitle'];
        $setting->SchoolName = $validated['SchoolName'];
        $setting->SchoolCode = $validated['SchoolCode'];
        $setting->EducationLevel = $validated['EducationLevel'];
        $setting->Region = $validated['Region'];
        $setting->Division = $validated['Division'];
        $setting->Address = $validated['Address'];
        $setting->ContactNumber = $validated['ContactNumber'];
        $setting->EmailAddress = $validated['EmailAddress'];
        $setting->PrincipalID = $validated['PrincipalID'] ?? null;
        $setting->RegistrarID = $validated['RegistrarID'] ?? null;

        if (isset($validated['Logo'])) {

            $setting->Logo = $validated['Logo'];
        }

        $setting->OfficialTimeIn = $validated['OfficialTimeIn'];
        $setting->OfficialTimeOut = $validated['OfficialTimeOut'];
        $setting->LateGraceMinutes = $validated['LateGraceMinutes'];

        $setting->EnableNFC = $validated['EnableNFC'];
        $setting->EnableQR = $validated['EnableQR'];
        $setting->EnableOfflineAttendance = $validated['EnableOfflineAttendance'];
        $setting->ThemeColor = $validated['ThemeColor'];
        $setting->cacert_path = $validated['cacert_path'];
        $setting->python_path = $validated['python_path'];
        $setting->port_com = $validated['port_com'];

        $setting->save();

        Cache::forget('school_settings_' . $setting->id);

        return redirect()
            ->route('settings.index')
            ->with(
                'success',
                'School settings saved successfully.'
            );
    }

    public function update(Request $request, SchoolSetting $settings)
    {
        $validated = $request->validate([
            'SystemTitle' => ['required', 'string', 'max:255'],
            'SchoolName' => ['required', 'string', 'max:255'],
            'SchoolCode' => ['nullable', 'string', 'max:100'],
            'EducationLevel' => [
                'required',
                Rule::in(['JHS', 'SHS', 'INTEGRATED'])
            ],

            'Region' => ['nullable', 'string', 'max:100'],
            'Division' => ['nullable', 'string', 'max:100'],
            'Address' => ['nullable', 'string'],

            'ContactNumber' => [
                'nullable',
                'regex:/^\+639\d{9}$/'
            ],

            'EmailAddress' => [
                'nullable',
                'email',
                'max:150'
            ],

            'PrincipalName' => ['nullable', 'string', 'max:255'],
            'RegistrarName' => ['nullable', 'string', 'max:255'],

            'OfficialTimeIn' => ['nullable', 'date_format:H:i'],
            'OfficialTimeOut' => ['nullable', 'date_format:H:i'],

            'LateGraceMinutes' => ['nullable', 'integer', 'min:0'],

            'ThemeColor' => ['nullable', 'string', 'max:20'],

            'Logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ]);

        $validated['EnableNFC'] = $request->boolean('EnableNFC');
        $validated['EnableQR'] = $request->boolean('EnableQR');
        $validated['EnableOfflineAttendance'] = $request->boolean('EnableOfflineAttendance');

        if ($request->hasFile('Logo')) {

            if ($settings->Logo && Storage::disk('public')->exists($settings->Logo)) {

                Storage::disk('public')->delete($settings->Logo);
            }

            $validated['Logo'] = $request
                ->file('Logo')
                ->store('school/logo', 'public');
        }

        $settings->update($validated);

        Cache::forget('school_settings');

        return redirect()
            ->route('settings.index')
            ->with('success', 'School settings updated successfully.');
    }

    public function destroy(SchoolSetting $settings)
    {
        if ($settings->Logo && Storage::disk('public')->exists($settings->Logo)) {

            Storage::disk('public')->delete($settings->Logo);
        }

        $settings->delete();

        Cache::forget('school_settings');

        return redirect()
            ->route('settings.index')
            ->with('success', 'School settings deleted successfully.');
    }
}
