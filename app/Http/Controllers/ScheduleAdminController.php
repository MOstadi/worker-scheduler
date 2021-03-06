<?php

namespace App\Http\Controllers;

use App\Events\ScheduleApproved;
use App\Events\ScheduleDeclined;
use App\Models\Job;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Redirect;

class ScheduleAdminController extends Controller
{
    public function index(){
        $user = auth()->user();
        $schedules = Schedule::calendarDate();
        $jobs = Job::get();
        $week = getWeek();

        return Inertia::render('Admin/ScheduleRequests',compact('user','schedules','jobs','week'));
    }
    public function approve(Request $request)
    {
        $user = auth()->user();
        $input = $request->validate(['id' => 'required|integer|exists:schedules']);
        $schedule = Schedule::find($input['id']);
        $schedule->update(['verified' => 1, 'verified_at' => now(), 'admin_id' => $user->id]);
        event(new ScheduleApproved($schedule));

        return Redirect::route('admin.schedules');
    }
    public function decline(Request $request)
    {
        $user = auth()->user();
        $input = $request->validate(['id'=>'required|integer|exists:schedules']);
        $schedule = Schedule::find($input['id']);
        $schedule->update(['verified' => 0,'verified_at' => now(),'admin_id' => $user->id]);
        event(new ScheduleDeclined($schedule));

        return Redirect::route('admin.schedules');
    }
}
