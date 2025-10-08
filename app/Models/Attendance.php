<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'latitude',
        'longitude',
        'location_status',
        'total_work_hours',
        'total_late_minutes'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'total_work_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
