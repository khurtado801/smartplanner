<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\Subject;
use App\Grade;
use App\KeyConcept;
use App\LearningTargetsKeyConcept;
use App\LearningtargetsName;
use App\Theme;
use App\LearningTargets;
use App\ActivityLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;
use Yajra\Datatables\Datatables;

class LearningTargetsController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $subjects = Subject::where('status', 'Active')
                        ->orderby('name', 'ASC')->lists('name', 'id')->all();
        $grades = Grade::where('status', 'Active')
                        ->orderby(DB::raw("name * 1, name"), 'ASC')->lists('name', 'id')->all();
        $theme = Theme::where('status', 'Active')
                        ->orderby('name', 'ASC')->lists('name', 'name')->all();
//        $learningTargets = LearningTargets::join('learningtargets_keyconcepts', 'learningtargets_keyconcepts.learningtargets_id', '=', 'learningtargets.id')
//                ->join('grades', 'learningtargets_keyconcepts.grade_id', '=', 'grades.id')                
//                ->join('themes', 'learningtargets_keyconcepts.theme_id', '=', 'themes.id')
//                ->join('subjects', 'learningtargets_keyconcepts.subject_id', '=', 'subjects.id')
//                ->join('keyconcepts', 'learningtargets_keyconcepts.keyconcepts_id', '=', 'keyconcepts.id')
//                ->select(['learningtargets.id as lt_id','learningtargets.name as lt_name','grades.name as g_name','themes.name as t_name','subjects.name as s_name','keyconcepts.name as kc_name','learningtargets.status as lt_status'])
//                ->get();
        return view('admin/LearningTargets/index',
                ['title_for_layout' => 'Learning Targets',
                    'subjects'=>$subjects,
                    'grades'=>$grades,
                    'theme'=>$theme,
                ]);
    }

    /**
     * Fetch data tobe used in datatable
     */
    public function getData() {
        $learningTargets = LearningTargets::join('learningtargets_keyconcepts', 'learningtargets_keyconcepts.learningtargets_id', '=', 'learningtargets.id')
                ->join('grades', 'learningtargets_keyconcepts.grade_id', '=', 'grades.id')                
                ->join('themes', 'learningtargets_keyconcepts.theme_id', '=', 'themes.id')
                ->join('subjects', 'learningtargets_keyconcepts.subject_id', '=', 'subjects.id')
                ->join('learningtargets_name', 'learningtargets.learningtargetsName_id', '=', 'learningtargets_name.id')
                ->join('keyconcepts', 'learningtargets_keyconcepts.keyconcepts_id', '=', 'keyconcepts.id')
//                ->select(['learningtargets.id as lt_id','learningtargets.name as lt_name','grades.name as g_name','themes.name as t_name','subjects.name as s_name','keyconcepts.name as kc_name','learningtargets.status as lt_status']);
                ->select(['learningtargets.id as lt_id','learningtargets_name.name as lt_name','grades.name as g_name','themes.name as t_name','subjects.name as s_name','keyconcepts.name as kc_name','learningtargets.status as lt_status']);
//                ->get(); 
//        print_r($learningTargets);
//        exit;
        $datatables =  app('datatables')->of($learningTargets);
        // additional users.name search
       if ($name = $datatables->request->get('lt_name')) {
           $datatables->where('learningtargets_name.name', 'like', "%$name%");
       }
       if ($gname = $datatables->request->get('g_name')) {
           $datatables->where('grades.name', '=', "$gname");
       }
       if ($tname = $datatables->request->get('t_name')) {
           $datatables->where('themes.name', '=', "$tname");
       }
       if ($sname = $datatables->request->get('s_name')) {
           $datatables->where('subjects.name', '=', "$sname");
       }
       if ($kcname = $datatables->request->get('kc_name')) {
           $datatables->where('keyconcepts.name', '=', "$kcname");
       }
        return Datatables::of($learningTargets)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $grades = Grade::where('status', 'Active')
                        ->orderby('name', 'ASC')->lists('name', 'id')->all();
        $learningtargetsName = LearningtargetsName::where('status', 'Active')
                        ->orderby('name', 'ASC')->lists('name', 'id')->all();
        //$keyconcepts = DB::table('keyconcepts')->where(['status' => 'Active'])->lists('name', 'id');
//        $keyconcepts = KeyConcept::where(['status' => 'Active'])->orderby('name', 'ASC')->lists('name', 'id')->all();
        return view('admin/LearningTargets/create', 
                ['title_for_layout' => 'Add Learning Target',
                    'grades' => $grades,
                'learningtargetsName' => $learningtargetsName]);
    }

    public function getSubjectsByGrade(Request $request) {

        $subjects = Subject::getSubjectsByGrade($request->grade_id);

        if (count($subjects) > 0) {
            $this->resultapi('1', 'Subjects Found', $subjects);
        } else {
            $this->resultapi('0', 'No Subjects Found', $subjects);
        }
    }

    public function getThemesByGradeAndSubjects(Request $request) {

        if ($request->all() && count($request->all()) > 0) {
            $themes_selected = Theme::getThemesByGradeAndSubjects($request->grade_id, $request->subject_id);

            if (count($themes_selected) > 0) {
                $this->resultapi('1', 'Themes Found', $themes_selected);
            } else {
                $this->resultapi('0', 'No Themes Found', $themes_selected);
            }
        }
    }

    public function getKeyconceptByTheme(Request $request) {

        $keyconcept_details = DB::table('keyconcepts as kc')
                ->join('keyconcepts_themes as kct', 'kct.keyconcept_id', '=', 'kc.id')
                ->select('kc.name as keyconcept_name', 'kc.id as kc_id')
                ->orderBy('keyconcept_name', 'asc')
                ->where('kct.theme_id', $request->theme_id)
                ->where('kc.status', 'Active')
                ->get();
        if (count($keyconcept_details) > 0) {
            $this->resultapi('1', 'Keyconcept Found', $keyconcept_details);
        } else {
            $this->resultapi('0', 'No Keyconcept Found', $keyconcept_details);
        }
    }

    public function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);
        exit;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'learningtargetsName_id' => 'required|max:255',
                    'keyconcepts_id' => 'required',
                    'overview_summary' => 'required',
                    'standards' => 'required',
                    'essential_questions' => 'required',
                    'core_ideas' => 'required',
                    'academic_vocabulary' => 'required',
                    'grade_id' => 'required',
                    'subject_id' => 'required',
                    'theme_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/learningtargets/create')
                            ->withInput()
                            ->withErrors($validator);
        }
