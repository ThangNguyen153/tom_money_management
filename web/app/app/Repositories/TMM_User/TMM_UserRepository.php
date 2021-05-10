<?php
namespace App\Repositories\TMM_User;

use App\Models\TMM_User;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;

class TMM_UserRepository extends BaseRepository implements TMM_UserRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\TMM_User::class;
    }

    public function findBy($attr = 'id', $value ='')
    {
        return $this->model->where($attr,$value)->first();
    }
}
