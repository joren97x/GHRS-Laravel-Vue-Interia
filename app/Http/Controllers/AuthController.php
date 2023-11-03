<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    //

    public function logout(Request $request) {
        auth()->logout();
        $request->session()->invalidate();
        return back()->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Cache-Control', 'post-check=0, pre-check=0')
        ->header('Pragma', 'no-cache');
    }

    public function login() {
        if(!auth()->user()) {
            return Inertia::render('Auth/Login');
        }
        return back();
    }

    public function create_guest() {
        return Inertia::render('Auth/CreateGuest');
    }

    public function create_owner() {
        return Inertia::render('Auth/CreateOwner');
    }

    public function forgot_password() {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function verify() {
        return Inertia::render('Auth/Verify');
    }

    public function authenticate(Request $request) {
        // $form = $request->validate([
        //     'email' => ['required', 'email'],
        //     'password' => 'required'
        // ]);

        $request->session()->regenerate();
        $user = User::where('email',$request->email)->first();
        if($user) {
            auth()->login($user);
        }
        else {
            return back()->withErrors(['loginError' => 'Invalid creadentials']);
        }
        switch(auth()->user()->role) {
            case 'admin':
                return redirect('/admin/dashboard');
                break;
            case 'owner':
                return redirect('/owner/dashboard');
                break;
            case 'guest':
                return redirect('/');
                break;
        }
        return redirect('/');
    }

    public function storeUser(Request $request) {
        $form = $request->validate([
            'firstname' => ['required', 'min:3'],
            'lastname' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'phone_number' => 'required'
        ]);
        //hash password
        $form['role'] = 'user';
        $form['password'] = bcrypt($form['password']);
        $user = User::create($form);
        $user->profile_pic = "default_profile.png";
        $user->save();
        // auth()->login($user);
        return redirect('/login');
    }

    public function storeOwner(Request $request) {
        $form = $request->validate([
            'firstname' => ['required', 'min:3'],
            'lastname' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'phone_number' => 'required'
        ]);
        //hash password
        $form['role'] = 'owner';
        $form['password'] = bcrypt($form['password']);
        dd($form);
        $user = User::create($form);
        $user->profile_pic = "default_profile.png";
        $user->save();
        // auth()->login($user);
        return redirect('/login');
    }

    public function sendVerificationCode(Request $request) {
        return redirect()->back();
        // $sid = getenv("TWILIO_ACCOUNT_SID");
        // $token = getenv("TWILIO_AUTH_TOKEN");

        // $twilio = new Client($sid, $token);

        // $verification = $twilio->verify->v2->services("VA678c4c11b87e1cd19c25641424aa402a")
        // ->verifications
        // ->create("+639168290756", "sms");
        // return redirect()->back();
    } 

    public function verifyVerificationCode(Request $request) {
        // dd($request->code);
        dd(auth()->user());
        // $sid = getenv("TWILIO_ACCOUNT_SID");
        // $token = getenv("TWILIO_AUTH_TOKEN");
        // $twilio = new Client($sid, $token);

        // try {
        //     $verification_check = $twilio->verify->v2->services("VA678c4c11b87e1cd19c25641424aa402a")
        //     ->verificationChecks
        //     ->create([
        //                 "to" => "+639168290756",
        //                 "code" => $request->code
        //             ]
        //     );
                $user = auth()->user();
                $user->phone_number_verified = true;
                $user->update();

        // }
        // catch(Exception $e) {
        //     return back()->withErrors(['badVerificationCode' => 'Invalid verification code']);
        // }

    }

}
