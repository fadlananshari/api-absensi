<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    public function Absent(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'status' => 'absent',
            ]
        );

        if ($attendance->wasRecentlyCreated) {
            return response()->json(['message' => 'Absent successful', 'attendance' => $attendance]);
        }

        return response()->json(['message' => 'You already absent today', 'attendance' => $attendance], 400);
    }

    public function show($id)
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'unauthorized',
            ], 403);
        }

        $attendance = Attendance::with('user')->find($id);

        if (!$attendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance attendance not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $attendance
        ]);
    }

    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'check_in' => Carbon::now(),
                'status' => 'present',
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
            ]
        );

        if ($attendance->wasRecentlyCreated) {
            return response()->json(['message' => 'Clock In successful', 'attendance' => $attendance]);
        }

        return response()->json(['message' => 'You already clocked in today', 'attendance' => $attendance], 400);
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'You have not clocked in today'], 400);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'You already clocked out today'], 400);
        }

        $attendance->update([
            'check_out' => Carbon::now(),
            'total_work_hours' => round(Carbon::parse($attendance->check_out)->diffInMinutes($attendance->check_in)/60, 2)
        ]);

        return response()->json(['message' => 'Clock Out successful', 'attendance' => $attendance]);
    }

    public function today()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->where('date', Carbon::today())->first();

        return response()->json($attendance);
    }

    public function report(Request $request)
    {
        $user = Auth::user();
    
        $query = Attendance::where('user_id', $user->id);
    
        // Filter tanggal dari FE
        if ($request->from && $request->to) {
            $query->whereBetween('date', [$request->from, $request->to]);
        }
    
        $report = $query->orderBy('date', 'desc')->get();
    
        return response()->json([
            'status' => 'success',
            'data' => $report,
        ]);
    }
    

    public function reportAll(Request $request)
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'unauthorized',
            ], 403);
        }
    
        $query = Attendance::with('user');
    
        if ($request->from && $request->to) {
            $query->whereBetween('date', [$request->from, $request->to]);
        }
    
        if ($request->name) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->name}%");
            });
        }
    
        $report = $query->orderBy('date', 'desc')->get();
    
        return response()->json([
            'status' => 'success',
            'data' => $report,
        ]);
    }

public function update(Request $request, $id)
{
    $user = Auth::user();

    if ($user->role !== 'admin') {
        return response()->json([
            'status' => 'error',
            'message' => 'unauthorized',
        ], 403);
    }

    $attendance = Attendance::find($id);

    if (!$attendance) {
        return response()->json([
            'status' => 'error',
            'message' => 'Attendance not found',
        ], 404);
    }

    $validated = $request->validate([
        'date' => 'required|date',
        'check_in' => 'nullable|date',
        'check_out' => 'nullable|date',
        'status' => 'required|string|in:present,absent',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    // Konversi waktu ke UTC jika dikirim dalam WIB (Asia/Jakarta)
    $checkInUTC = isset($validated['check_in'])
        ? Carbon::parse($validated['check_in'], 'Asia/Jakarta')->setTimezone('UTC')
        : null;

    $checkOutUTC = isset($validated['check_out'])
        ? Carbon::parse($validated['check_out'], 'Asia/Jakarta')->setTimezone('UTC')
        : null;

    if ($checkInUTC && $checkOutUTC && $checkOutUTC->lessThanOrEqualTo($checkInUTC)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Check-out time cannot be earlier than check-in time.',
        ], 422);
    }

    $totalWorkHours = null;
    if ($checkInUTC && $checkOutUTC) {
        $diffInMinutes = $checkInUTC->diffInMinutes($checkOutUTC);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;
        $totalWorkHours = round($hours + ($minutes / 60), 2);
    }

    $attendance->update([
        'date' => $validated['date'],
        'check_in' => $checkInUTC,
        'check_out' => $checkOutUTC,
        'status' => $validated['status'],
        'latitude' => $validated['latitude'] ?? $attendance->latitude,
        'longitude' => $validated['longitude'] ?? $attendance->longitude,
        'total_work_hours' => $totalWorkHours,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Attendance updated successfully',
        'data' => $attendance,
    ]);
}

    
    public function destroy($id)
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'unauthorized',
            ], 403);
        }
    
        $attendance = Attendance::find($id);
    
        if (!$attendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'data not found',
            ], 404);
        }
    
        $attendance->delete();
    
        return response()->json([
            'status' => 'success',
            'message' => 'data deleted successfully',
        ]);
    }
    
}
