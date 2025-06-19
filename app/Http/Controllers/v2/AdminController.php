<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function loginBypass (Request $request)
    {
        if (!v2_acl([2])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        return v2_login_bypass($_GET['email']);
    }

    public function backToAdmin (Request $request)
    {
        if (!v2_acl([2, 0, 1, 4, 5, 6])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!session()->has('v2-coming-from-admin')) {
            auth()->logout();

            return redirect()->route('login');
        }

        $admin = User::where('is_employee', 2)->first();

        session()->remove('v2-coming-from-admin');
        return v2_login_bypass($admin->email);
    }
}
