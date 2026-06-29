<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Attendance::class;

    public function definition(): array
    {
        // デフォルト値
        return [
            'user_id' => 1,
            'start_at' => Carbon::now(),
            'finish_at' => Carbon::now()->addHours(8),
        ];
    }

    /**
     * 出勤データ作成後、自動的に12:00〜13:00の休憩データを紐づける
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Attendance $attendance) {
            // 出勤日の12:00と13:00を算出
            $date = Carbon::parse($attendance->start_at)->format('Y-m-d');
            
            Rest::create([
                'attendance_id' => $attendance->id,
                'rest_start_at' => Carbon::parse("${date} 12:00:00"),
                'rest_finish_at' => Carbon::parse("${date} 13:00:00"),
            ]);
        });
    }
}
