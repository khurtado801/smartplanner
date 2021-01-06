<?php
namespace App\api;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Payment extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'cc_no','cc_name', 'cc_cvv',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ ];
}
