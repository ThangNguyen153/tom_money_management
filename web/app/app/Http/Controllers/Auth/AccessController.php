<?php


namespace App\Http\Controllers\Auth;

use App\Models\TMM_User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccessController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] username
     * @param  [string] firstname
     * @param  [string] lastname
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        $user_info = array(
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email_verified_at' => 20210412
        );
        $firstname ='';
        $lastname ='';
        if(isset($request->firstname)){
            $user_info['firstname'] = $request->firstname;
            $firstname = $request->firstname;
        }
        if(isset($request->lastname)){
            $user_info['lastname'] = $request->lastname;
            $lastname = $request->lastname;
        }
        $user_info['fullname'] = $firstname . ' ' . $lastname;
        $user = new TMM_User($user_info);
        if($user) {
            $user->save();
            return response()->json([
                'message' => 'Successfully created user!'
            ], 201);
        }else{
            return response()->json([
                'message' => 'Something goes wrong during registeration.'
            ], 500);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function getMyProfile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Get the authenticated User
     * @param  [string] email
     * @return [string] message
     */
    public function deleteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        $email =$request->input('email');
        $user = TMM_User::where('email',$email)->first();
        if($user){
            $user->delete();
            return response()->json([
                'message' => 'Successfully deleted out'
            ]);
        }else{
            return response()->json([
                'message' => 'User not found'
            ],404);
        }
    }

}
