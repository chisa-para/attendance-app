<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'reason',
        'status',
        'request_start_at',
        'request_finish_at',
    ];

    protected $casts = [
        'request_start_at'  => 'datetime',
        'request_finish_at' => 'datetime',
    ];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function approvalRests(){
        return $this->hasMany(ApprovalRest::class);
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Attendance::class,
            'id',          //approval_requests.attendance_id と紐づく
            'id',          //attendances.user_id と紐づく
            'attendance_id',
            'user_id'
        );
    }

}