//        echo $request->learningtargetsName_id.'<pre>';
        $learningtargetkeyconcept = LearningTargetsKeyConcept::join('learningtargets as lt', 'learningtargets_keyconcepts.learningtargets_id', '=', 'lt.id')
                ->where('subject_id',$request->subject_id)
                ->where('theme_id',$request->theme_id)->where('grade_id',$request->grade_id)
                ->where('keyconcepts_id',$request->keyconcepts_id)
                ->first();
        
        if($learningtargetkeyconcept['learningtargetsName_id']==$request->learningtargetsName_id){
            Session::flash('error_msg', 'Already using #ID('.$learningtargetkeyconcept['id'].').');
            return redirect('/admin/learningtargets');
        }
//        exit;
        $learningtarget = new LearningTargets;
        $learningtarget->learningtargetsName_id = $request->learningtargetsName_id;
        $learningtarget->status = $request->status;
        $learningtarget->overview_summary = $request->overview_summary;
        $learningtarget->standards = $request->standards;
        $learningtarget->essential_questions = $request->essential_questions;
        $learningtarget->core_ideas = $request->core_ideas;
        $learningtarget->academic_vocabulary = $request->academic_vocabulary;
        $learningtarget->save();

        $learningtarget->keyconcepts_id = $request->keyconcepts_id;
