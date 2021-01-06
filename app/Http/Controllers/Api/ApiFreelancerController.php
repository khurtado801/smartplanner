<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Api\User;
use App\UserProfiles;
use App\Payments;
use App\Skills;
use App\Category;
use Illuminate\Support\Facades\Hash;
use App\EmailTemplates; 
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\URL;
use Mail; 

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiFreelancerController extends Controller {
    private $req;
    private $user;
    private $jwtAuth;
    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth)
    {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
    }

    public function getChangePasssword(Request $request){

        if($request->user_id && $request->all() && count($request->all()) > 0)
        {      
            $validator = Validator::make($request->all(), [

                'user_id'           =>'required|numeric',
                'old_password'      =>'required|max:255|min:6',
                'password'          =>'required|max:255|min:6',
                'confirm_password'  =>'required|max:255|min:6|same:password',
                
            ]);
             
            if ($validator->fails()) 
            {
                $this->resultapi('0',$validator->errors()->all(), null);
            }
            else
            {
                $userId = $request->user_id;
                $user = User::find($userId);
                
                if(Hash::check($request->old_password, $user->password))
                {                    
                    $user->password  =  bcrypt($request->password);
                    $user->save();

                    $this->resultapi('1','Password Updated Sucessfully',null);
                   
                }
                else
                {
                     $this->resultapi('0','Wrong Old password',null);
                }
            }
        }
        else
        {
            $this->resultapi('0','Operation not not possible',null);
        }
    }

    public function postUpdateCompany(Request $request){
        
        if($request->user_id && $request->all() && count($request->all()) > 0)
        {
            
            $validator = Validator::make($request->all(), [

                'user_id'                    =>'required|numeric',
                'company_no_of_employer'     =>'required|max:5',
                'company_type'               =>'required',
                'vat_number'                 =>'required|max:100',
                'commercial_register_number' =>'required|max:100',
                'company_address'            =>'required',
            ]);
             
            if ($validator->fails()) 
            {
                $this->resultapi('0',$validator->errors()->all(), null);
            }
            else
            {
                $userId = $request->user_id;

                $userProfile = UserProfiles::where("user_id","=", $userId)->first();
                $userProfile->company_no_of_employer        = $request->company_no_of_employer;
                $userProfile->company_type                  = $request->company_type;
                $userProfile->vat_number                    = $request->vat_number;
                $userProfile->commercial_register_number    = $request->commercial_register_number;
                $userProfile->company_address               = $request->company_address;

                $user = User::find($userId);
                $user->is_company                           = "Yes";

                $user->save();
                $userProfile->save(); 
                 
                if($userProfile->save() &&  $user->save())
                {
                    $this->resultapi('1','Company Details Updated Sucessfully',$userId);
                }
                else
                {
                    $this->resultapi('0','Company Details Not Updated',$userId);
                }
            }         
        }
        else
        {
            $this->resultapi('0','Company Details Not Updated',$userId);
        }
    }
       
    public function postUpdateFreelancer(Request $request){

        if($request->all() && count($request->all()) > 0)
        {
            if($request->user_id && $request->user_id !="" && $request->user_id !="undefiend")
            {
                $userId = $request->user_id;
                $validator = Validator::make($request->all(), [

                    'user_id'                    =>'required',
                    'firstname'                  =>'required|max:100',
                    'lastname'                   =>'required|max:100',
                    'phone_number'               =>'required|max:10',
                    'street'                     =>'required',
                    'zipcode'                    =>'required|max:6',
                    'job_title'                  =>'required|max:100',
                    'profile_description'        =>'required',                   
                    'invoice_adress'             =>'required',
                    'delivery_adress'            =>'required',
                    'gender'                     =>'required',
                    'hourly_rate'                =>'required|max:5',
                    'skills'                     =>'sometimes|required',                
                    'website'                    =>'required',
                    'videos'                     =>'required',
                    'birth_date'                 =>'required',
                    'countryId'                  =>'required',
                    'stateId'                    =>'required',
                    'cityId'                     =>'required',
                    'street'                     =>'required',
                    'zipcode'                    =>'required',
                    'profile_image'              =>'required',

                ]);

                if ($validator->fails()) 
                {
                    $this->resultapi('0',$validator->errors()->all(), null);
                }
                else
                {
                    
                    $user = User::find($userId);

                    $user->status               = $user->status;
                    $user->usertype             = $user->usertype;;
                    $user->is_company           = $user->is_company;;
                    $user->is_payment_updated   = $user->is_payment_updated;;
                    $user->is_profile_updated   = 1;

                    $user->firstname            = $request->firstname;
                    $user->lastname             = $request->lastname;
                    $user->phone_number         = $request->phone_number;

                    if($request->profile_image && $request->profile_image != "")
                    {
                        $destinationPath = storage_path().'/uploads/freelancer/profile/';
                        $extension = $request->profile_image->getClientOriginalExtension();
                        $fileName = rand(11111,99999).'.'.$extension; // renameing image
                        $request->profile_image->move($destinationPath, $fileName);
                    }

                    $user->profile_image         = $fileName;

                                

                    $userProfile = UserProfiles::where("user_id","=", $userId)->first();                   
                
                    $userProfile->gender                        = $request->gender;
                    $userProfile->countryId                     = $request->countryId;
                    $userProfile->stateId                       = $request->stateId ;
                    $userProfile->locationId                    = $request->locationId;
                    $userProfile->street                        = $request->street;
                    $userProfile->birth_date                    = $request->birth_date ;
                    $userProfile->qualifications                = $request->qualifications;
                    $userProfile->skills                        = json_encode($request->skills);
                    $userProfile->job_title                     = $request->job_title;
                    $userProfile->zipcode                       = $request->zipcode;
                    $userProfile->street                        = $request->street;
                    $userProfile->profile_description           = $request->profile_description;
                    $userProfile->invoice_adress                = $request->invoice_adress;
                    $userProfile->delivery_adress               = $request->delivery_adress;
                    $userProfile->website                       = $request->website;
                    $userProfile->videos                        = $request->videos;                    
                    $userProfile->school_gratuation             = $request->school_gratuation;
                  
                    $user->save();
                    $userProfile->save(); 
                     
                    if($userProfile->save() &&  $user->save())
                    {
                        $this->resultapi('1','Profile Updated Sucessfully',$userId);
                    }
                    else
                    {
                        $this->resultapi('0','Profile Not Updated',$userId);
                    }
                }
            }
            else
            {
                $this->resultapi('0','Session Expired please login and try again',$userId);
                 
            }
        }
        else
        {
            $this->resultapi('0','Due to some problem please login and try again',$userId);           
        }
    }


    public function getFreelancerDetails(Request $request) {
       
        $userId = $request->input('user_id');
        if($userId && $userId != "")
        {
            $user = DB::table('users')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->join('payments', 'users.id', '=', 'payments.user_id')
            ->select('users.*', 'user_profiles.*', 'payments.*')
            ->where('users.id',$userId)
            ->first();

            $this->resultapi('1','Profile Update', $user);
        }

    }

    public function getAllProjects(Request $request) {
        
        $countFilter =  count($request->all());

        if($countFilter && $countFilter > 0)
        {

        }
        else
        {
           $jobDetails = DB::table('job_details')            
            ->select('id','job_title','job_skills','job_images','job_documents','job_subtitle','job_cost_min','job_cost_max','job_description','job_availble_for')     
            ->orderBy('id', 'desc')
            ->where('status','Active')
            ->get();

           /* echo "<pre>";
            print_r($jobDetails);*/



            if(count($jobDetails) > 0)
            {
                $this->resultapi('1','Projects Found', $jobDetails);
            }
            else
            {                
                $this->resultapi('0','No Projects Found', $jobDetails);
            }
        }
    }

    public function getProjectDetailsById(Request $request) {

        if($request->all() && count($request->all()) > 0)
        {
            $jobDetails = DB::table('job_details')
            ->where('status','Active')
            ->where('id',$request->pid)           
            ->get(); 

            if(count($jobDetails) > 0)
            {
                //foreach ($jobDetails as $jobDetail) {
                   
                    $skills     = $jobDetails[0]->job_skills;
                    $category   = $jobDetails[0]->job_category;
                    $images     = $jobDetails[0]->job_images;
                    $documents  = $jobDetails[0]->job_documents;
                //}

                $imagesDecodeJson       = json_decode($images, TRUE);
                $documentsDecodeJson    = json_decode($documents, TRUE);
                $skillsDecodeJson       = json_decode($skills, TRUE);
                $categoryDecodeJson     = json_decode($category, TRUE);
               
                $allSkills = Skills::whereIn('id',$skillsDecodeJson)
                ->select('skill')
                ->where('status', 'Active')
                ->get();

                $allCategories = Category::whereIn('id', $categoryDecodeJson)
                ->select('name')
                ->where('status', 'Active')
                ->get();

                $jobDetails['skills']           = $allSkills;
                $jobDetails['categories']       = $allCategories;
                $jobDetails['job_images']       = $imagesDecodeJson;
                $jobDetails['job_documents']    = $documentsDecodeJson;

                $this->resultapi('1','Projects Found', $jobDetails);
            }
            else
            {                
                $this->resultapi('0','No Projects Found', $jobDetails);
            }
        }
    }

    public function getSkillsByArray(Request $request){ 
        
        if($request->skillsArray && count($request->skillsArray) > 0)
        {
            $data = json_decode($request->skillsArray, TRUE);
            $models = Skills::whereIn('id', $data)
            ->select('skill')
            ->where('status', 'Active')
            ->get();

            /*foreach ($models as $skills) {
                $skillsArray[] = $skills->skill;
            }

            $finalString = implode(" ",$skillsArray);*/

            return $this->resultapi('1','success',$models);
        }
        else
        {
            return $this->resultapi('0','success',null);
        }
        
    }

    

    public function resultapi($status,$message,$result = array()) {
            $finalArray['STATUS'] = $status;
            $finalArray['MESSAGE'] = $message;
            $finalArray['DATA'] = $result;
            echo json_encode($finalArray);

            exit;
            
    }



}
