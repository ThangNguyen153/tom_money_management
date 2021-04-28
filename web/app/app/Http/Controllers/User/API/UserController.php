<?php
namespace App\Http\Controllers\User\API;

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

}
