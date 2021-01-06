<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\EmailTemplates; 
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;
use Yajra\Datatables\Datatables;

class EmailTemplatesController extends Controller
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
        return view('admin/EmailTemplates/index', ['title_for_layout' => 'Email Templates']);
    }
    
    /**
     * Fetch data tobe used in datatable
    */
    public function getData() {
        return Datatables::of(EmailTemplates::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        return view('admin/EmailTemplates/create', ['title_for_layout' => 'Add Email Templates']);
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
                'subject' => 'required|max:255|unique:email_templates',
                'email_content'=> 'required',
        ]);
    
        $slug = EmailTemplates::slugify($request->subject);
        //$slug = EmailTemplates::slugify($request->subject); 

        if ($validator->fails()) {
            return redirect('/admin/emailtemplates/create')
                ->withInput()
                ->withErrors($validator);
        }


        //Save car
        $email_templates = new EmailTemplates;
        $email_templates->subject = $request->subject; 
        $email_templates->slug = $slug; 
        $email_templates->email_content = $request->email_content; 
        $email_templates->status = $request->status;
        $email_templates->save();
        
        /*//Save slider image 
        if($request->hasFile('main_image')) {

            $main_image = Input::file('main_image');
            $timestamp = time().  uniqid();
            $name = $timestamp. '-' .trim($main_image->getClientOriginalName());
            $main_image->move(storage_path().'/mainImages/', $name);

            $carupdate = Car::find($car->id);
            $carupdate->image = $name;
            $carupdate->save();
        }*/

        //Save 

        Session::flash('success_msg', 'EmailTemplate has been added successfully.');
        return redirect('/admin/emailtemplates');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        $EmailTemplates = EmailTemplates::find($id);
       // pr($EmailTemplates);exit;
         if(empty($EmailTemplates)) {  
                Session::flash('error_msg', 'EmailTemplate not found.');
                return redirect('/admin/emailtemplates');
         } 
         return view('admin/EmailTemplates/edit', ['title_for_layout' => 'Edit EmailTemplate', 'EmailTemplates' => $EmailTemplates]);
         
        
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
                'subject' => 'required|max:255unique:email_templates,id,'.$id,
                'email_content'=> 'required',
            ]);
             
            if ($validator->fails()) {  
                return redirect('admin/emailtemplates/'.$id.'/edit')
                    ->withInput()
                    ->withErrors($validator); 
            }

            //$subject = EmailTemplates::slugify($request->subject);           
            
            $data = array("subject" => $request->subject,"email_content"=>$request->email_content,'status' =>$request->status); 
            $result = DB::table('email_templates')->where("id",$id)->update($data); 
           
            Session::flash('success_msg', 'EmailTemplate has been updated successfully.');
            return redirect('/admin/emailtemplates');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
        //DB::table('email_templates')->where('id', $id)->delete();
        $email_templates = EmailTemplates::findOrFail($id);
        $email_templates->delete();
        $msg = 'EmailTemplate has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;exit;
    } 
}
