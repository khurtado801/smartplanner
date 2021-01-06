<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonSequence extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Bloom record associated with webb
     */
    public function bloom() {
        return $this->belongsToMany('App\Bloom');
    }

    /**
     * Webb record associated with bloom
     */
    public function webb() {
        return $this->belongsToMany('App\Webb');
    }

    /**
     * Activity record
     */
    public function activity() {
        return $this->belongsToMany('App\Activity');
    }

    /**
     * Delivery record
     */
    public function delivery() {
        return $this->belongsToMany('App\Delivery');
    }

    /**
     * Beyond the Standards record
     */
    public function beyondStandards() {
        return $this->belongsToMany('App\BeyondStandards');
    }

    /**
     * Modifications
     */
    public function modifications() {
        return $this->belongsToMany('App\Modifications');
    }

    // function getSequenceSummary($lession_id) {
    //     $summary_list = DB::table('lesson_sequence as ls')
    //     ->join('lesson_sequence')
    // }


}