<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;

class AttendanceReportController extends Controller
{
    public function index(){
        $user = Auth::user();

        //6ケ月前の1日から
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        // 今月の末まで
        $endDate = Carbon::now()->endOfMonth();

        $monthlyAttendance = Attendance::with('rests')
        ->where('user_id', $user->id)
        ->whereBetween('start_at', [$startDate, $endDate])
        ->orderBy('start_at', 'asc')
        ->get()
        ->groupBy(function($item) {
            return Carbon::parse($item->start_at)->format('Y-m');
        })
        ->map(function($days) {
            //月次集計
            $actualMinutes = $days->sum('actual_work_minutes');//実働時間
            $overtimeMinutes = $days->sum(function($day) {
                return $day->actual_work_minutes > 480 ? ($day->actual_work_minutes - 480) : 0;//実働時間が残業になっているかをチェック
            });

            //遅刻・早退・長時間労働を初期値0でセット
            $lateCount = 0;
            $earlyLeaveCount = 0;
            $longWorkCount = 0;

            foreach ($days as $day) {
                if (!$day->start_at || !$day->finish_at) continue;

                // 遅刻判定：出勤日の「時刻」が 09:01 以降の場合
                // ※秒単位のブレを考慮して 09:00:59 を超えているか、または「09:00」より大きいかで判定
                if ($day->start_at->format('H:i') > '09:00') {
                    $lateCount++;
                }

                // 早退判定：退勤日の「時刻」が 18:00 より前の場合
                if ($day->finish_at->format('H:i') < '18:00') {
                    $earlyLeaveCount++;
                }

                // 長時間労働判定：実働10時間（600分）以上の場合
                if ($day->actual_work_minutes > 600) {
                    $longWorkCount++;
                }
            }

            return [
                'days' => $days,
                'day_count' => $days->count(), // 出勤日数
                'actual_minutes' => $actualMinutes, // 計算用の生データ（分）
                'overtime_minutes' => $overtimeMinutes,
                'late_count' => $lateCount,
                'early_leave_count' => $earlyLeaveCount,
                'long_work_count' => $longWorkCount,
                // 画面表示用の文字列
                'total_time_str' => sprintf('%02dh%01dm', floor($actualMinutes / 60), $actualMinutes % 60),
                'overtime_time_str' => sprintf('%02dh%01dm', floor($overtimeMinutes / 60), $overtimeMinutes % 60),
            ];
        });

        $totalActualMinutes = $monthlyAttendance->sum('actual_minutes');
        $totalOvertimeMinutes = $monthlyAttendance->sum('overtime_minutes');
        $totalDays = $monthlyAttendance->sum('day_count');

        // 1日あたりの平均労働時間（分）の計算
        $averageMinutesPerDay = $totalDays > 0 ? round($totalActualMinutes / $totalDays) : 0;

        $summary6Months = [
            'total_work_time_str' => sprintf('%02dh%01dm', floor($totalActualMinutes / 60), $totalActualMinutes % 60),
            'total_overtime_time_str' => sprintf('%02dh%01dm', floor($totalOvertimeMinutes / 60), $totalOvertimeMinutes % 60),
            'average_work_time_str' => sprintf('%02dh%01dm', floor($averageMinutesPerDay / 60), $averageMinutesPerDay % 60),
            'total_late_count' => $monthlyAttendance->sum('late_count'),
            'total_early_leave_count' => $monthlyAttendance->sum('early_leave_count'),
            'total_long_work_count' => $monthlyAttendance->sum('long_work_count'),
        ];

        return view('general.attendance_report',compact('monthlyAttendance', 'summary6Months'));
    }
}
