<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Hash;
use DB;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['password'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    'password', 'remember_token',
    ];

    // ************* Check Site User Login *************
    public function authenticateSiteUser($mobile, $password)
    {
        $user = User::where('email', $mobile)->first();

        if (!isset($user->password) || !Hash::check($password, $user->password)) {
            return false;
        }
        return $user;
    }

    public static $CareTakerRules = array(
        'first_name' =>'required',
        'last_name' =>'required',
        'email' =>'required|email', 
        'password' =>'required|same:confirm_password',
        'confirm_password' =>'required|same:password'
    );
    
    public static function user_details($user_id){
        $user_details = DB::table('users as u')
                ->select('u.*')
                ->where('u.id', $user_id)
                ->where('u.status', 'Active')
                ->first();
        return $user_details;
    }
}
