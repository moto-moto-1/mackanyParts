<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
// use App\Http\Controllers\AuthController;



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
     *
     * protected $redirectTo = RouteServiceProvider::LOGIN;
     */
    // protected $redirectTo = response('Hello World', 200)
    // ->header('Content-Type', 'text/plain');

    //  public function redirectTo()
    //  {
    //     return response()->json(["redirect"=>"redirectifauth"]);
    //  }

    protected function registered(Request $request, $user)
    {
        if($user->email=$request->email){

            if($request['source']=='web'){
                // return view('landpage', ['jwt_token' => auth()->refresh(),'username'=>auth()->user()->name]);
                $newtoken=auth()->refresh();
                return response()->view('landpage', ['jwt_token' => $newtoken,'username'=>auth()->user()->name])
                ->cookie("jwt", $newtoken , 2, "/", 'mackany.com', true, true)
                ->cookie("username", $newtoken , 2, "/", 'mackany.com', true, true);
           
            }
            
         return response()->json([
                'jwtToken' => auth()->refresh(),
                'email' => auth()->user()->email,
                'name' => auth()->user()->name,
                'Telephone' => auth()->user()->telephone,
                'Address' => auth()->user()->address,
                'otherAddress' => auth()->user()->otheraddress,
                'otherTelephone' => auth()->user()->othertelephone ,
                'signedin'  => true            
                ]);  //new line
        
        
        }
        else return false;
        //
    }

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

        $error_messages = [
            'required'    => 'You must write your :attribute',
            'string'    => 'The :attribute must string',
            'max' => 'The :attribute must not exceed :max characters.',
            'min' => ' :attribute must  exceed :min characters.',
            'unique'      => 'we already have the same :attribute in our database',
            'email'      => ':attribute is not written correctly',
            'confirmed'      => 'the two :attribute does not match',
        ];
       
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'address' => ['string', 'max:255'],
            'telephone' => ['string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ],$error_messages);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'otheraddress' => $data['address'],
            'telephone' => $data['telephone'],
            'othertelephone' => $data['telephone'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
