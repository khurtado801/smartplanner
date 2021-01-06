<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\Grade;
use App\Subject;
use App\Theme;
use App\Lesson;
use App\LessonsModification;
use App\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class LessonsController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        return view('admin/Lesson/index', ['title_for_layout' => 'Lessons']);
    }

    /**
     * Fetch data tobe used in datatable
     */
    public function getData() {
        //return Datatables::of(Lesson::query())->make(true);

        /*  $lesson = Lesson::all(); */
        /* $posts = Lesson::with('grade')->with('theme')->with('subject')->select('*'); */


        $lesson = DB::table('lessons as ls')
                ->Join('grades as gr', 'ls.grade_id', '=', 'gr.id')
                ->Join('subjects as sub', 'ls.subject_id', '=', 'sub.id')
                ->Join('themes as th', 'ls.theme_id', '=', 'th.id')
                ->Join('users as us', 'ls.user_id', '=', 'us.id')
                //->where('ls.status','Draft')                
                ->whereNull('ls.deleted_at')
                ->select(array('ls.*', 'gr.name AS grade_name', 'sub.name AS subject_name',
            'th.name AS theme_name', 'us.firstname AS user_fname', 'us.lastname AS user_lname'));
        //'th.name AS theme_name','CONCAT_WS(`us.firstname AS fname`," ",`us.lastname AS lname`) AS fullname'));

        return Datatables::of($lesson)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $grades = Grade::where('status', 'Active')->lists('name', 'id')->all();
        //$grades = DB::table('grades')->where(['status' => 'Active'])->lists('name', 'id');
        //$subjects = DB::table('subjects')->where(['status' => 'Active'])->lists('name', 'id');
        //$subjects = Subject::where('status','Active')->lists('name', 'id')->all();
        //$themes = DB::table('themes')->where(['status' => 'Active'])->lists('name', 'id');
        return view('admin/Lesson/create', ['title_for_layout' => 'Add Lesson',
            'grades' => $grades
                //,'subjects' => $subjects, 'themes' => $themes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
                    'unit_title' => 'required|max:255',
                    'grade_id' => 'required',
                    'subject_id' => 'required',
                    'theme_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('/admin/lessons/create')
                            ->withInput()
                            ->withErrors($validator);
        }

        $lesson = new Lesson;
        $lesson->unit_title = $request->unit_title;
        $lesson->status = $request->status;
        $lesson->grade_id = $request->grade_id;
        $lesson->subject_id = $request->subject_id;
        $lesson->theme_id = $request->theme_id;
        $lesson->save();

//        $lesson->grade_id = $request->grade_id;
//        foreach ($subject->grade_id as $grade_id_value) {
//            //$grade_sub['grade_id'] = $grade_id_value;
//            //echo $grade_id_value;
//            DB::table('grades_subjects')->insert(['subject_id' => $subject->id, 'grade_id' => $grade_id_value]);
//        }
        Session::flash('success_msg', 'Lesson has been added successfully.');
        return redirect('/admin/lessons');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $lesson = Lesson::find($id);

        $grades = Grade::where('status', 'Active')->lists('name', 'id')->all();
        $subjects = Subject::join('grades_subjects as gs', 'gs.subject_id', '=', 'subjects.id')
                ->where('gs.grade_id', $lesson->grade_id)
                ->where('subjects.status', 'Active')
                ->lists('subjects.name', 'subjects.id')
                ->all();

        //$themes = DB::table('themes')->where(['status' => 'Active'])->lists('name', 'id');
        $themes = Theme::join('grades_subjects_themes as gst', 'gst.theme_id', '=', 'themes.id')
                ->where('gst.grade_id', $lesson->grade_id)
                ->where('gst.subject_id', $lesson->subject_id)
                ->where('themes.status', 'Active')
                ->lists('themes.name', 'themes.id')
                ->all();

        if (empty($lesson)) {
            Session::flash('error_msg', 'Lesson not found.');
            return redirect('/admin/lessons');
        }
        return view('admin/Lesson/edit', ['title_for_layout' => 'Edit Lesson', 'lesson' => $lesson,
            'grades' => $grades,
            'subjects' => $subjects,
            'themes' => $themes
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
                    'unit_title' => 'required|max:255',
                    'grade_id' => 'required',
                    'subject_id' => 'required',
                    'theme_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('admin/lessons/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        } else {
            $lesson = Lesson::findOrFail($id);
            $lesson->unit_title = $request->unit_title;
            $lesson->grade_id = $request->grade_id;
            $lesson->subject_id = $request->subject_id;
            $lesson->theme_id = $request->theme_id;
            $lesson->status = $request->status;
            $lesson->save();

            $msg = 'Lesson has been updated successfully.';
            $log = ActivityLog::createlog(Auth::Id(), "Lesson", $msg, Auth::Id());
            Session::flash('success_msg', $msg);
            return redirect('/admin/lessons');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $subject = Lesson::findOrFail($id);
        $subject->delete();
        //DB::table('grades_subjects')->where(['subject_id' => $id])->delete();
        Session::flash('success_msg', 'Lesson has been deleted successfully.');
        echo 1;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show() {

        return view('admin/Lesson/mylessons', ['title_for_layout' => 'My Lessons']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userLessons($id) {

        $mylessons = DB::table('lessons as ls')
                ->Join('grades as gr', 'ls.grade_id', '=', 'gr.id')
                ->Join('subjects as sub', 'ls.subject_id', '=', 'sub.id')
                ->Join('themes as th', 'ls.theme_id', '=', 'th.id')
                ->Join('users as us', 'ls.user_id', '=', 'us.id')
                ->where('ls.user_id', $id)
                ->whereNull('ls.deleted_at')
                ->select(array('ls.*', 'gr.name AS grade_name', 'sub.name AS subject_name',
            'th.name AS theme_name'));

        return Datatables::of($mylessons)->make(true);
    }

    /**
     * Display the content of lesson.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lessonsContent($id) {
        
        $lesson_content = LessonsModification::where('lesson_id', $id)->lists('content')->all();
        
        if (empty($lesson_content)) {
            Session::flash('error_msg', 'Lesson content is empty.');
            return redirect('/admin/lessons');
        }
        return view('admin/Lesson/lesson_content', ['title_for_layout' => 'Lesson Content',
            'lesson_content' => $lesson_content]);
    }

}
