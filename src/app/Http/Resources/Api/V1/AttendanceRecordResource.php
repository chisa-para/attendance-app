<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => optional($this->user)->name,

            //  日時カラムを日本時間のフォーマット（YYYY-MM-DD HH:mm:ss）
            'date' => $this->start_at ? $this->start_at->format('Y-m-d') : null,
            'clock_in' => $this->start_at ? $this->start_at->format('H:i:s') : null,
            'clock_out' => $this->finish_at ? $this->finish_at->format('H:i:s') : null,

            'comment'    => $this->retouch_reason,
            
            // Attendance.phpのアクセサ（計算結果）を呼び出す
            'total_time' => $this->actual_work_time, 

            // getDisplayTotalRestTimeAttribute() を呼び出す
            'total_break_time' => $this->display_total_rest_time,
            // 関連データ（リレーション）もそのまま含める
            'breaks' => $this->whenLoaded('rests'),
            'application' => $this->whenLoaded('approvalRequests'),
        ];
    }
}
