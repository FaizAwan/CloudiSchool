<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function getTimetable()
    {
        return response()->json(Timetable::all());
    }

    public function storeTimetable(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => 'required',
            'day' => 'required',
            'period_id' => 'required',
            'class' => 'required',
            'subject' => 'required',
        ]);
        $data['tenant_id'] = 2;

        $tt = Timetable::create($data);
        return response()->json($tt, 201);
    }
}
