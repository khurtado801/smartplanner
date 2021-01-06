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
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class SubjectsController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin/Subject/index', ['title_for_layout' => 'Subjects']);
    }

    /**
     * Fetch data tobe used in datatable
     */
    public function getData() {
        return Datatables::of(Subject::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //$grades = DB::table('grades')->where(['status' => 'Active'])->lists('name', 'id');
        $grades = Grade::where('status','Active')
            ->orderby('name','ASC')->lists('name', 'id')->all();
        return view('admin/Subject/create', ['title_for_layout' => 'Add Subject', 'grades' => $grades]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255|unique:subjects',
                    'grade_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('/admin/subjects/create')
                            ->withInput()
                            ->withErrors($validator);
        }

        $subject = new Subject;
        $subject->name = $request->name; //subject table
        $subject->status = $request->status;
        //echo "<pre>"; print_r($grade_sub); exit;
        $subject->save();

        $subject->grade_id = $request->grade_id;
        foreach ($subject->grade_id as $grade_id_value) {
            //$grade_sub['grade_id'] = $grade_id_value;
            //echo $grade_id_value;
            DB::table('grades_subjects')->insert(['subject_id' => $subject->id, 'grade_id' => $grade_id_value]);
        }
        Session::flash('success_msg', 'Subject has been added successfully.');
        return redirect('/admin/subjects');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $subject = Subject::find($id);
        //$grades = DB::table('grades')->where(['status' => 'Active'])->lists('name', 'id');
        $grades = Grade::where('status','Active')
            ->orderby('name','ASC')->lists('name', 'id')->all();
        $grades_selected = DB::table('grades_subjects')->where('grades_subjects.subject_id', '=', $id)->lists('grade_id');
//        $grades_selected_array = array();
//        foreach ($grades_selected_obj as $grades_selected_key => $grades_selected_value) {
//           $grades_selected_array[] = $grades_selected_value->grade_id;
//        }
        //$grades_selected = implode(',',$grades_selected);
        if (empty($subject)) {
            Session::flash('error_msg', 'Subject not found.');
            return redirect('/admin/subjects');
        }
        return view('admin/Subject/edit', ['title_for_layout' => 'Edit Subject',
            'grades' => $grades, 'subject' => $subject,
            'grades_selected' => $grades_selected]);
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
                    'name' => 'required|max:255|unique:subjects,id,'.$id,
                    'grade_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('admin/subjects/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        } else {
            $subject = Subject::findOrFail($id);
            $subject->name = $request->name;
            $subject->status = $request->status;
            $subject->save();

            $subject->grade_id = $request->grade_id;
            DB::table('grades_subjects')
                    ->where(['subject_id' => $subject->id])
                    ->whereNotIn('grade_id', $subject->grade_id)
                    ->delete();

            $existing_data = DB::table('grades_subjects')
                    ->where(['subject_id' => $subject->id])
                    ->lists('grade_id');

            $new_grades = array_diff($subject->grade_id, $existing_data);
            //echo '<pre>'; print_r($new_grades); exit;
            if (!empty($new_grades)) {
                foreach ($new_grades as $grade_id_new_value) {
                    DB::table('grades_subjects')
                            ->insert(['subject_id' => $subject->id, 'grade_id' => $grade_id_new_value]);
                }
            }
            Session::flash('success_msg', 'Subject has been updated successfully.');
            return redirect('/admin/subjects');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $subject = Subject::findOrFail($id);
        $subject->delete();
        DB::table('grades_subjects')->where(['subject_id' => $id])->delete();
        Session::flash('success_msg', 'Subject has been deleted successfully.');
        echo 1;
    }

    /**
     * Fetch subjects by c tobe used in datatable
     */
    public function getSubjectsByGrade($grade_id) { //echo "zxzxzx"; exit;
        return $subjects = Subject::getSubjectsByGrade($request->grade_id);
    }

}
