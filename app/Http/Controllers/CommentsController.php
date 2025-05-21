<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentsController extends Controller
{
    public function updateClientComments (Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->is_employee, [0, 2, 4, 6])) { return redirect()->back(); }

        DB::table('clients')->where('id', $request->rec_id)->update([
            'comments' => $request->comments ?? '',
            'comments_id' => auth()->id(),
            'comments_timestamp' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Comments added!',
            'errors' => [],
        ]);
    }

    public function updateProjectComments (Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->is_employee, [0, 2, 4, 6])) { return redirect()->back(); }

        DB::table('projects')->where('id', $request->rec_id)->update([
            'comments' => $request->comments ?? '',
            'comments_id' => auth()->id(),
            'comments_timestamp' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Comments added!',
            'errors' => [],
        ]);
    }
}
