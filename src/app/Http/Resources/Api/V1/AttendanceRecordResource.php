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
            
            // 🌟 日時カラムを日本時間のフォーマット（YYYY-MM-DD HH:mm:ss）に変換
            // ※ご自身のテーブルのカカラム名（punch_in_timeなど）に合わせて書き換えてください
            'start_at' => $this->start_at ? $this->start_at->format('Y-m-d H:i:s') : null,
            'finish_at' => $this->finish_at ? $this->finish_at->format('Y-m-d H:i:s') : null,
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // 関連データ（リレーション）もそのまま含める
            'user' => $this->whenLoaded('user'),
            'rests' => $this->whenLoaded('rests'),
            'approvalRequests' => $this->whenLoaded('approvalRequests'),
        ];
    }
}
