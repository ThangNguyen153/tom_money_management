<?php
namespace App\Http\Controllers\Auth;

use http\Env\Response;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\TMM_User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = TMM_User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return redirect('/');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return redirect('/');
    }

    public function resendVerificationEmail(Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json(["msg" => "Verification link is sent to your email"]);
    }

    /**
     * Send password reset link
     * @param  [string] email
     * @return [string] message
     */
    public function resendResetPasswordEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        $email =$request->input('email');
        $user = TMM_User::where('email',$email)->first();
        if($user){
            $status = Password::sendResetLink(
                $request->only('email')
            );
            return $status === Password::RESET_LINK_SENT
                ? response()->json([
                    'message' => 'Reset password link is sent to your email'
                ])
                : response()->json([
                    'message' => 'Something goes wrong, please try it again!'
                ],500);
        }else{
            return response()->json([
                'message' => 'User not found'
            ],404);
        }
    }

    /**
     * Send password reset link
     * @param  [string] token
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json([
                'message' => 'Password reset successfully'
            ])
            : response()->json([
                'message' => 'Something goes wrong, please try it again!'
            ],500);
    }
}
