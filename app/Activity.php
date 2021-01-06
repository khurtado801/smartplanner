<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Activity extends Model {
    //
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Activity many relationship to lessonSequence
     */
    public function lessonSequence() {
        return $this->hasMany('App\LessonSequence');
    }

    /**
     * 
     */
    public function mapactivity() {
        return $this->hasMany('App\BloomWebActivity', 'activity_id');
    }

    // public static function getAllActivities() {
              // set activities table, select all id-name-description-status
    //           $activityDetails = DB::table('activity')
    //           ->select('id', 'name', 'description', 'status')
    //           ->orderBy('id', 'ASC')
    //           ->where('status', 'Active')
    //           ->get();

    //           return $activityDetails;
    // }

    /**
     * Get activty_id by bloom_id and webb_id
     */
    public static function getActivitiesByBloomAndWebb(Request $request) {
        $activitiesPosts = DB::table('full_sequence as fs')
            ->join('activity as act', 'fs.activity_id', '=', 'act.id')
            ->select('fs.activity_id', 'act.name', 'act.description')
            ->distinct()
            ->orderBy('act.name', 'ASC')
            ->where('fs.bloom_id', '=', $request->bloom_id)
            ->where('fs.webb_id', '=', $request->webb_id)
            ->get();

        // echo "<pre>";
        // print_r($activitiesPosts);
        // exit;

        return $activitiesPosts;
    }
}
