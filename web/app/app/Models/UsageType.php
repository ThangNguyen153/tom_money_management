<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageType extends Model
{
    use HasFactory;
    protected $table = 'usage_types';
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    public function daily_usages(){
        return $this->hasMany(DailyUsage::class,'usagetype_id','id');
    }
}
