<?php
namespace App\Repositories\PaymentMethod;
use App\Repositories\RepositoryInterface;

interface PaymentMethodRepositoryInterface extends RepositoryInterface
{
    public function findBy($attr = 'id',$value = '');
}
