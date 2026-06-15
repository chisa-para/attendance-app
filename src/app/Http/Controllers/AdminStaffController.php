<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class AdminStaffController extends Controller
{
    public function index()
    {
        $members = User::where('admin_status','false')->get();

        return view('admin.staff_list',compact('members'));
    }
}
