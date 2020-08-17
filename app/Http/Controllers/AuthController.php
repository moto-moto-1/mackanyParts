<?php

namespace App\Http\Controllers;
// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;


use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


use App\User;







class AuthController extends Controller
{
    
    use SendsPasswordResetEmails;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','forgotpassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        
        $credentials = request(['email', 'password']);
        $source = request('source');
        

        if (! $token = auth()->attempt($credentials)) {
            
            if($source=='web'){return redirect('/login')->with('message', 'email or password are incorrect');}
            else return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = auth()
            //  ->claims([
            // 'email' => auth()->user()->email,
            // 'name' => auth()->user()->name,
            // 'telephone' => auth()->user()->telephone,
            // 'address' => auth()->user()->address,
            // 'otheraddress' => auth()->user()->otheraddress,
            // 'othertelephone' => auth()->user()->othertelephone            
            // ])
            ->attempt($credentials);

            
            if($source=='web'){
                return redirect('/')
                ->cookie("jwt", $token , 20160, "/", 'mackany.com', true, true)
                ->cookie("username", Auth::user()->name ,20160, "/", 'mackany.com', true, true);
                // return response()->view('landpage', ['jwt_token' => $token,'username'=>auth()->user()->name])
                // ->cookie("jwt", $token , 20160, "/", 'mackany.com', true, true)
                // ->cookie("username", Auth::user()->name ,20160, "/", 'mackany.com', true, true);

                // return view('landpage', ['jwt_token' => $token,'username'=>auth()->user()->name]);
            }

            else return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        if( request('source')=='web'){
            return redirect('/login')->cookie("jwt", '' , 0, "/", 'mackany.com', true, true)
            ->cookie("username", '' ,0, "/", 'mackany.com', true, true);
        }

        return response()->json([
            'jwtToken' => "",
            
            'signedin'  => false
        ])->cookie("jwt", '' , 0, "/", 'mackany.com', true, true)
        ->cookie("username", '' ,0, "/", 'mackany.com', true, true);
    



        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        // return response()->json(['error' => 'Unauthorized'], 401);

        return $this->respondWithToken(auth()->refresh());
    }

    public function changeuserdata()
    {
        $data_to_change = request(['name', 'address', 'telephone','oldpassword','password','password_confirmation']);

        
        $this->validator1($data_to_change)->validate();
         
        $user=auth()->user();
        $user->fill([
            'name' => $data_to_change['name'],
            'address' => $data_to_change['address'],
            'telephone' => $data_to_change['telephone'],
            'password' => Hash::make($data_to_change['password']), 
        ]);
        $user->save();
        return $this->respondWithToken(auth()->login($user));

        // return response()->json(['signedin' => true]);
        
        }

        protected function validator1(array $data)
        {
            
            
            $error_messages = [
                'required'    => 'You must write your :attribute',
                'string'    => 'The :attribute must be string',
                'max' => 'The :attribute must not exceed :max characters.',
                'unique'      => 'we already have the same :attribute in our database',
                'confirmed'      => 'the two :attribute does not match',
                'min'      => ':attribute must not be less than 8 characters',
            ];
            $data['oldpassword_confirmation']='ASWE#@#$R1265WEW this is anything';
            if(Hash::check($data['oldpassword'], auth()->user()->password)){
              $data['oldpassword_confirmation']=$data['oldpassword'];}

            if(auth()->user()->telephone==$data['telephone']){$tele='min:6';}else{$tele='unique:users';}
           
            return Validator::make($data, [
                'name' => [ 'string', 'max:255'],
                'address' => ['string', 'max:255'],
                'telephone' => ['string', 'max:255', $tele],
                'oldpassword' => ['required', 'string', 'min:8', 'confirmed'],
                'password' => [ 'string', 'min:8', 'confirmed'],
                
            ],$error_messages);
    
        }
        public function forgotpassword()
        {
            
            $email = request(['email']);
            request()->validate(['email' => 'required|email']);

            $this->sendResetLinkEmail(request());

            return response()->json([
                'message' => 'e-mail sent successfuly with the new password',
                'jwtToken' => "",
                
                'signedin'  => false
            ]);


        }
        
    

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        // return $token;
        // dump(auth()->user()->name);
        // return $token;

        return response()->json([
            'jwtToken' => $token,
            'email' => auth()->user()->email,
            'name' => auth()->user()->name,
            'Telephone' => auth()->user()->telephone,
            'Address' => auth()->user()->address,
            'otherAddress' => auth()->user()->otheraddress,
            'otherTelephone' => auth()->user()->othertelephone ,
            'signedin'  => true
        ])->cookie("jwt", $token , 20160, "/", 'mackany.com', true, true)
        ->cookie("username", Auth::user()->name ,20160, "/", 'mackany.com', true, true);

        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'bearer',
        //     'expires_in' => auth()->factory()->getTTL() * 60
        // ]);
    }
}