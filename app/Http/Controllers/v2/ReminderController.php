<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReminderController extends Controller
{
    public function setReminder (Request $request)
    {
        if (!v2_acl([2, 0, 4, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'heading' => 'required',
            'text' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        if ($request->has('ping_time') && $request->get('ping_time') != "") {
            DB::table('reminders')->insert([
                'user_id' => auth()->id(),
                'heading' => $request->heading,
                'text' => $request->text,
                'ping_time' => Carbon::parse($request->get('ping_time')),
            ]);
        }

        if ($request->has('hours') && $request->get('hours') > 0) {
            DB::table('reminders')->insert([
                'user_id' => auth()->id(),
                'heading' => $request->heading,
                'text' => $request->text,
                'ping_time' => Carbon::now()->addHours($request->get('hours')),
            ]);
        }

        return redirect()->back()->with('success', 'Reminder set!');
    }
}
