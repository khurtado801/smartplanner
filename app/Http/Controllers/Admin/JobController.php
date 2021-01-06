<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\JobDetail;
use App\User;
use App\Category;
use App\Skills;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin/Job/index', ['title_for_layout' => 'All Jobs']);
    }

    
    /**
     * Fetch data tobe used in datatable
    */
    public function getData() {
        return Datatables::of(JobDetail::query())->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $usertype = [
            'Freelancer'          => 'Freelancer',
            'Company'             => 'Company',
            'Company,Freelancer'  => 'Freelancer & Company',            
        ];

        $jobstage = [

            'Starting'    => 'Starting',
            'Pending'     => 'Pending',
            'Processing'  => 'Processing',
            'Finishing'   => 'Finishing',            
        ];

        $categories = DB::table('categories')
        ->orderBy('name')
        ->where('status', 'Active')
        ->pluck('name', 'id');

        $skills = DB::table('skills')
        ->orderBy('skill')
        ->where('status', 'Active')
        ->pluck('skill', 'id');

        return view('admin/Job/create', ['title_for_layout' => 'Post a Job', 'categories' => $categories, 'usertype' => $usertype, 'skills' => $skills , 'jobstage' => $jobstage]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function uploadDocuments($files)
    {
        $uploadcount = 0;                   
        $job_documnets = array();
        foreach($files as $file)
        {
            $rules = array('file' => 'mimes:txt,pdf,doc,docx,xls,xlsx,odt,jpg,jpeg,png,gif||max:2048');
            $validator = Validator::make(array('file'=> $file), $rules);
            if($validator->passes())
            {
                $destinationPath = storage_path().'/jobDocuments/';                        
                $timestamp = time().  uniqid();
                $filename = $timestamp. '_' .trim($file->getClientOriginalName());
                $upload_success = $file->move($destinationPath, $filename);
                $job_documnets[] = $filename;
                $uploadcount ++;
            }
            else
            {
                return Redirect::to('/admin/job/create')->withInput()->withErrors($validator);
               
            }
        }

        return $job_documnets;
    }

    public function getSkills($selectedSkills)
    {
        $checkSelectedSkills = count(array_filter($selectedSkills));

        if($checkSelectedSkills > 0){

            $skillsInJson = json_encode(array_filter($selectedSkills));

        } else {

            $skillsInJson = "";
        }

        return $skillsInJson;
    }

    public function store(Request $request)
    {         
        $authUser =  $this->isAuthUser($request);
        if($authUser == 1)
        { 
            /* validation for add skills */
            $selectedSkills = $request->job_skills;
            if($selectedSkills && !empty($selectedSkills))
            {                
                $skillsInJson = $this->getSkills($selectedSkills);
            } else {
                $skillsInJson = "";
            }

            $request->request->add(['job_skills' => $skillsInJson]);
            /* validation for add skills */

            $validator = Validator::make($request->all(), [                

                'job_category'           => 'required|integer',
                'job_title'              => 'required|max:255',
                'job_subtitle'           => 'required|max:255',
                'job_keywords'           => 'required|max:255',
                'job_stage'              => 'required',
                'job_description'        => 'required|max:255',
                'job_comments'           => 'required|max:255',
                'job_cost_min'           => 'required|integer',
                'job_cost_max'           => 'required|integer',
                'job_stattime'           => 'required',
                'job_endtime'            => 'required',
                'job_skills'             => 'required|max:255', 
                'job_visible_duration'   => 'required',
                'job_availble_for'       => 'required|max:255',
                'status'                 => 'required',
                'terms_conditions'       => 'required|integer',
            ]);

            if ($validator->fails()) {
                return redirect('/admin/job/create')
                ->withInput()
                ->withErrors($validator);
            }

            /* Uploading Documents Finish*/
            $allDocuments = $request->documents;
            if($allDocuments && !empty($allDocuments))
            {                
                $files = array_filter(Input::file('documents'));
                $file_count = count($files);
                
                if($file_count > 0)
                {
                    $job_documnets = $this->uploadDocuments($files);
                    $documentsInJson = json_encode($job_documnets);
                }
                else
                {
                    $documentsInJson = "[]";
                }
            }
            /* Uploading Documents Finish */
            
            $job = new JobDetail;

            $job->user_id               = Auth::Id();
            $job->job_category          = $request->job_category;
            $job->job_title             = $request->job_title;
            $job->job_subtitle          = $request->job_subtitle;
            $job->job_keywords          = $request->job_keywords;        
            $job->job_cost_min          = $request->job_cost_min;
            $job->job_cost_max          = $request->job_cost_max;
            $job->job_stattime          = $request->job_stattime;
            $job->job_endtime           = $request->job_endtime;
            $job->job_skills            = $skillsInJson ;
            $job->job_visible_duration  = $request->job_visible_duration;
            $job->job_description       = $request->job_description;
            $job->job_comments          = $request->job_comments;
            $job->job_availble_for      = $request->job_availble_for;
            $job->status                = $request->status;
            $job->job_documents         = $documentsInJson;            
            $job->terms_conditions      = $request->terms_conditions;
            $job->save(); 

            Session::flash('success_msg', 'Job added successfully.');
            return redirect('/admin/job');

        } else {

            Session::flash('error_msg', 'you are not a authorized person .');
            return redirect('/admin/login');

        }        
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jobDetail = JobDetail::find($id);

        if(!empty($jobDetail))
        {
            $userId         = $jobDetail->user_id;
            $jobCategoryId  = $jobDetail->job_category;
            $userName       = "N/A";
            $jobCategory    = "N/A";
            
            /* Job Poster name view */
            if($userId  && $userId > 0 && $userId != null)
            {
                $userData   = User::find($userId);
                $userName   = $userData->firstname.' '.$userData->lastname;
            }

            /* category */
            if($jobCategoryId && $jobCategoryId > 0 && $jobCategoryId != null)
            {
                $categoryData = Category::find($jobCategoryId);
                $jobCategory  = $categoryData->name;
            }

            /* skills view */
            $skills = $jobDetail->job_skills;
            $skillsArray = array_filter(json_decode($skills, true));
            if(!empty($skillsArray))
            {  
                foreach ($skillsArray as $key => $value)
                {               
                    $skillsData   = skills::find($value);
                    if(count($skillsData) > 0);
                    {
                        $finsSkills[] = $skillsData->skill;
                    }
                }           
                $allSkills =  implode(', ', $finsSkills);            
            }
            else
            {
                $allSkills = "N/A";
            }

            /* uploded job document view */
            $jobDetail->job_documents;
            $documents = $jobDetail->job_documents;
            $jobDocArray = array_filter(json_decode($documents, true));
            if(!empty($jobDocArray))
            {
                $jobDocArray == $jobDocArray;
            }
            else
            {
                $jobDocArray = "N/A";
            }  
        }
        else
        {
            Session::flash('error_msg', 'Job not found.');
            return redirect('/admin/job');
        }

        return view('admin/Job/show',[

            'title_for_layout'  => 'Job Details',
            'jobdeatils'        => $jobDetail,
            'username'          => $userName ,
            'jobcategory'       => $jobCategory,
            'allSkills'         => $allSkills,
            'jobDocArray'       => $jobDocArray
           

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
            $jobDetail = JobDetail::find($id);
            if(empty($jobDetail)) {
                Session::flash('error_msg', 'Sub Category not found.');
                return redirect('/admin/job');
            }

            $usertype = [
                'Freelancer'          => 'Freelancer',
                'Company'             => 'Company',
                'Company,Freelancer'  => 'Freelancer & Company',            
            ];

            $jobstage = [

                'Starting'    => 'Starting',
                'Pending'     => 'Pending',
                'Processing'  => 'Processing',
                'Finishing'   => 'Finishing',            
            ];

            $categories = DB::table('categories')
            ->orderBy('name')
            ->where('status', 'Active')
            ->pluck('name', 'id');

            $skills = DB::table('skills')
            ->orderBy('skill')
            ->where('status', 'Active')
            ->pluck('skill', 'id');

            /* uploded job document view */
            $documents = $jobDetail->job_documents;
            if($documents && !empty($documents))
            {
                $documents = $jobDetail->job_documents;
                $jobDocArray = array_filter(json_decode($documents, true));
            }
            else
            {
                $jobDocArray = "";
            }
                      
            return view('admin/Job/edit', [
                'title_for_layout' => 'Edit Job Details',
                'categories'       => $categories,
                'jobDetail'        => $jobDetail,
                'skills'           => $skills,
                'jobstage'         => $jobstage,
                'usertype'         => $usertype,                
                'jobDocArray'      => $jobDocArray,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        if(Auth::check())
        {           
            $job = JobDetail::find($id);
            $userId     = Auth::Id();
            $userToken  =  Session::token(); 
            $formToken  = $request->_token;

            if($userToken === $formToken)
            { 
                /* validation for add skills */
                $selectedSkills = $request->job_skills;
                if($selectedSkills)
                {
                    $checkSelectedSkills = count(array_filter($selectedSkills));

                    if($checkSelectedSkills > 0){

                        $skillsInJson = json_encode(array_filter($selectedSkills));

                    } else {

                        $skillsInJson = "";
                    }

                } else {

                    $skillsInJson = "";
                }

                /* Uploading Documents */
                $files = Input::file('documents');               
                $file_count = count($files);
                $documentsInJson = "";
                if($file_count > 0)
                {
                    $uploadcount = 0;                   
                    $job_documnets = array();
                    foreach($files as $file)
                    {
                        $rules = array('file' => 'mimes:txt,pdf,doc,docx,xls,xlsx,odt|max:5120');
                        $validator = Validator::make(array('file'=> $file), $rules);
                        if($validator->passes())
                        {
                            $destinationPath = storage_path().'/jobDocuments/';                        
                            $timestamp = time().  uniqid();
                            $filename = $timestamp. '_' .trim($file->getClientOriginalName());
                            $upload_success = $file->move($destinationPath, $filename);
                            $job_documnets[] = $filename;
                            $uploadcount ++;
                        }
                        else
                        {
                            return Redirect::to('/admin/job/create')->withInput()->withErrors($validator);

                        }
                    }
                    $documentsInJson = json_encode($job_documnets);
                }
                else
                {
                    $documentsInJson = $job->job_documnets;
                }

                /* Uploading Images */
                $images = Input::file('job_images');
                $image_count = count($images);               
                $imagesInJson = "";
                if($image_count > 0)
                {  
                    $uploadcount = 0;                    
                    $job_images = array();
                    foreach($images as $file)
                    {
                        $rules = array('file' => 'mimes:jpg,jpeg,png,gif|max:1024');
                        $validator = Validator::make(array('file'=> $file), $rules);
                        if($validator->passes())
                        {
                            $destinationPath = storage_path().'/jobImages/';                        
                            $timestamp = time().  uniqid();
                            $filename = $timestamp. '_' .trim($file->getClientOriginalName());
                            $upload_success = $file->move($destinationPath, $filename);
                            $job_images[]   = $filename;
                            $uploadcount ++;
                        }
                        else
                        {
                            return Redirect::to('/admin/job/create')->withInput()->withErrors($validator);

                        }
                    }

                    $imagesInJson = json_encode($$imagesInJson);
                }
                else
                {
                    $imagesInJson = $imagesInJson;
                }           

                $validator  = Validator::make($request->all(), [                

                    'job_category'           => 'required|integer',
                    'job_title'              => 'required|max:255',
                    'job_subtitle'           => 'required|max:255',
                    'job_keywords'           => 'required|max:255',
                    'job_stage'              => 'required',
                    'job_description'        => 'required|max:255',
                    'job_comments'           => 'required|max:255',
                    'job_cost_min'           => 'required|integer',
                    'job_cost_max'           => 'required|integer',
                    'job_stattime'           => 'required',
                    'job_endtime'            => 'required',
                    'job_skills'             => 'required|max:255', 
                    'job_visible_duration'   => 'required',
                    'job_availble_for'       => 'required|max:255',
                    'status'                 => 'required',
                    'terms_conditions'       => 'required|integer',
                    ]);

                if ($validator->fails()){
                    return redirect('admin/job/' . $id . '/edit')
                    ->withInput()
                    ->withErrors($validator);
                }
                else
                {                    

                    $job->user_id               = $userId;
                    $job->job_category          = $request->job_category;
                    $job->job_title             = $request->job_title;
                    $job->job_subtitle          = $request->job_subtitle;
                    $job->job_keywords          = $request->job_keywords;        
                    $job->job_cost_min          = $request->job_cost_min;
                    $job->job_cost_max          = $request->job_cost_max;
                    $job->job_stattime          = $request->job_stattime;
                    $job->job_endtime           = $request->job_endtime;
                    $job->job_skills            = $skillsInJson ;
                    $job->job_visible_duration  = $request->job_visible_duration;
                    $job->job_description       = $request->job_description;
                    $job->job_comments          = $request->job_comments;
                    $job->job_availble_for      = $request->job_availble_for;
                    $job->status                = $request->status;
                    $job->job_documents         = $documentsInJson;
                    $job->job_images            = $imagesInJson;
                    $job->terms_conditions      = $request->terms_conditions;
                    $job->save();
                }


                if($job->save()) {

                    Session::flash('success_msg', 'Job added successfully.');
                    return redirect('/admin/job');

                } else {

                    Session::flash('error_msg', 'Problem in form submission please try again.');
                    return redirect('/admin/job');

                }

                Session::flash('error_msg', 'Job added successfully.');
                return redirect('/admin/job');

            } else {

                Session::flash('error_msg', 'you are not a authorized person for post job.');
                return redirect('/admin/login');

            }
        }
        else
        {
            Session::flash('error_msg', 'Yoo have to login as a admin for post the job.');
            return redirect('/admin/login');
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jobDetails = JobDetails::findOrFail($id);
        $jobDetails->delete();
        echo 1;exit;
    }

    public function isAuthUser($request)
    {        
        if(Auth::check())
        {           
            $userId     = Auth::Id();
            $userToken  = Session::token(); 
            $formToken  = $request->_token;

            if($userToken === $formToken)
            {
                $isAuthUser = 1;
            }
            else
            {
                $isAuthUser = 0;
            }
        } 
        else
        {
            $isAuthUser = 0;
        }
        return $isAuthUser;
    }

    


}
