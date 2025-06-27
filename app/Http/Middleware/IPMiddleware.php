<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class IPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
//        if ((mt_rand(1, 100) % 4) == 0) {
//            auth()->logout();
//            return redirect()->route('login')->with("error","Access denied.");
//        }

        if (v2_acl([2, 6, 4, 0, 1, 5])) {
            if (!session()->has('v2_valid_user')) {
                $bytes = bin2hex(random_bytes(3));
                DB::table('users')
                    ->where('id', auth()->user()->id)
                    ->update(['verfication_code' => $bytes, 'verfication_datetime' => date('Y-m-d H:i:s')]);

                $details = [
                    'title' => 'Verfication Code',
                    'body' => 'Your one time use Verfication code for email ' . auth()->user()->email . ' is ' . $bytes,
                    'last_login_ip' => $request->ip(),
                    'last_login_device' => $_SERVER['HTTP_USER_AGENT'],
                ];

                $sender_emails = ['bilal.khan3587341@gmail.com', 's4s.mohsin@gmail.com', 'sayedmehdius@gmail.com'];

                try {
                    Mail::send('mail', $details, function($message) use ($bytes, $sender_emails){
                        $message->to($sender_emails)->subject('Verfication Code');

                        $message->from('info@designcrm.net', config('app.name'));
                    });
                } catch (\Exception $e) {

                    $mail_error_data = json_encode([
                        'emails' => $sender_emails,
                        'body' => 'Your one time use Verfication code for email ' . auth()->user()->email . ' is ' . $bytes,
                        'error' => $e->getMessage(),
                    ]);
                    \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
                }

//                auth()->logout();
                return redirect()->route('salemanager.verify');
            }

            return $next($request);
        }

        return redirect()->back()->with("error","Access denied.");
    }
}
