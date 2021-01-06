<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\Skills;
use App\ActivityLog;
use Illuminate\Support\Facades\Input;
use File;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class SkillsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin/Skills/index', ['title_for_layout' => 'Skills Pages']);
    }
    
    /**
     * Fetch data tobe used in datatable
    */
    public function getData() {
        return Datatables::of(Skills::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin/Skills/create', ['title_for_layout' => 'Add Skills']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skill'          => 'required|max:100',            
            ]);

        if ($validator->fails()) {
            return redirect('/admin/skills/create')
            ->withInput()
            ->withErrors($validator);
        }

        $skill = new skills;
        $skill->skill = $request->skill;             
        $skill->save();

        $msg = "Skills Added Successfully.";
        $log = ActivityLog::createlog(Auth::Id(),"Skills",$msg,Auth::Id()); 
        Session::flash('success_msg', $msg);
        return redirect('/admin/skills');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $skill = Skills::find($id);
        if(empty($skill)){
            Session::flash('error_msg', 'Page not found.');
            return redirect('/admin/skills');
        }
        return view('admin/Skills/show', ['title_for_layout' => 'Skill View', 'skill' => $skill]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = Skills::find($id);
        if(empty($page)) {
            Session::flash('error_msg', 'Page not found.');
            return redirect('/admin/skills');
        }

         return view('admin/Skills/edit', ['title_for_layout' => 'Edit Skills', 'page' => $page]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'skill'         => 'required|max:255',
            'status'        => 'required|max:255',
            ]);

        if ($validator->fails()) {
            return redirect('admin/skills/' . $id . '/edit')
            ->withInput()
            ->withErrors($validator);
        }
      
        $skill          = Skills::find($id);
        $skill->skill   = $request->skill;
        $skill->status  = $request->status;           
        $skill->save(); 

        $msg = "Skills Updated Successfully.";
        $log = ActivityLog::createlog(Auth::Id(),"Skills",$msg,Auth::Id());          

        Session::flash('success_msg', $msg);
        return redirect('/admin/skills');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        
        DB::table('skills')->where('id', $id)->delete();
        echo 1;exit;
    } 
}
