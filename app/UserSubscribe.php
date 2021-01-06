<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class UserSubscribe extends Model {

    protected $table = 'user_subscribe';

    public static function getAgreementId($user_id) {

        $renewal = DB::table('user_subscribe as us')
                ->select('us.*')
                ->where('us.user_id', $user_id)
                ->where('us.status', 'Active')
                //->update(['us.status' => 'Inactive','us.updated_at' => date('Y-m-d')]);
                ->first();
        return $renewal;
    }

}
