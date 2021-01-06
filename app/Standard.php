<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Standard extends Model {
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
        return $this->hasMany('App\FullSequence', 'beyond_id');
    }

    public static function getStandardsByDelivery(Request $request) {
        $standardsPost = DB::table('full_sequence as fs')
            ->join('standard as std', 'fs.beyond_id', '=', 'std.id')
            ->select('fs.beyond_id', 'std.name')
            ->distinct()
            ->orderBy('std.name', 'ASC')
            ->where('fs.bloom_id', '=', $request->bloom_id)
            ->where('fs.webb_id', '=', $request->webb_id)
            ->where('fs.activity_id', '=', $request->activity_id)
            ->where('fs.delivery_id', '=', $request->delivery_id)
            ->get();

            return $standardsPost;
    }




}