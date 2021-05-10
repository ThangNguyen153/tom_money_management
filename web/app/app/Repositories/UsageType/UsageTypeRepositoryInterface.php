<?php

namespace App\Repositories\UsageType;
use App\Repositories\RepositoryInterface;
interface UsageTypeRepositoryInterface extends RepositoryInterface
{
    public function getAll();
    public function findBy($attr = 'id',$value = '');
}
