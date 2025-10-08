<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttendanceRuleController extends Controller
{
    public function index()
    {
        $rules = AttendanceRule::all();

        return response()->json([
            'status' => 'success',
            'data' => $rules
        ]);
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

        $rule = AttendanceRule::find($id);

        if (!$rule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance rule not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $rule
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

        $rule = AttendanceRule::findOrFail($id);

        $rule->update($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Rule updated'
        ]);
    }

}
