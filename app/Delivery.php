<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Delivery extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Lesson Delivery many relationship to lessonSequence
     */
    public function lessonSequence() {
        return $this->hasMany('App\LessonSequence');
    }

    /**
     * Map
     */
    public function maplessondelivery() {
        return $this->hasMany('App\FullSequence', 'delivery_id');
    }

    public static function getAllDelivery() {
        $lessonDeliveryDetails  = DB::table('delivery')
        ->select('id', 'name', 'status')
        ->orderBy('id', 'ASC')
        ->where('status', 'Active')
        ->get();

        return $lessonDeliveryDetails;
    }

    public static function getDeliveriesByBloomWebbActivity(Request $request) {
        $deliveriesPost = DB::table('full_sequence as fs')
            ->join('delivery as dlv', 'fs.delivery_id', '=', 'dlv.id')
            ->select('fs.delivery_id', 'dlv.name')
            ->distinct()
            ->orderBy('dlv.name', 'ASC')
            ->where('fs.bloom_id', '=', $request->bloom_id)
            ->where('fs.webb_id', '=', $request->webb_id)
            ->where('fs.activity_id', '=', $request->activity_id)
            ->get();

            return $deliveriesPost;
    }

}