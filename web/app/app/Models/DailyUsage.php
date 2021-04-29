<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyUsage extends Model
{
    use HasFactory;
    protected $table = 'daily_usages';
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'paymentmethod_id',
        'usagetype_id',
        'paid',
        'extra',
        'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(TMM_User::class,'user_id','id');
    }
    public function payment_method(){
        return $this->belongsTo(PaymentMethod::class,'paymentmethod_id','id');
    }
    public function usage_type(){
        return $this->belongsTo(UsageType::class,'usagetype_id','id');
    }
}
