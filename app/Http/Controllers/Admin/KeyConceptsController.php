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
use App\KeyConcept;
use App\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class KeyConceptsController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //echo 'dasf';die;
        return view('admin/KeyConcepts/index', ['title_for_layout' => 'KeyConcepts']);
    }

    /**
     * Fetch data tobe used in datatable
     */
    public function getData() {
        return Datatables::of(KeyConcept::query()->orderby('order_by'))->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //$themes = DB::table('themes')->where(['status' => 'Active'])->lists('name', 'id');
        $themes = Theme::where(['status' => 'Active'])->orderby('name')->lists('name', 'id')->all();
        return view('admin/KeyConcepts/create', ['title_for_layout' => 'Add KeyConcept',
            'themes' => $themes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //echo "zzzz"; exit;
        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'theme_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('/admin/KeyConcepts/create')
                            ->withInput()
                            ->withErrors($validator);
        }

        $keyconcept = new KeyConcept;
        $keyconcept->name = $request->name; //theme table
        $keyconcept->status = $request->status;
        //echo "<pre>"; print_r($grade_sub); exit;
        $keyconcept->save();

        $keyconcept->theme_id = $request->theme_id;
        foreach ($keyconcept->theme_id as $theme_id_value) {
            DB::table('keyconcepts_themes')->insert(['keyconcept_id' => $keyconcept->id, 'theme_id' => $theme_id_value]);
        }

        $msg = 'KeyConcept has been added successfully.';
        $log = ActivityLog::createlog(Auth::Id(), "KeyConcepts", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/keyconcepts');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $theme = DB::table('keyconcepts')
                ->select('keyconcepts.*', 'keyconcepts.name as keyconcepts_name')
                ->leftJoin('grades', 'keyconcepts.grade_id', '=', 'grades.id')
                ->where('grades.id', '=', $id)
                ->get();

        if (empty($theme)) {
            Session::flash('error_msg', 'KeyConcept not found.');
            return redirect('/admin/keyconcepts');
        }
        return view('admin/KeyConcepts/show', ['title_for_layout' => 'KeyConcept', 'subcategory' => array_shift($theme)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $keyconcept = KeyConcept::find($id);
        $keyconcept_selected = DB::table('keyconcepts_themes')->where('keyconcepts_themes.keyconcept_id', '=', $id)->lists('theme_id');
        //$themes = DB::table('themes')->where(['status' => 'Active'])->lists('name', 'id');
        $themes = Theme::where(['status' => 'Active'])->orderby('name')->lists('name', 'id')->all();

        if (empty($keyconcept)) {
            Session::flash('error_msg', 'Key Concepts not found.');
            return redirect('/admin/KeyConcepts');
        }

        return view('admin/KeyConcepts/edit', ['title_for_layout' => 'Edit KeyConcept',
            'keyconcept' => $keyconcept,
            'themes' => $themes,
            'themes_selected' => $keyconcept_selected]);
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
                    'theme_id' => 'required',
        ]);

        // echo '<pre>';
        // print_r($request->all());
        // die;

        if ($validator->fails()) {
            return redirect('admin/KeyConcepts/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        } else {

            $keyconcept = KeyConcept::findOrFail($id);
            $keyconcept->name = $request->name;
            $keyconcept->status = $request->status;
            $keyconcept->save();


            $keyconcept->theme_id = $request->theme_id;

            DB::table('keyconcepts_themes')
                    ->where(['keyconcept_id' => $keyconcept->id])
                    ->whereNotIn('theme_id', $keyconcept->theme_id)
                    ->delete();

            $existing_data = DB::table('keyconcepts_themes')
                    ->where(['keyconcept_id' => $keyconcept->id])
                    ->lists('theme_id');

            $new_theme_id = array_diff($keyconcept->theme_id, $existing_data);
            //echo '<pre>'; print_r($new_grades); exit;
            if (!empty($new_theme_id)) {
                foreach ($new_theme_id as $theme_id_new_value) {
                    DB::table('keyconcepts_themes')
                            ->insert(['keyconcept_id' => $keyconcept->id, 'theme_id' => $theme_id_new_value]);
                }
            }

            $msg = 'KeyConcept has been updated successfully.';
            $log = ActivityLog::createlog(Auth::Id(), "KeyConcepts", $msg, Auth::Id());
            Session::flash('success_msg', $msg);
            return redirect('/admin/keyconcepts');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $keyconcept = KeyConcept::findOrFail($id);
        $keyconcept->delete();

        DB::table('keyconcepts_themes')->where(['keyconcept_id' => $id])->delete();
        $msg = 'KeyConcept has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

}
