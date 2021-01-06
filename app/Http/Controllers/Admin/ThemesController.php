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
use App\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class ThemesController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin/Theme/index', ['title_for_layout' => 'Themes']);
    }

    /**
     * Fetch data tobe used in datatable
     */
    public function getData() {
        return Datatables::of(Theme::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $grades = Grade::where('status', 'Active')
            ->orderby('name','ASC')->lists('name', 'id')->all();
        $subjects = Subject::where('status', 'Active')
            ->orderby('name','ASC')->lists('name', 'id')->all();
        return view('admin/Theme/create', ['title_for_layout' => 'Add Theme',
            'grades' => $grades, 'subjects' => $subjects]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255|unique:themes',
                    'grade_id' => 'required|min:1',
                    'subject_id' => 'required|min:1',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/themes/create')
                            ->withInput()
                            ->withErrors($validator);
        }

        $theme = new Theme;
        $theme->name = $request->name; //theme table
        $theme->status = $request->status;
        $theme->save();

        $theme_id = $theme->id;

        foreach ($request->grade_id as $key => $value) {
            if (count($value) > 0) {
                foreach ($value as $grade_key => $grade_val) {
                    DB::table('grades_subjects_themes')->insert(['theme_id' => $theme_id,
                        'grade_id' => $grade_val,
                        'subject_id' => $request->subject_id[$key]]);
                }
            }
        }
        $msg = 'Theme has been added successfully.';
        $log = ActivityLog::createlog(Auth::Id(), "Themes", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/themes');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $theme = Theme::find($id);
        $grades = Grade::where('status', 'Active')
            ->orderby('name','ASC')->lists('name', 'id')->all();
        $subjects = Subject::where('status', 'Active')
            ->orderby('name','ASC')->lists('name', 'id')->all();

        $theme_id = $theme->id;

        $theme_count = DB::table('grades_subjects_themes')
                ->select('*', DB::raw('GROUP_CONCAT(distinct SUBSTRING(grade_id ,1)) as grade_id'))
                ->where('theme_id', $theme_id)
                ->groupBy('subject_id')
                ->get();

        if (empty($theme)) {
            Session::flash('error_msg', 'Subject not found.');
            return redirect('/admin/themes');
        }
        
        return view('admin/Theme/edit', ['title_for_layout' => 'Edit Theme',
            'grades' => $grades, 'subjects' => $subjects, 'theme' => $theme,
            'theme_count' => $theme_count]);
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
                    'name' => 'required|max:255|unique:themes,id,' . $id,
                    'grade_id' => 'required',
                    'subject_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect('admin/themes/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        } else {
            $theme = Theme::findOrFail($id);
            $theme->name = $request->name;
            $theme->status = $request->status;
            $theme->save();            
            
            $theme_id = $theme->id;
            $theme->grade_id = $request->grade_id;
            $theme->subject_id = $request->subject_id;
            
            DB::table('grades_subjects_themes')
                    ->where('theme_id',$theme->id)
                    ->delete();
            
            foreach ($request->grade_id as $key => $value) {
                if (count($value) > 0) {
                    foreach ($value as $grade_key => $grade_val) {
                        DB::table('grades_subjects_themes')->insert(['theme_id' => $theme_id,
                            'grade_id' => $grade_val,
                            'subject_id' => $request->subject_id[$key]]);
                    }
                }
            }
            
            $msg = 'Theme has been updated successfully.';
            $log = ActivityLog::createlog(Auth::Id(), "Themes", $msg, Auth::Id());
            Session::flash('success_msg', $msg);
            return redirect('/admin/themes');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $theme = Theme::findOrFail($id);
        $theme->delete();

        DB::table('grades_subjects_themes')->where(['theme_id' => $id])->delete();
        DB::table('keyconcepts_themes')->where(['theme_id' => $id])->delete();
        $msg = 'Theme has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

}
