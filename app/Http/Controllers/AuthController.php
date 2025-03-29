<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:225',
            'username' => 'required|string|max:225|unique:users', 
            'email' => 'required|string|email|unique:users', 
            'password' => 'required|string|min:6|confirmed' 
        ]);
        $verification_code=rand(100000,999999);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email, 
            'password' => Hash::make($request->password),
            'verification_code' => $verification_code,
        ]);

        Mail::raw("Your email verification code is: $verification_code", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email');
        });

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully', 
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request){
        $request->validate([
            'username'=>'required|string|max:225',
            'password' => 'required'
        ]);

        $user= User::where('username',$request->username)->first();

       

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message'=>'Invalid credentials',
            ],401);
        }
        $token = $user->createToken('authToken')->plainTextToken;
        
            return response()->json([
                'message'=>'User signed in successfully',
                'user'=>$user,
                'token'=>$token,

            ]);
    }

    public function logout(Request $request){
       $user=Auth::user();
       if(!$user){
        return response()->json([
            'message'=>'Unautheticated',
        ],401);
       }
       $user->tokens()->delete();
       return response()->json(['message' => 'Logged out successfully']);
    }

    public function forgotPassword(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $status= Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
        ? response()->json(['message'=>'Reset link sent'])
        : response()->json(['message'=>'Error sending reset link'],500);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'token'=>'required',
            'email'=>'required|email|exists:users,email',
            'password'=>'required|string|min:6|confirmed',
        ]);

        $status= Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function($user,$password){
                $user->forceFill([
                    'password'=>bcrypt($password)
                ])->save();
            }
        );
        return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Password reset successful!'])
        : response()->json(['error' => 'Invalid token'], 400);
       
    }

    public function changePassword(Request $request){
       
        $request->validate([
            'current_password'=>'required',
            'new_password'=>'required|min:6|string',
        ]);
        $user=auth()->user();

        if(!Hash::check($request->current_password,$user->password)){
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        $user->update(['password' => bcrypt($request->new_password)]);
        return response()->json(['message' => 'Password changed successfully!']);
    }
}
