<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index (Request $request)
    {
        if (!v2_acl([2, 4, 6, 0])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return view('v2.tutorial.index');
    }
}
