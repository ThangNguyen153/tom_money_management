<?php


namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\UsageType;
use Illuminate\Http\Request;

class UsageTypeController extends Controller
{
    /**
     * Get list of usage types
     *
     * @return [json] user object
     */
    public function getUsageTypes(Request $request)
    {
        return response()->json(UsageType::select(array('name','slug','description'))->get());
    }
}
