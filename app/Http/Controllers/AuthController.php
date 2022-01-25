<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Jobs\InvitationJob;
use App\Jobs\RegistrationPinJob;
use App\Models\VerificationCode;

class AuthController extends Controller

{
    
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['user_name', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request){
       
        $userId = Auth::id();
        $user = User::find($userId);

        if($request->name && $request->user_name && $request->email){
            $request->validate( [
                'name' => ['string', 'max:255'],
                'user_name' => ['string','min:4', 'max:20'],
                'email'=>['email']
                
            ]);

            $user->name = $request->name;
        }

        if($request->name){
            $request->validate( [
                'name' => ['string', 'max:255']
                
            ]);

            $user->name = $request->name;
        }

        if($request->user_name){
            $request->validate( [
                'user_name' => ['string','min:4' ,'max:20']
            ]);
            $user->user_name = $request->name;
        }
        
        if($request->email){
            $request->validate([
                'email'=>['email']
            ]);

            $user->email = $request->email;
        }

        if($request->avatar){
            // delete user profile if exist
            $user->unlink_profile();


            $avatar = $request->file('avatar');
            $user->link_profile($avatar);
            }

        $user->save();
        return Response()->Json([
            "message"=>"Account updated Successfully!"
        ]);

    }

    public function registeration_form(Request $request){
        $email = $request->email;
        return view('registration.registration_form')->with(['email'=>$email]);
    }


    public function generateUniqueCode()
    {
       
        $code = random_int(100000000, 999999999);
        return $code;
    }
    public function confirm_registration(Request $request){

        $request->validate([
            'user_name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required|confirmed'
        ]);

        $verification = VerificationCode::where('email',$request->email)->first();
        if($verification){
            $verification->delete();
        }
           

        $verification =new  VerificationCode();
        $verification->code = $this->generateUniqueCode();
        $verification->user_name = $request->user_name;
        $verification->email = $request->email;
        $verification->password = bcrypt($request->password);


        $verification->save();
        dispatch(new RegistrationPinJob($verification->code,$verification->email));


        return Response()->Json([
            "message"=>"We have sent you a 9 digit registration pin to your email!"
        ],200);


    }

   

    public function verify_code(Request $request){
      $code = $request->code;

      if(!$code){
          return Response()->Json(["message"=>"Invalid verification code"]);
      }

      $verification = VerificationCode::where('code',$code)->first();

      if($verification){
          $user = new User();
          $user->user_name = $verification->user_name;
          $user->email = $verification->email;
          $user->password = $verification->password;
          $user->user_role = "user";
          $user->save();

        //   set created time
          $user->registered_at = $user->created_at;
          $user->save();

          $verification->delete();

          return Response()->Json([
              "message"=>"User registered successfully"
          ]);
      }
      return Response()->Json([
          "message"=>"Invalid code"
      ]);
    }




    public function send_invite(Request $request){
      
        if(!$request->email){
            return Response()->Json([
                "message"=>"Please provide email to send invite too"
            ],400);
        }


        $user = User::where('email',$request->email)->first();
       
        if($user){
            return Response()->Json([
                "message"=>"User with this email $request->email already exist"
            ]);
        }

        $message = "Please click on button to register";
        $email = $request->email;

        $url = route('register',['email'=>$email]);
            $details = [
                'title'=>'Registration',
                'body'=>$message,
                'email'=>$email
            ];
    
        
    
        
        dispatch(new InvitationJob($details,$url));

        return Response()->Json([
            "message"=>"Invitation sent successfully"
        ],200);
    }



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

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
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
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
