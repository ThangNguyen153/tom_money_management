<?php

namespace App\Repositories\DailyUsage;
use App\Models\UsageType;
use App\Repositories\BaseRepository;
use App\Repositories\UsageType\UsageTypeRepository;

class DailyUsageRepository extends BaseRepository implements DailyUsageRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\DailyUsage::class;
    }
    public function findUsage(UsageTypeRepository $usageTypeRepo,$attr='id',$value=''){
        return $usageTypeRepo->findBy($attr,$value);
    }
    public function find($id)
    {
        return $this->model->where('id',$id)->first();
    }
}
