<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendanceRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * 🌟 管理者ならすべての操作を無条件で許可する（要件シートの指定）
     */
    public function before(User $user, string $ability)
    {
        if ($user->admin_status) { // ※Userモデルに管理者フラグ（is_adminなど）がある想定
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendanceRecord): bool
    {
        return $user->id === $attendanceRecord->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendanceRecord): bool
    {
        return $user->id === $attendanceRecord->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return false;
    }
}
