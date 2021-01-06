<?php

namespace App;

//use DB;
use Illuminate\Database\Eloquent\Model;
use File;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Get the phone record associated with the user.
     */
    public function grade() {
        return $this->belongsTo('App\Grade');
    }

    /**
     * Get the phone record associated with the user.
     */
    public function subject() {
        return $this->belongsTo('App\Subject');
    }

    /**
     * Get the phone record associated with the user.
     */
    public function theme() {
        return $this->belongsTo('App\Theme');
    }

    function getSummary($lession_id) {
        $summary_list = DB::table('learningtargets as lt')
                ->join('learningtargets_name as lt_name', 'lt.learningtargetsName_id', '=', 'lt_name.id')
                ->select('lt.id', 'lt_name.name', 'lt.overview_summary')
                ->join('map_users_targets as mut', 'mut.targetids', '=', 'lt.id')
                ->where('mut.ulessons_id', $lession_id)
                ->where('mut.type', "summary")
                ->where('lt.status', 'Active')
                ->get();
        return $summary_list;
    }

    function getStandard($lession_id) {
        $standard_list = DB::table('learningtargets as lt')
                ->join('learningtargets_name as lt_name', 'lt.learningtargetsName_id', '=', 'lt_name.id')
                ->select('lt.id', 'lt_name.name', 'lt.standards')
                ->join('map_users_targets as mut', 'mut.targetids', '=', 'lt.id')
                ->where('mut.ulessons_id', $lession_id)
                ->where('mut.type', "standard")
                ->where('lt.status', 'Active')
                ->get();
        //print_r($standard_list);
        return $standard_list;
    }

    function getEssaential($lession_id) {
        $essential_list = DB::table('learningtargets as lt')
                ->join('learningtargets_name as lt_name', 'lt.learningtargetsName_id', '=', 'lt_name.id')
                ->select('lt.id', 'lt_name.name', 'lt.essential_questions')
                ->join('map_users_targets as mut', 'mut.targetids', '=', 'lt.id')
                ->where('mut.ulessons_id', $lession_id)
                ->where('mut.type', "essential")
                ->where('lt.status', 'Active')
                ->get();
        return $essential_list;
    }

    function getCoreideas($lession_id) {
        $coreideas_list = DB::table('learningtargets as lt')
                ->join('learningtargets_name as lt_name', 'lt.learningtargetsName_id', '=', 'lt_name.id')
                ->select('lt.id', 'lt_name.name', 'lt.core_ideas')
                ->join('map_users_targets as mut', 'mut.targetids', '=', 'lt.id')
                ->where('mut.ulessons_id', $lession_id)
                ->where('mut.type', "coreideas")
                ->where('lt.status', 'Active')
                ->get();
        return $coreideas_list;
    }

    function getVocabulary($lession_id) {
        $vocabulary_list = DB::table('learningtargets as lt')
                ->join('learningtargets_name as lt_name', 'lt.learningtargetsName_id', '=', 'lt_name.id')
                ->select('lt.id', 'lt_name.name', 'lt.academic_vocabulary')
                ->join('map_users_targets as mut', 'mut.targetids', '=', 'lt.id')
                ->where('mut.ulessons_id', $lession_id)
                ->where('mut.type', "vocabulary")
                ->where('lt.status', 'Active')
                ->get();

        return $vocabulary_list;
    }

}
