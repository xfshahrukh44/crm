<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function loginBypass (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return v2_login_bypass($_GET['email'], url()->previous());
    }

    public function backToAdmin (Request $request)
    {
        if (!v2_acl([0, 1, 4, 5, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return v2_back_to_admin();
    }
}
