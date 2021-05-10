<?php


namespace App\Http\Controllers\Auth\API;

use App\Models\PaymentMethod;
use App\Models\TMM_User;
use App\Notifications\UserChangePassword;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use \Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

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
            $user->assignRole('user');
            $wallet_method = PaymentMethod::where('slug','wallet')->first();
            $user->payment_methods()->attach($wallet_method);
            event(new Registered($user));
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
     * @OA\Post(
     * path="/api/access/login",
     *   tags={"Access"},
     *   summary="Login",
     *   operationId="login",
     *   description="return access token",
     *
     *   @OA\RequestBody(
     *    required=true,
     *    description="user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       @OA\Property(property="remember_me", type="boolean", example="true"),
     *    ),
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *    response=401,
     *    description="Unauthorized",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *    )
     *   )
     *)
     **/
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
     * @OA\Get(
     * path="/api/access/logout",
     *   tags={"Access"},
     *   summary="Logout",
     *   operationId="logout",
     *
     *  security={
     *      {"bearer_token": {}},
     *  },
     *  @OA\Response(
     *     response=401,
     *      description="Unauthenticated"
     *  )
     *)
     **/
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
     * Change user password
     * @param  [string] current_password
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);
        $user = $request->user();
        if(Hash::check($request->current_password, $user->password))
        {
            $user->forceFill([
                'password' => Hash::make($request->password)
            ])->setRememberToken(Str::random(60));
            $user->save();
            $user->notify(new UserChangePassword());

            return response()->json([
                'message' => 'Your password is changed successfully!'
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'Please enter correct current password'
            ],400);
        }
    }
}
