<?php

namespace App\Http\Controllers\APIS\AUTH;

use App\Http\Controllers\Controller;
use App\Mail\forget_password_Otp;
use App\Mail\Send_otp_VerifyCode;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function getallusers()
    {
        $data = User::get();
    return response()->json([
        'status' => 'success',        
        'data' => $data
    ]);

    }
 
 
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'phone_no' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            
        ]);
        if($validator->fails()){
           
            return response()->json([
            
                'status' =>'failure', 
                  'data'  =>  'Failure sss'
            ]);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['name' => $request->name,
                     'email' => $request->email,
                     'verify_code' => random_int(10000, 99999),
                     'phone_no' => $request-> phone_no,
                      //'password' => bcrypt($request->password),
                    'password' => Hash::make($request->password),
                    ]
                ));
                if($user == true){
                Mail::to($request->email)->send(new Send_otp_VerifyCode($user));
                
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }
    }


    public function login(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        
    ]);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    /**  login without userapprove /(login in flutter) بال  user_approve',1 بحط ال  */
     //$user = User::where('email', $request->email)->where('user_approve',1)->first();
       
        $user = User::where('email', $request->email)->first();  
         if ($user && Hash::check($request->password, $user->password)) {
    
            return response()->json(
                [  
                    'status' => 'success',   
                    'data'  =>  $user
                ]);
        }
        else{
            return response()->json([
                'status' =>'failure',
                  'data'  =>  $user
            ]); 
        }
   
    }



    public function loginWithOtp(Request $request){ //verfy code
       
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'verify_code' => 'required',
        ]);

         if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
       
       
        Log::info($request);
        $user  = User::where([['email','=',request('email')],['verify_code','=',request('verify_code')]])->first();
       
        
       
       if($user){
            Auth::login($user, true);
            User::where('email','=',$request->email)->update(['user_approve'=>'1', 'verify_code' => request('verify_code')]);
            
            $user->refresh();
            return response()->json([
                
                'status' =>'success',
                'data' => $user
            ]);
       
       }
       
        else{
          return response()->json([
            'status' =>'failure',
            'data' => $user 
        ]);
        }

              
    }



          // send OTP Message to Email Address {Check Email}
          public function resendOtp(Request $request){

            $validator = Validator::make($request->all(), [
                'email' => 'required|email', //exists:users:if email not registered yet
            ]);
    
             if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            
            
            $otp = random_int(10000,99999);
            Log::info("verify_code = ".$otp);
           
            $users = User::where('email','=',$request->email)->first();
           
            if($users){
           
            $users->update(['verify_code'=>$otp]);
            $users->refresh();
               
           
                Mail::to($request->email)->send(new forget_password_Otp($users));
               
                return response()->json(
                            [
                                'status' => 'success', 
                                'data' => $users
                            ]
                         );
                         } else{
                return response()->json(
                    [
                        'status' => 'failure', 
                       // 'data' => $users
                    ]
                 );
    
            }
        }





            /***************************Reset password // after otp veify***************************************** */ 

 public function reset(Request $request)
 {
     $validator = Validator::make($request->all(), [
         'email' => 'required|email|exists:users',
         'password' => 'required|min:5',
     ]);
 
     if($validator->fails()){
         return response()->json($validator->errors()->toJson(), 400);
     }
 
     $users = User::where('email','=',$request->email)->first();
     $users->update(['password'=>bcrypt($request->password)]);
     return response()->json(
         [
             'status' => 'success', 
             'data' => $users
         ]
      );
 }
 /***************************Reset password // after otp veify***************************************** */ 
}