//        foreach ($learningtarget->keyconcepts_id as $keyconcepts_id_value) {
//            //$grade_sub['grade_id'] = $grade_id_value;
//            //echo $grade_id_value;
//            DB::table('learningtargets_keyconcepts')->insert([
//                    'learningtargets_id' => $learningtarget->id,
//                    'keyconcepts_id' => $keyconcepts_id_value,
//                    'subject_id' => $request->subject_id,
//                    'theme_id' => $request->theme_id,
//                    'grade_id' => $request->grade_id]);
//        }
            $learningtargetkeyconcept = LearningTargetsKeyConcept::where('subject_id',$request->subject_id)
                    ->where('theme_id',$request->theme_id)->where('grade_id',$request->grade_id)
                    ->where('keyconcepts_id',$request->keyconcepts_id)->where('learningtargets_id',$learningtarget->id)
                    ->first();
            if(count($learningtargetkeyconcept)<=0){
                $learningtargetkeyconcept = new LearningTargetsKeyConcept;
                $learningtargetkeyconcept->subject_id = $request->subject_id;
                $learningtargetkeyconcept->theme_id = $request->theme_id;
                $learningtargetkeyconcept->grade_id = $request->grade_id;
                $learningtargetkeyconcept->keyconcepts_id = $request->keyconcepts_id;
                $learningtargetkeyconcept->learningtargets_id = $learningtarget->id;
                $learningtargetkeyconcept->save();
            }
        
        $msg = "Learning target has been added successfully.";
        //$log = ActivityLog::createlog(Auth::Id(), "Learning Targets", $msg, Auth::Id());

        Session::flash('success_msg', $msg);
        return redirect('/admin/learningtargets');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $learningtarget = LearningTargets::find($id);

        if (empty($learningtarget)) {
            Session::flash('error_msg', 'Learning Target not found.');
            return redirect('/admin/learningtargets');
        }
        return view('admin/LearningTargets/show', ['title_for_layout' => 'Learning Target View', 'learningtarget' => $learningtarget]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $learningtarget = LearningTargets::find($id);
        $learningtargetsName = LearningtargetsName::where('status', 'Active')
                        ->orderby('name', 'ASC')->lists('name', 'id')->all();
        //$keyconcepts = DB::table('keyconcepts')->where(['status' => 'Active'])->lists('name', 'id');
        $keyconcepts = KeyConcept::where(['status' => 'Active'])->orderby('name', 'ASC')->lists('name', 'id')->all();
        $drop_ids = DB::table('learningtargets_keyconcepts')->where('learningtargets_keyconcepts.learningtargets_id', '=', $id)->first();
        $keyconcepts_selected = DB::table('learningtargets_keyconcepts')
                ->where('learningtargets_keyconcepts.learningtargets_id', '=', $id)
                ->lists('keyconcepts_id');
//        echo '<pre>';print_r($keyconcepts_selected);
//        print_r($learningtarget);exit;
        if (empty($learningtarget)) {
            Session::flash('error_msg', 'Learning Target not found.');
            return redirect('/admin/learningtargets');
        }
        $learningtarget->grade_id = $drop_ids->grade_id;
        $learningtarget->theme_id = $drop_ids->theme_id;
        $learningtarget->subject_id = $drop_ids->subject_id;
        $learningtarget->keyconcepts_id = $drop_ids->keyconcepts_id;
        
        $subjects = Subject::getSubjectsByGrade($drop_ids->grade_id);
        $themes_selected = Theme::getThemesByGradeAndSubjects($drop_ids->grade_id, $drop_ids->subject_id);

        $subjects_all = $themes_all = array();
        foreach ($subjects as $value) {
            $subjects_all[$value['id']] = $value['name'];
        }
        foreach ($themes_selected as $value1) {
            $themes_all[$value1['theme_id']] = $value1['name'];
        }
        $grades = Grade::where('status', 'Active')
                        ->orderby('name', 'ASC')->lists('name', 'id')->all();
        return view('admin/LearningTargets/edit', ['title_for_layout' => 'Edit Learning Target',
            'learningtarget' => $learningtarget, 'keyconcepts' => $keyconcepts,
            'keyconcepts_selected' => $keyconcepts_selected,'grades' => $grades,
            'subjects' => $subjects_all,'themes' => $themes_all,
            'learningtargetsName' => $learningtargetsName]);
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
                    'learningtargetsName_id' => 'required|max:255',
                    'keyconcepts_id' => 'required',
                    'overview_summary' => 'required',
                    'standards' => 'required',
                    'essential_questions' => 'required',
                    'core_ideas' => 'required',
                    'academic_vocabulary' => 'required',
                    'grade_id' => 'required',
                    'subject_id' => 'required',
                    'theme_id' => 'required',                    
        ]);

        if ($validator->fails()) {
            return redirect('admin/learningtargets/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        }
        $learningtargetkeyconcept = LearningTargetsKeyConcept::join('learningtargets as lt', 'learningtargets_keyconcepts.learningtargets_id', '=', 'lt.id')
                ->where('subject_id',$request->subject_id)
                ->where('theme_id',$request->theme_id)->where('grade_id',$request->grade_id)
                ->where('keyconcepts_id',$request->keyconcepts_id)
                ->first();

        if($learningtargetkeyconcept['id']!=$id && $learningtargetkeyconcept['learningtargetsName_id']==$request->learningtargetsName_id){
            Session::flash('error_msg', 'Already using #ID('.$learningtargetkeyconcept['id'].').');
            return redirect('/admin/learningtargets');
        }
        //Update CMS
        $learningtarget = LearningTargets::find($id);
        $learningtarget->learningtargetsName_id = $request->learningtargetsName_id;
        $learningtarget->status = $request->status;
        $learningtarget->overview_summary = $request->overview_summary;
        $learningtarget->standards = $request->standards;
        $learningtarget->essential_questions = $request->essential_questions;
        $learningtarget->core_ideas = $request->core_ideas;
        $learningtarget->academic_vocabulary = $request->academic_vocabulary;
        $learningtarget->save();

        $learningtarget->keyconcepts_id = $request->keyconcepts_id;
//        DB::table('learningtargets_keyconcepts')
//                ->where(['learningtargets_id' => $learningtarget->id])
//                ->whereNotIn('keyconcepts_id', $learningtarget->keyconcepts_id)
//                ->delete();
//
//        $existing_data = DB::table('learningtargets_keyconcepts')
//                ->where(['learningtargets_id' => $learningtarget->id])
//                ->lists('learningtargets_id');
//
//        $new_keyconcepts = array_diff($learningtarget->keyconcepts_id, $existing_data);

//        if (!empty($new_keyconcepts)) {
//            foreach ($new_keyconcepts as $keyconcepts_id_new_value) {
//                DB::table('learningtargets_keyconcepts')->insert([
//                    'learningtargets_id' => $learningtarget->id,
//                    'keyconcepts_id' => $keyconcepts_id_new_value
//                ]);
//            }
//        }
//        $all_data = DB::table('learningtargets_keyconcepts')
//                ->where(['learningtargets_id' => $learningtarget->id])
//                ->get();
//        
//        
//        foreach ($all_data as $key => $value) {
            $learningtargetkeyconcept = LearningTargetsKeyConcept::where('subject_id',$request->subject_id)
                    ->where('theme_id',$request->theme_id)->where('grade_id',$request->grade_id)
                    ->where('keyconcepts_id',$request->keyconcepts_id)->where('learningtargets_id',$learningtarget->id)
                    ->first();
            if(count($learningtargetkeyconcept)<=0){
                $learningtargetkeyconcept = new LearningTargetsKeyConcept;
                $learningtargetkeyconcept->subject_id = $request->subject_id;
                $learningtargetkeyconcept->theme_id = $request->theme_id;
                $learningtargetkeyconcept->grade_id = $request->grade_id;
                $learningtargetkeyconcept->keyconcepts_id = $request->keyconcepts_id;
                $learningtargetkeyconcept->learningtargets_id = $learningtarget->id;
                $learningtargetkeyconcept->save();
            }
            
//        }
        
        $msg = "Learning target has been updated successfully.";
        //$log = ActivityLog::createlog(Auth::Id(), "CMS", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/learningtargets');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $learningtarget = LearningTargets::findOrFail($id);
        $learningtarget->delete();
        DB::table('learningtargets_keyconcepts')->where(['learningtargets_id' => $id])->delete();
        $msg = 'Learning target has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

}
