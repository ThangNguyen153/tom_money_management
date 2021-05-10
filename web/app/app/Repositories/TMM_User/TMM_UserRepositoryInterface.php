<?php

namespace App\Repositories\TMM_User;
use App\Repositories\RepositoryInterface;

interface TMM_UserRepositoryInterface extends RepositoryInterface
{
    public function findBy($attr = 'id', $value ='');
}
