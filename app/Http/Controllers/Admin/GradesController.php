<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use App\Grade;
use App\Lesson;
use App\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Model;

class GradesController extends Controller {


    public function __construct() {
        $this->middleware('auth');
    }

    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin/Grade/index', ['title_for_layout' => 'Grades']);
    }

    public function getData() {
        return Datatables::of(Grade::query())->orderby('name','asc')->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin/Grade/create', ['title_for_layout' => 'Add Grade']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255|unique:grades',
                    'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('/admin/grades/create')
                            ->withInput()
                            ->withErrors($validator);
        }
        $grade = new Grade;
        $grade->name = $request->name;
        $grade->status = $request->status;
        $grade->save();

        $msg = 'Grade has been added successfully.';
        $log = ActivityLog::createlog(Auth::Id(), "Grade", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/grades');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $grade = Grade::find($id);
        if (empty($grade)) {
            Session::flash('error_msg', 'Grade not found.');
            return redirect('/admin/grades');
        }
        return view('admin/Grade/edit', ['title_for_layout' => 'Edit Grade', 'grade' => $grade]);
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
                    'name' => 'required|max:255|unique:grades,id,'.$id,
                    //'name' => 'required',
                    'status' => 'required',
        ]);
        if ($validator->fails()) {

            return redirect('admin/grades/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        } else {

            $grade = Grade::find($id);
            $grade->name = $request->name;
            $grade->status = $request->status;
            $grade->save();
            
            $msg = 'Grade has been updated successfully.';
            $log = ActivityLog::createlog(Auth::Id(), "Grade", $msg, Auth::Id());
            Session::flash('success_msg', $msg);
            return redirect('/admin/grades');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //echo $id;die;
        $grade = Grade::findOrFail($id);
        $grade->delete();
        $msg = 'Grade has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

}
