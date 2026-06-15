<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalRest extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_request_id',
        'rest_id',
        'request_rest_start_at',
        'request_rest_finish_at',
    ];

    protected $casts = [
        'request_rest_start_at'  => 'datetime',
        'request_rest_finish_at' => 'datetime',
    ];

    public function approvalRequest(){
    return $this->belongsTo(ApprovalRequest::class);
    }
}
