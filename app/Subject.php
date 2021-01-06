<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Requests;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Get the user that owns the phone.
     */
    public function lesson() {
        return $this->hasMany('App\Lesson');
    }

    public static function getSubjectsByGrade($grade_id) {
        //$grade_id = $request->grade_id;
        $subjects_selected = DB::table('grades_subjects')->where('grades_subjects.grade_id', '=', $grade_id)->lists('subject_id');

        $subjects = Subject::whereIn('id', $subjects_selected)
            ->orderby('name','ASC')->get();
        return $subjects;
    }

}
