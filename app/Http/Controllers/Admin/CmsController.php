<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\Cms;
use App\ActivityLog;
use Illuminate\Support\Facades\Input;
use File;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class CmsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function index() {
        return view('admin/Cms/index', ['title_for_layout' => 'Cms Pages']);
    }

    /**
     * Fetch data tobe used in datatable
     */
    public function getData() {
        return Datatables::of(Cms::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin/Cms/create', ['title_for_layout' => 'Add Pages']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'title' => 'required|max:255|unique:cms',
                    'description' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/cms/create')
                            ->withInput()
                            ->withErrors($validator);
        }

        $cms = new Cms;
        $cms->title = $request->title;
        $cms->slug = preg_replace('/[^a-zA-Z0-9_.]/', '_', strtolower($request->title));
        $cms->description = $request->description;
        $cms->status = $request->status;
        $cms->save();

        $msg = "Page Added Successfully.";
        $log = ActivityLog::createlog(Auth::Id(), "CMS", $msg, Auth::Id());

        Session::flash('success_msg', $msg);
        return redirect('/admin/cms');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $page = Cms::find($id);
        if (empty($page)) {
            Session::flash('error_msg', 'Page not found.');
            return redirect('/admin/user');
        }

        return view('admin/Cms/edit', ['title_for_layout' => 'Edit Page', 'page' => $page]);
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
                    'title' => 'required|max:255|unique:cms,id,'.$id,
                    'description' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('admin/cms/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        }

        //Update CMS
        $page = Cms::find($id);
        $page->title = $request->title;
        $page->description = $request->description;
        $page->status = $request->status;
        $page->save();

        $msg = "Page Updated Successfully.";
        $log = ActivityLog::createlog(Auth::Id(), "CMS", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/cms');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $cms = Cms::findOrFail($id);
        $cms->delete();
        $msg = 'Country has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

}
