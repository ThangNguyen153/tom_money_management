<?php

namespace App\Repositories\PaymentMethod;
use App\Repositories\BaseRepository;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\PaymentMethod::class;
    }
    public function findBy($attr = 'id',$value = ''){
        return $this->model->where($attr,$value)->first();
    }
}
