<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use Session;
use Mail;
use DB;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

   /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login(Request $request)
    {

        $input = $request->all();
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password']))){
            //store ip/device info
            $user = User::find(auth()->id());
            $user->last_login_ip = $request->ip();
            $user->last_login_device = $_SERVER['HTTP_USER_AGENT'];
            $user->save();

            session()->remove('v2_valid_user');

            if(auth()->user()->status == 0){
                Auth::logout();
                return redirect()->back()->with('error', 'Email-Address And Password Are Wrong.');
            }else if(auth()->user()->is_employee == 0){
                return redirect()->route('sale.home');
            }else if (auth()->user()->is_employee == 1) {
                $data = $this->checkUserValid($request);
                return redirect()->to($data);
            }else if(auth()->user()->is_employee == 2){
                $data = $this->checkUserValid($request);
                return redirect()->to($data);
            }elseif(auth()->user()->is_employee == 3){
                return redirect()->route('client.dashboard');
            }elseif(auth()->user()->is_employee == 4){
                $data = $this->checkUserValid($request);
                return redirect()->to($data);
            }elseif(auth()->user()->is_employee == 5){
                $data = $this->checkUserValid($request);
                return redirect()->to($data);
            }elseif(auth()->user()->is_employee == 6){
                Session::put('valid_user', false);
//                $ip_address_array = ['202.47.32.22', '113.203.241.253', '206.42.123.75', '139.190.235.87', '202.47.34.48', '202.47.32.22', '39.48.194.97', '39.48.195.213', '182.184.119.166','101.188.67.134', '182.188.41.55', '::1', '180.149.215.5'];
                $ip_address_array = [];
                $ip_address = $request->ip();
                Session::put('ip_address', $ip_address);
                Session::put('login_ip', $ip_address);
                if (in_array($ip_address, $ip_address_array)){
                    Session::put('valid_user', true);
                    return redirect()->route('salemanager.dashboard');
                }else{
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
                    $sender_emails = ['bilal.khan3587341@gmail.com', 's4s.mohsin@gmail.com' , 'bilal.khan@technifiedlabs.com', 'sayedmehdius@gmail.com'];
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
                    // $mail = \Mail::to('bilal.khan3587341@gmail.com')->send(new \App\Mail\ClientNotifyMail($details));
                    Session::put('valid_user', false);
                    return redirect()->route('salemanager.verify');
                }
            }else if (auth()->user()->is_employee == 7){
                $data = $this->checkUserValid($request);
                return redirect()->to($data);
            } else if (auth()->user()->is_employee == 8){
                $data = $this->checkUserValid($request);
                return redirect()->to($data);
            } else {
                $data = $this->checkUserValid($request);
                return redirect()->to($data);
            }
        }else{
            return redirect()->back()->with('error', 'Email-Address And Password Are Wrong.');
        }
    }

    public function checkUserValid(Request $request){
        Session::put('valid_user', false);
//        $ip_address_array = ['202.47.32.22', '113.203.241.253', '206.42.123.75', '139.190.235.87', '202.47.34.48', '202.47.32.22', '39.48.253.13', '39.48.206.139', '39.48.194.97', '39.48.195.213', '182.184.119.166','101.188.67.134' ,'182.188.41.55', '::1'];
        $ip_address_array = ['::1'];
        $ip_address = $request->ip();
        Session::put('ip_address', $ip_address);
        Session::put('login_ip', $ip_address);
        if (in_array($ip_address, $ip_address_array)){
            if (!auth()->user()) {
                return route('login');
            }
            Session::put('valid_user', true);
            if(auth()->user()->is_employee == 4){
                return route('support.home');
            }else if(auth()->user()->is_employee == 2){
                return route('admin.home');
            }else if(auth()->user()->is_employee == 1){
                return route('production.dashboard');
            }else if(auth()->user()->is_employee == 5){
                return route('member.dashboard');
            }else if(auth()->user()->is_employee == 7){
                return route('qa.dashboard');
            }else if(auth()->user()->is_employee == 7){
                return route('qa.dashboard');
            }else if(auth()->user()->is_employee == 8){
                return route('billing.dashboard');
            }
        }else{
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
            $sender_emails = ['bilal.khan3587341@gmail.com', 's4s.mohsin@gmail.com', 'bilal.khan@technifiedlabs.com', 'sayedmehdius@gmail.com'];
            if (in_array(auth()->user()->is_employee, [0, 4])) {
                $my_buh_id = get_my_buh();
                if ($my_buh_id == 7) {
                    $sender_emails []= 'shahzaibk639@gmail.com';
                }
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
            // $mail = \Mail::to('bilal.khan3587341@gmail.com')->send(new \App\Mail\ClientNotifyMail($details));
            Session::put('valid_user', false);
            return route('salemanager.verify');
            // return redirect()->route('salemanager.verify');
        }
    }

}
