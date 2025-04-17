<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class IsSale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $bytes = bin2hex(random_bytes(3));
        DB::table('users')
            ->where('id', auth()->user()->id)
            ->update(['verfication_code' => $bytes, 'verfication_datetime' => date('Y-m-d H:i:s')]);

        $details = [
            'title' => 'Verfication Code',
            'body' => 'Your one time use Verfication code for email ' . auth()->user()->email . ' is ' . $bytes
        ];

        $sender_emails = ['bilal.khan3587341@gmail.com', 's4s.mohsin@gmail.com', 'sayedmehdius@gmail.com'];
        if (auth()->id() == 7) {
            $sender_emails []= 'shahzaibk639@gmail.com';
        }

        try {
            $newmail = Mail::send('mail', $details, function($message) use ($bytes, $sender_emails){
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
        Session::put('valid_user', false);
        return redirect()->route('salemanager.verify');

        return redirect()->back()->with("error","You don't have emplyee rights.");

    }
}
