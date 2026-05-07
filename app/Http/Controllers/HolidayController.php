<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $year     = request('year', now()->year);
        $holidays = Holiday::whereYear('holiday_date', $year)->orderBy('holiday_date')->get();
        return view('admin.holidays', compact('holidays', 'year'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'holiday_date' => 'required|date|unique:holidays',
            'name'         => 'required|string|max:100',
            'is_optional'  => 'boolean',
        ]);

        $data['is_optional'] = $request->boolean('is_optional');

        Holiday::create($data);

        return back()->with('success', 'Holiday added successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return back()->with('success', 'Holiday removed.');
    }
}
