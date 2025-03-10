<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;
use DB;
use Mail;

class IsSaleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->user()->is_employee == 6){
            $ip_address = $request->ip();
            $current_ip = Session::get('login_ip');
            $ip_address_array = ['202.47.32.22', '113.203.241.253', '206.42.123.75', '139.190.235.87', '202.47.34.48', '202.47.32.22', '39.48.206.139', '182.184.119.166', '103.255.151.65', '180.149.215.8', '180.149.215.9', '202.47.37.103', '202.47.35.103', '180.149.215.5'];
            if($current_ip != null){
                array_push($ip_address_array, $current_ip);
            }
            Session::put('ip_address', $ip_address);
            $valid_user = Session::get('valid_user');
            if($valid_user == true){
                if (in_array($ip_address, $ip_address_array)){
                    Session::put('valid_user', true);
                }else{
                    $valid_user = Session::get('valid_user');
                    if($valid_user == true){
                        Session::put('valid_user', true);
                    }else{
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
                    }
                }
            }else{
                $bytes = bin2hex(random_bytes(3));
                DB::table('users')
                ->where('id', auth()->user()->id)
                ->update(['verfication_code' => $bytes, 'verfication_datetime' => date('Y-m-d H:i:s')]);

                $details = [
                    'title' => 'Verfication Code',
                    'body' => 'Your one time use Verfication code for email ' . auth()->user()->email . ' is ' . $bytes
                ];

                $sender_emails = ['bilal.khan3587341@gmail.com', 'sayedmehdius@gmail.com'];
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
                        'emails' => ['bilal.khan3587341@gmail.com', 'sayedmehdius@gmail.com'],
                        'body' => 'Your one time use Verfication code for email ' . auth()->user()->email . ' is ' . $bytes,
                        'error' => $e->getMessage(),
                    ]);

                    \Illuminate\Support\Facades\Log::error('MAIL FAILED: ' . $mail_error_data);
                }
                Session::put('valid_user', false);
            }
            return $next($request);
        }

        return redirect()->back()->with("error","You don't have admin access.");
    }
}
