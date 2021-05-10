<?php
namespace App\Http\Controllers\User\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TMM_User\TMM_UserRepositoryInterface;
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

}
