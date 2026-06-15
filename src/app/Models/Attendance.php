<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_at',
        'finish_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'finish_at' => 'datetime',
    ];

    public function user(){
    return $this->belongsTo(User::class);
    }

    public function rests(){
    return $this->hasMany(Rest::class);
    }

    /**
     * 休憩時間（文字列）を計算するアクセサ
     */

    public function getTotalRestMinutesAttribute()
    {
        $totalMinutes = 0;
        foreach ($this->rests as $rest) {
            if ($rest->rest_start_at && $rest->rest_finish_at) {
                $totalMinutes += $rest->rest_start_at->diffInMinutes($rest->rest_finish_at);
            }
        }
        return $totalMinutes;
    }

    /**
     * 実働時間（文字列）を計算するアクセサ
     */
    public function getActualWorkTimeAttribute()
    {
        if (!$this->start_at || !$this->finish_at) {
            return '-';
        }

        // 総労働時間（分）
        $totalMinutes = $this->start_at->diffInMinutes($this->finish_at);
        
        // 実働時間 ＝ 総労働時間 － 休憩時間
        $actualMinutes = $totalMinutes - $this->total_rest_minutes;

        if ($actualMinutes < 0) $actualMinutes = 0;

        // 「〇時間〇分」の形式に変換
        $hours = floor($actualMinutes / 60);
        $minutes = $actualMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function getDisplayTotalRestTimeAttribute()
    {
        $hours = floor($this->total_rest_minutes / 60);
        $minutes = $this->total_rest_minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
