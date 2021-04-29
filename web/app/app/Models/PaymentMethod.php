<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';
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

    public function users(){
        return $this->belongsToMany(TMM_User::class,'user_paymentmethod','user_id','paymentmethod_id');
    }

    public function daily_usages(){
        return $this->hasMany(DailyUsage::class,'paymentmethod_id','id');
    }
}
