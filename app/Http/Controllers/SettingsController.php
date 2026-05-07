<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private array $keys = [
        'present_hours'         => ['label' => 'Full-day hours threshold', 'description' => 'Minimum net hours to mark as Present (not half-day).', 'type' => 'number', 'step' => '0.5'],
        'half_day_hours'        => ['label' => 'Half-day hours threshold', 'description' => 'Minimum net hours to mark as Half-Day (not Absent).', 'type' => 'number', 'step' => '0.5'],
        'el_per_month'          => ['label' => 'Earned Leave accrual per month', 'description' => 'Number of EL days earned per month automatically.', 'type' => 'number', 'step' => '0.5'],
        'el_carry_forward_max'  => ['label' => 'Max EL carry-forward', 'description' => 'Maximum EL days that can be carried forward to next year.', 'type' => 'number', 'step' => '1'],
        'working_days_per_week' => ['label' => 'Working days per week', 'description' => 'Used for leave calculation. Typically 5 (Mon–Fri).', 'type' => 'number', 'step' => '1'],
    ];

    public function index()
    {
        $settings = AttendanceSetting::all()->keyBy('key');
        $keys = $this->keys;
        return view('admin.settings', compact('settings', 'keys'));
    }

    public function update(Request $request)
    {
        $rules = collect($this->keys)->mapWithKeys(fn ($v, $k) => [$k => 'required|numeric|min:0'])->toArray();
        $data  = $request->validate($rules);

        foreach ($data as $key => $value) {
            AttendanceSetting::setValue($key, $value);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
