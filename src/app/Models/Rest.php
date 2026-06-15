<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'rest_start_at',
        'rest_finish_at',
    ];

    protected $casts = [
        'rest_start_at' => 'datetime',
        'rest_finish_at' => 'datetime',
    ];

    public function attendance(){
    return $this->belongsTo(Attendance::class);
    }
}
