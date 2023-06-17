<?php

namespace App\Http\Controllers\Auth;

use Str;
use mail;
use Carbon\Carbon;
use App\Models\User;
use App\Models\verifyUser;
use App\Http\Controllers\Controller;
//custom
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // return Validator::make($data, [
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => ['required', 'string', 'min:8', 'confirmed'],
        // ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }


    public function register(Request $request){
        $request->validate([
            'name'=>['required','string','max:255'],
            'email'=>['required','email','string','max:255','unique:users'],
            'password'=>['required','min:8','max:150','confirmed']
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $save = $user->save();

        //last id 
        $lastId = $user->id;
        //token
        $token = $lastId.hash('sha256', \Str::random(120));
        $verifyUrl = route('verifyUser',['token'=>$token, 'service'=>'Email_verification']);

        //verifyUser insert id information
        verifyUser::create([
            'user_id'=>$lastId,
            'user_email'=>$request->email,
            'token'=>$token,
        ]);

        //email
        $fromEmail = 'noreplay@darkengineers.org';
        $fromName = 'Dark Engineers';
        $message = "Dear <b>".$request->name.'</b>';
        $message.= 'Thanks for signin up, we just need you to verify your email address to complete setting up your account.';
        $mail_data = [
            'recipient'=>$request->email,
            'fromEmail'=>$fromEmail,
            'fromName'=>$fromName,
            'subject'=>'Email Verification from Dark Engineers',
            'body'=>$message,
            'actionLink'=>$verifyUrl,
        ];

        mail::send('mail.register-usr',$mail_data, function($message) use($mail_data){
            $message->to($mail_data['recipient'])
                    ->from($mail_data['fromEmail'], $mail_data['fromName'])
                    ->subject($mail_data['subject']);
        });

        if($save){
            return redirect()->back()->with('regSucc','please verify your Email, we sent activition link');
        }else{
            return redirect()->back()->with('regError','we got some error to register your data');
        }

    }


    public function verifyUser(){
        return 'ok';
    }
}
