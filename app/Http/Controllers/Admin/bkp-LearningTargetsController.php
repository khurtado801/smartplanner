<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\Subject;
use App\KeyConcept;
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
            ->orderby('name','ASC')->lists('name', 'id')->all();

        return view('admin/LearningTargets/index', ['title_for_layout' => 'Learning Targets','subjects' => $subjects]);
    }

    /**
     * Fetch data tobe used in datatable
     */
    public function getData() {
        return Datatables::of(LearningTargets::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //$keyconcepts = DB::table('keyconcepts')->where(['status' => 'Active'])->lists('name', 'id');
        $keyconcepts = KeyConcept::where(['status' => 'Active'])->orderby('name','ASC')->lists('name', 'id')->all();
        return view('admin/LearningTargets/create', ['title_for_layout' => 'Add Learning Target', 'keyconcepts' => $keyconcepts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'keyconcepts_id' => 'required',
                    'overview_summary' => 'required',
                    'standards' => 'required',
                    'essential_questions' => 'required',
                    'core_ideas' => 'required',
                    'academic_vocabulary' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/learningtargets/create')
                            ->withInput()
                            ->withErrors($validator);
        }

        $learningtarget = new LearningTargets;
        $learningtarget->name = $request->name;
        $learningtarget->status = $request->status;
        $learningtarget->overview_summary = $request->overview_summary;
        $learningtarget->standards = $request->standards;
        $learningtarget->essential_questions = $request->essential_questions;
        $learningtarget->core_ideas = $request->core_ideas;
        $learningtarget->academic_vocabulary = $request->academic_vocabulary;
        $learningtarget->save();

        $learningtarget->keyconcepts_id = $request->keyconcepts_id;
        foreach ($learningtarget->keyconcepts_id as $keyconcepts_id_value) {
            //$grade_sub['grade_id'] = $grade_id_value;
            //echo $grade_id_value;
            DB::table('learningtargets_keyconcepts')->insert(['learningtargets_id' => $learningtarget->id, 'keyconcepts_id' => $keyconcepts_id_value]);
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
        //$keyconcepts = DB::table('keyconcepts')->where(['status' => 'Active'])->lists('name', 'id');
        $keyconcepts = KeyConcept::where(['status' => 'Active'])->orderby('name','ASC')->lists('name', 'id')->all();
        $keyconcepts_selected = DB::table('learningtargets_keyconcepts')->where('learningtargets_keyconcepts.learningtargets_id', '=', $id)->lists('keyconcepts_id');

        if (empty($learningtarget)) {
            Session::flash('error_msg', 'Learning Target not found.');
            return redirect('/admin/learningtargets');
        }
        //echo '<pre>'; print_r($learningtarget); //exit;
        return view('admin/LearningTargets/edit', ['title_for_layout' => 'Edit Learning Target',
            'learningtarget' => $learningtarget, 'keyconcepts' => $keyconcepts,
            'keyconcepts_selected' => $keyconcepts_selected]);
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
                    'name' => 'required|max:255',
                    'keyconcepts_id' => 'required',
                    'overview_summary' => 'required',
                    'standards' => 'required',
                    'essential_questions' => 'required',
                    'core_ideas' => 'required',
                    'academic_vocabulary' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('admin/learningtargets/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        }

        //Update CMS
        $learningtarget = LearningTargets::find($id);
        $learningtarget->name = $request->name;
        $learningtarget->status = $request->status;
        $learningtarget->overview_summary = $request->overview_summary;
        $learningtarget->standards = $request->standards;
        $learningtarget->essential_questions = $request->essential_questions;
        $learningtarget->core_ideas = $request->core_ideas;
        $learningtarget->academic_vocabulary = $request->academic_vocabulary;
        $learningtarget->save();

        $learningtarget->keyconcepts_id = $request->keyconcepts_id;
        DB::table('learningtargets_keyconcepts')
                ->where(['learningtargets_id' => $learningtarget->id])
                ->whereNotIn('keyconcepts_id', $learningtarget->keyconcepts_id)
                ->delete();

        $existing_data = DB::table('learningtargets_keyconcepts')
                ->where(['learningtargets_id' => $learningtarget->id])
                ->lists('learningtargets_id');

        $new_keyconcepts = array_diff($learningtarget->keyconcepts_id, $existing_data);
        //echo '<pre>'; print_r($new_grades); exit;
        if (!empty($new_keyconcepts)) {
            foreach ($new_keyconcepts as $keyconcepts_id_new_value) {
                DB::table('learningtargets_keyconcepts')
                        ->insert(['learningtargets_id' => $learningtarget->id, 'keyconcepts_id' => $keyconcepts_id_new_value]);
            }
        }

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
