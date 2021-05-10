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
     * @OA\Get(
     * path="/api/user/my-profile",
     *   tags={"Users"},
     *   summary="My Profile",
     *   operationId="my-profile",
     *
     *  security={
     *      {"bearer_token": {}},
     *  },
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
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
