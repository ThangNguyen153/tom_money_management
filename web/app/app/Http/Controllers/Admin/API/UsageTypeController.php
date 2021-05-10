<?php


namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\UsageType;
use App\Repositories\UsageType\UsageTypeRepositoryInterface;
use Illuminate\Http\Request;

class UsageTypeController extends Controller
{
    /**
     * @var UsageTypeRepositoryInterface
     */
    protected $usageTypeRepo;

    public function __construct(UsageTypeRepositoryInterface $usageTypeRepo)
    {
        $this->usageTypeRepo = $usageTypeRepo;
    }
    /**
     * Get list of usage types
     *
     * @return [json] user object
     */
    public function getUsageTypes()
    {
        return $this->usageTypeRepo->getAll();
    }
}
