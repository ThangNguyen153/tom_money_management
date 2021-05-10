<?php
namespace App\Repositories\DailyUsage;
use App\Repositories\RepositoryInterface;
use App\Repositories\UsageType\UsageTypeRepository;

interface DailyUsageRepositoryInterface extends RepositoryInterface
{
    public function findUsage(UsageTypeRepository $usageTypeRepo,$attr='id',$value='');
    public function find($id);
}
