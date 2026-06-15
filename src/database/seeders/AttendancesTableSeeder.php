<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
    {
        $userId = 1; 

        // 1.過去5ヶ月間の通常勤務
        for ($i = 5; $i >= 1; $i--) {
            $month = Carbon::now()->subMonths($i);
            $date = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $workdays = [];
            // 月の初日から末日まで1日ずつ進めるループ
            while ($date->lte($endOfMonth)) {
                if ($date->isWeekday()) {
                    $workdays[] = $date->format('Y-m-d');
                }
                $date->addDay();
            }

            $targetDays = array_slice($workdays, 0, 15);

            foreach ($targetDays as $d) {
                Attendance::factory()->create([
                    'user_id' => $userId,
                    'start_at' => Carbon::parse("${d} 09:00:00"),
                    'finish_at' => Carbon::parse("${d} 18:00:00"),
                ]);
            }
        }

        // 2.当月の勤務データ
        $currentMonth = Carbon::now();
        $date = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $currentWorkdays = [];
        while ($date->lte($endOfMonth)) {
            if ($date->isWeekday()) {
                $currentWorkdays[] = $date->format('Y-m-d');
            }
            $date->addDay();
        }

        $targetCurrentDays = array_slice($currentWorkdays, 0, 17);

        $patterns = [
            ['start' => '09:00:00', 'finish' => '18:00:00', 'count' => 10],
            ['start' => '09:00:00', 'finish' => '20:00:00', 'count' => 3],
            ['start' => '09:30:00', 'finish' => '18:00:00', 'count' => 2],
            ['start' => '09:00:00', 'finish' => '17:00:00', 'count' => 1],
            ['start' => '08:00:00', 'finish' => '21:00:00', 'count' => 1],
        ];

        $dayIndex = 0;
        foreach ($patterns as $pattern) {
            for ($j = 0; $j < $pattern['count']; $j++) {
                if (!isset($targetCurrentDays[$dayIndex])) {
                    break;
                }
                $d = $targetCurrentDays[$dayIndex];

                Attendance::factory()->create([
                    'user_id' => $userId,
                    'start_at' => Carbon::parse("${d} {$pattern['start']}"),
                    'finish_at' => Carbon::parse("${d} {$pattern['finish']}"),
                ]);

                $dayIndex++;
            }
        }

        $userId2 = 2;

        // 過去5ヶ月前から当月まで
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $date = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $workdays = [];
            while ($date->lte($endOfMonth)) {
                if ($date->isWeekday()) {
                    $workdays[] = $date->format('Y-m-d');
                }
                $date->addDay();
            }

            // 月20日出勤
            $targetDays = array_slice($workdays, 0, 20);

            foreach ($targetDays as $d) {
                // 8:00〜22:00の間で、7時間〜12時間のランダムな勤務時間
                $workingHours = rand(7, 12);
                // 開始時間は 08:00 〜 (22:00 - 勤務時間) の間でランダムに決定
                // 例: 12時間勤務なら 22 - 12 = 10 なので、開始は 08:00、09:00、10:00 のいずれかになる
                $latestStartHour = 22 - $workingHours;
                $startHour = rand(8, $latestStartHour);

                // 分単位も少しバラつかせたい場合は rand(0, 1) * 30 などにできますが、
                // 今回はシンプルに◯時00分スタートで計算します
                $startAt = Carbon::parse("${d} " . sprintf('%02d:00:00', $startHour));
                $finishAt = $startAt->copy()->addHours($workingHours);

                Attendance::factory()->create([
                    'user_id' => $userId2,
                    'start_at' => $startAt,
                    'finish_at' => $finishAt,
                ]);
            }
        }
    }
}