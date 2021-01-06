<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\LanguageTag;
use App\ActivityLog;
use Illuminate\Support\Facades\Input;
use File;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class GlobelController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin/Language/index', ['title_for_layout' => 'Manage Label']);
    }
    
    /**
     * Fetch data tobe used in datatable
    */
    public function getData() {
        return Datatables::of(LanguageTag::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin/Language/create', ['title_for_layout' => 'Add Labels']);
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
            'label'           => 'required',
            'changed_label'   => 'required',
            ]);

        if ($validator->fails()) {
            return redirect('/admin/language/create')
            ->withInput()
            ->withErrors($validator);
        }

        $label = new LanguageTag;
        $label->lang_id         = 2; 
        $label->lang_code       = "de";
        $label->label           = trim($request->label);
        $label->changed_label   = trim($request->changed_label);     
        $label->save();

        $msg = "Label Added Successfully.";
        $log = ActivityLog::createlog(Auth::Id(),"Language",$msg,Auth::Id());              

        Session::flash('success_msg', $msg);
        return redirect('/admin/language');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = LanguageTag::find($id);

        if(empty($page)) {
            Session::flash('error_msg', 'Data not found.');
            return redirect('/admin/Language');
        }
        return view('admin/Language/show', ['title_for_layout' => 'Page View', 'page' => $page]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = LanguageTag::find($id);
        if(empty($page)) {
            Session::flash('error_msg', 'Label not found.');
            return redirect('/admin/language');
        }

        return view('admin/Language/edit', ['title_for_layout' => 'Edit Label', 'page' => $page]);

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
            'label'           => 'required',
            'changed_label'   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('admin/language/' . $id . '/edit')
            ->withInput()
            ->withErrors($validator);
        }

        //Update Language Label
       
        $label                  = LanguageTag::find($id);
        $label->changed_label   = trim($request->changed_label);             
        $label->save();

        $msg = "Label Updated Successfully.";        
        Session::flash('success_msg', $msg);
        return redirect('/admin/language');
    }

    public function refresh()
    {
        $labals = DB::table('language_tags')
            ->orderBy('created_at')
            ->where('lang_code', 'de')
            ->pluck('changed_label', 'label');    

        if(count($labals) > 0)
        {
            $filePathDe = "public/app/lib/language/translation_label_de.js";
            $json = "var translationsDE = ".json_encode($labals);
            $file = fopen($filePathDe,'w+');
            $fileWrite = fwrite($file, $json);
            
            if($fileWrite && $fileWrite != "" && $fileWrite != 'undefiend' && $fileWrite != null)
            {
                Session::flash('success_msg',"Labels updated sucessfully, Use Ctrl+F5 to see the changes in frontend.");
            }
            else
            {
                Session::flash('error_msg',"Problem in label update"); 
            }          
        }
        else
        {
            Session::flash('error_msg',"No labels found"); 
        }
        
        return redirect('admin/language/');  
    } 
}
