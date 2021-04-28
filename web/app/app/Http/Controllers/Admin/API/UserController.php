<?php
namespace App\Http\Controllers\Admin\API;

use App\Models\TMM_User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

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
     * @param [string] email
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
                'message' => 'Successfully deleted user'
            ]);
        }else{
            return response()->json([
                'message' => 'User not found'
            ],404);
        }
    }
}
