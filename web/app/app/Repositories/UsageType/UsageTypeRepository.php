<?php
namespace App\Repositories\UsageType;

use App\Repositories\BaseRepository;
use App\Repositories\UsageType\UsageTypeRepositoryInterface;

class UsageTypeRepository extends BaseRepository implements UsageTypeRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\UsageType::class;
    }
    public function getAll()
    {
        return $this->model->select(array('name','slug','description'))->get();
    }
    public function findBy($attr = 'id',$value = ''){
        return $this->model->where($attr,$value)->first();
    }
}
