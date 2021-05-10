<?php
namespace App\Http\Controllers\Admin\API;

use App\Models\TMM_User;
use App\Repositories\TMM_User\TMM_UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @var TMM_UserRepositoryInterface
     */
    protected $userRepo;

    public function __construct(TMM_UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
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
     * @param [string] email
     * @return [string] message
     */
    public function deleteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        $email =$request->input('email');
        $user = $this->userRepo->findBy('email',$email);
        if($user){
            // delete all user's payment methods
            $user->payment_methods()->detach();
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

    /**
     * Get the authenticated User
     * @param [string] attr
     * @param [string] value
     * @return [json] user object
     */
    public function findUserBy(Request $request)
    {
        $request->validate([
            'attr' => 'string|min:3|max:50',
            'value' => 'string|min:3|max:50',
        ]);
        $attr = 'id';
        $value = '';
        if(isset($request->attr))
            $attr = $request->attr;
        if(isset($request->value))
            $value = $request->value;

        return $this->userRepo->findBy($attr,$value);
    }
}
