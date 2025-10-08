<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    use HasFactory;

    protected $table = 'attendance_rules';

    protected $fillable = [
        'office_start',
        'office_end',
        'latitude',
        'longitude',
        'radius_meter',
        'tolerance_minutes',
    ];
}
