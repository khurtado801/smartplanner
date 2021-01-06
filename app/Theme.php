<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Requests;
use DB;
use App\GradesSubjectsTheme;
use Illuminate\Database\Eloquent\SoftDeletes;

class Theme extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Get the user that owns the phone.
     */
    public function lesson()
    {
        return $this->hasMany('App\Lesson');
    }
    /**
     * Get the phone record associated with the user.
     */
    public function maptheme()
    {
        return $this->hasMany('App\GradesSubjectsTheme', 'theme_id');
    }

    public static function getThemesByGradeAndSubjects($grade_id, $subject_id)
    {

        $themes_selected = DB::table('grades_subjects_themes')->where('grades_subjects_themes.subject_id', '=', $subject_id)->where('grades_subjects_themes.grade_id', '=', $grade_id)->lists('theme_id');

        $posts = Theme::join('grades_subjects_themes as gst', 'gst.theme_id', '=', 'themes.id')
            ->where('gst.grade_id', $grade_id)
            ->where('gst.subject_id', $subject_id)
            ->select('gst.theme_id', 'themes.name')
            ->get();

        // echo "<pre>";
        // print_r($posts);
        // exit;

        return $posts;
    }
}
