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

class LearningtargetsNameController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin/LearningTargetsName/index',
                ['title_for_layout' => 'Learning Name']);
    }

    /**
     * Fetch data tobe used in datatable
     */
    public function getData() {
        return Datatables::of(LearningTargetsName::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin/LearningTargetsName/create', 
                ['title_for_layout' => 'Add Learning Name']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255|unique:learningtargets_name',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/learningtargetsname/create')
                            ->withInput()
                            ->withErrors($validator);
        }

        $learningtargetName = new LearningTargetsName;
        $learningtargetName->name = $request->name;
        $learningtargetName->status = $request->status;
        $learningtargetName->save();

        $msg = "Learning Name has been added successfully.";
        //$log = ActivityLog::createlog(Auth::Id(), "Learning Targets", $msg, Auth::Id());

        Session::flash('success_msg', $msg);
        return redirect('/admin/learningtargetsname');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $learningtargetName = LearningTargetsName::find($id);

        if (empty($learningtargetName)) {
            Session::flash('error_msg', 'Learning Name not found.');
            return redirect('/admin/learningtargetsname');
        }
        return view('admin/LearningTargetsName/show', ['title_for_layout' => 'Learning Name View', 'learningtargetsname' => $learningtargetName]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $learningtargetsName = LearningTargetsName::find($id);

        if (empty($learningtargetsName)) {
            Session::flash('error_msg', 'Learning Name not found.');
            return redirect('/admin/learningtargetsname');
        }

        return view('admin/LearningTargetsName/edit', ['title_for_layout' => 'Edit Learning Name','learningtargetsname' =>$learningtargetsName]);
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
                    'name' => 'required|max:255|unique:learningtargets_name,name,'.$id,              
        ]);

        if ($validator->fails()) {
            return redirect('admin/learningtargetsname/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        }

        //Update CMS
        $learningtargetName = LearningTargetsName::find($id);
        $learningtargetName->name = $request->name;
        $learningtargetName->save();

        $msg = "Learning Name has been updated successfully.";
        //$log = ActivityLog::createlog(Auth::Id(), "CMS", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/learningtargetsname');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $learningtargetName = LearningTargetsName::findOrFail($id);
        $learningtargetName->delete();
        
        DB::table('learningtargets')->where(['learningtargetsName_id' => $id])->delete();
//        DB::table('learningtargets_keyconcepts')->where(['learningtargets_id' => $id])->delete();
        $msg = 'Learning Name has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

}
