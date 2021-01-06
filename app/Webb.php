<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Webb extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * 
     */
    public function lessonSequence() {
        return $this->hasMany('App\LessonSequence');
    }


    /**
     * Get Webb level associated with Bloom level
     */
    public static function getWebbByBloom() {
        // $webbs_selected = DB::table('blooms_webbs')
        // ->where('blooms_webbs.bloom_id', '=', $bloom_id)
        // ->lists('webb_id');

        // $webbs = Webb::wherein('id', $webbs_selected)
        // ->orderby('level', 'ASC')->get();
        // return $webbs;

        // $webbs_selected = $bloom_id;

        // $webbs_selected = DB::table('webbs as wb')
        // ->join('blooms_webbs as bw', 'bw.webb_id', '=', 'wb.id')
        // ->join('blooms as bl', 'bl.id', '=', 'bw.bloom_id')
        // ->select('wb.level as webb_level', 'wb.id as webb_id', 'wb.description as webb_description', 'bl.id as bloom_id', 'bl.name as bloom_name', 'bl.description as bloom_description')
        // ->where('bw.bloom_id', '=', $bloom_id)
        // ->get();

        // ! Get all bloom and respective webb levels-working
        $webbs_selected = DB::table('blooms_webbs as blmswbs')
        ->join('blooms as blms', 'blms.id', '=', 'blmswbs.bloom_id')
        ->join('webbs as wbs', 'wbs.id', '=', 'blmswbs.webb_id')
        ->select('blms.id as bloom_id', 'wbs.id as webb_id', 'wbs.level as webb_level', 'wbs.name as webb_name','wbs.description as webb_description')
        ->get();

        // $webbs_selected = DB::table('blooms_webbs as blmswbs')
        // ->join('blooms as blms', 'blms.id', '=', 'blmswbs.bloom_id')
        // ->join('webbs as wbs', 'wbs.id', '=', 'blmswbs.webb_id')
        // ->select('blms.id as bloom_id', 'blms.name as bloom_name', 'blms.description as bloom_description, wbs.id as webb_id', 'wbs.level as webb_level', 'wbs.description as webb_description')




        return $webbs_selected;
    }
}
