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
use App\Api\Payment; 
use App\JobDetail;

use App\EmailTemplates; 
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Support\Facades\Validator; 
use Hash;
use Illuminate\Support\Facades\URL;
use Mail; 

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

use File;
use Image;

class ApiClientController extends Controller {
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
    
    public function postClientData(Request $request){ 
    
        $users = DB::table('users')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->join('payments', 'users.id', '=', 'payments.user_id')
            ->select('users.*', 'user_profiles.*', 'payments.*','users.id as id')
            ->where('users.id',$request->id)
            ->first();
            
        if(count($users)>0){ 
            $this->resultapi('1','Profile Detials Updated Sucessfully',$users);
        }
    }

    // client document uploads for documents
    public function file_upload(Request $request){

        $profileImage = ""; 
        if($request->hasFile('file')) {
            $file = $request->file('file');
            foreach ($file  as $key => $value) {
            $img_name = $value->getClientOriginalName(); 

            $timestamp = time().  uniqid();
            $name = $timestamp. '-' .$value->getClientOriginalName();
            $profileImage = $name;
        
            $value->move(public_path().'/asset/Client/', $name);
            $file_data[] = $profileImage;

            $path = public_path().'/asset/Client/';
            $destinationPath = public_path('/asset/Client/thumb');
            $img = Image::make($path.$name);
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$name);
            //File::delete(public_path().'/asset/userImages/'.$user->profile_pic);
             // 
             # code...
            }
            $result_data = json_encode($file_data);
            $this->resultapi('1','Documents Updated Sucessfully',$result_data);  
        }else{
            $result_data = "";
            $this->resultapi('1','Documents Updated Sucessfully',$result_data); 
        }
            
    }
    // profile image upload for client profile image
    public function profile_upload(Request $request){
       
       $data = ($request->all());
       pr($data);exit;
        $profileImage = ""; 
        if($request->hasFile('profile_file')) {
            $file = $request->file('profile_file');
            foreach ($file  as $key => $value) {
                $img_name = $value->getClientOriginalName(); 

                $timestamp = time().  uniqid();
                $name = $timestamp. '-' .$value->getClientOriginalName();
                $profileImage = $name;
            
                $value->move(public_path().'/asset/Client/Profile', $name);
                $file_data[] = $profileImage;

                $path = public_path().'/asset/Client/Profile/';
                $destinationPath = public_path('/asset/Client/Profile/thumb');
                $img = Image::make($path.$name);
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$name);
                //File::delete(public_path().'/asset/userImages/'.$user->profile_pic);
                 // 
                 # code...
                //DB::enableQueryLog();
                //$userProfile =  User::where('id',$userId)->first();   

            }

            $result_data = json_encode($file_data);

            /*if(count($userProfile)>0 && $file_data!=""){
                $array_update = array("profile_image"=>$result_data);
                $userProfile_update = User::where('id',$userId)->update($array_update); 
            }*/
            
           // print_r(DB::getQueryLog());exit;
            $this->resultapi('1','Profile Updated Sucessfully',$result_data);  
            
        }else{
            $result_data = "";
            $this->resultapi('1','Profile Updated Sucessfully',$result_data); 
        }
            
    }

    
    public function postUpdateClient(Request $request){ 
           // $user = User::find($request->user_id);   
            $user_array = $request->user;  
            $userId = $user_array['id']; 

            //$user = User::find($userId);

            if($request->hasFile('profile_file')) {

                $file[] = $request->file('profile_file');

                foreach ($file  as $key => $value) { 
                    $img_name = $value->getClientOriginalName();  
                    $timestamp = time().  uniqid();
                    $name = $timestamp. '-' .$value->getClientOriginalName();
                    $profileImage = $name;
                
                    $value->move(public_path().'/asset/Client/Profile', $name);
                    $file_data = $profileImage;

                    $path = public_path().'/asset/Client/Profile/';
                    $destinationPath = public_path('/asset/Client/Profile/thumb');
                    $img = Image::make($path.$name);
                    $img->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$name);
                    //File::delete(public_path().'/asset/userImages/'.$user->profile_pic);
                     // 
                     # code...
                    //DB::enableQueryLog();
                    $userProfile_data =  User::where('id',$userId)->first();   

                }
                 
                $result_data = $file_data; 
                if(count($userProfile_data)>0 && $file_data!=""){
                    $array_update = array("profile_image"=>$result_data);
                    $userProfilepic_update = User::where('id',$userId)->update($array_update); 
                } 
                
            } 
             
            if($request->user && count($request->user))
            {
                $validator = Validator::make($request->user, [
                    'firstname'                  =>'required|max:100',
                    'lastname'                   =>'required|max:100',
                    'phone_number'               =>'required|max:10',
                    'street'                     =>'required',
                    'zipcode'                    =>'required|max:6',
                    'profile_description'        =>'required|max:500',
                    'job_title'                  =>'required', 
                    'stateId'                    =>'required',
                    'locationId'                 =>'required',
                    'countryId'                  =>'required', 
                    'vat_number'                 =>'required',
                    'commercial_register_number' =>'required' 
                   /* 'cc_name'                    =>'required',
                    'cc_cvv'                     =>'required',
                    'cc_number'                  =>'required', */
                    
                ]);
                if ($validator->fails()) { 
                    $this->resultapi('0','Fail',$validator->errors()->all());
                }
                else
                {  
                    $user = User::find($userId);
                    //pr($user);exit;
                    if($user && $user->status === "Active")
                    {

                        $user->firstname            = $user_array['firstname'];
                        $user->lastname             = $user_array['lastname']; 
                        $user->phone_number         = $user_array['phone_number'];
                        $user->status               = 'Active';
                        $user->usertype             = 'Company';
                        $user->is_company           = 'No'; 
                        $user->update(); 
                                         
                        $userProfile_array = array(  
                            "gender"                     => $user_array['gender'],
                            "countryId"                  => $user_array['countryId'],
                            "stateId"                    => $user_array['stateId'],
                            "locationId"                 => $user_array['locationId'],
                            "street"                     => $user_array['street'],
                            "birth_date"                 => $user_array['birth_date'],
                            "qualifications"             => $user_array['qualifications'],                
                            "language_id"                => 2,
                            "job_title"                  => $user_array['job_title'],
                            "zipcode"                    => $user_array['zipcode'],
                            "phone_number"               => $user_array['phone_number'],
                            "street"                     => $user_array['street'], 
                            "profile_description"        => $user_array['profile_description'],
                            "vat_number"                 => $user_array['vat_number'],
                            "commercial_register_number" => $user_array['commercial_register_number'],
                            "invoice_address"            => $user_array['invoice_address'],
                            "delivery_address"           => $user_array['delivery_address']
                        );
                        /*if(!empty($request->profile_image)){
                            $user_profile_pic = array("portfolio_images" => $request->portfolio_images);
                            $user_pic = User::where('id',$userId)->update($user_profile_pic);
                        }*/

                        if(!empty($user_array['portfolio_images'])){
                            $userProfile_array['portfolio_images'] = $user_array['portfolio_images'];
                            //print_r($request->exist_images);exit; 
                            if(!empty($user_array['exist_images'])){
                                 
                                if(!is_array($user_array['portfolio_images'])){
                                    $portfolio_images = json_decode($user_array['portfolio_images'],true);
                                }else{
                                    $portfolio_images = $user_array['portfolio_images'];
                                } 
                                $userProfile_array['portfolio_images'] = array_merge($portfolio_images,$user_array['exist_images']);
                            }
                            $userProfile_array['portfolio_images'] = json_encode($userProfile_array['portfolio_images']);
                        }
                          
                      
                $userProfile_update = UserProfiles::where('user_id',$userId)->update($userProfile_array);  

                        $Payments = Payment::where('user_id', $userId)->first();    
                        //pr($Payments);exit; 
                        if(count($Payments)>0){
                            $Payment_array = array(  
                                "cc_cvv" => $user_array['cc_cvv'],
                                "cc_name"=> $user_array['cc_name'],
                                "cc_no"  => $user_array['cc_no']  
                            ); 
                            $Payment_detail =  Payment::where('user_id',$userId)->update($Payment_array);   
                        }else{
                            $Payment = new Payment();  
                            $Payment->user_id    = $userId;
                            $Payment->cc_cvv     = $user_array['cc_cvv'];
                            $Payment->cc_name    = $user_array['cc_name'];
                            $Payment->cc_no  = $user_array['cc_no']; 
                            $Payment->save();    
                        }  
                        $userProfile = DB::table('users')
                        ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                        ->join('payments', 'users.id', '=', 'payments.user_id')
                        ->select('users.*', 'user_profiles.*','payments.cc_no','payments.cc_name','payments.cc_cvv')
                        ->where('users.id',$userId)->get();

                        // $userProfile =  UserProfiles::where('user_id',$userId)->first();   
                        $userdata = $userProfile[0];
                        //$userdata['profile_image'] =  User::select('profile_image')->where('id',$userId)->first(); 
                        $this->resultapi('1','Profile Details Updated Sucessfully',$userdata); 

                    }
                    else
                    {
                        $this->resultapi('0','Some Problem in Profile Update',null);
                    }

                }
            }
    }

    public function remove_file_image(Request $request){
        
        if(!empty($request->all())){
            $userId = $request->user_id;
            $image_name = $request->image_name;
            $userProfile_image =  UserProfiles::select('portfolio_images')->where('user_id',$userId)->first();   
            $images_array = json_decode($userProfile_image['portfolio_images'],true);
            foreach ($images_array as $key => $value) {
                if($image_name == $value){ 
                    $path = public_path().'/asset/userImages/';
                    $destinationPath = public_path('/asset/userImages/thumb/');
                    if(file_exists($path.$value) && file_exists($destinationPath.$value)){ 
                        File::delete($path.$value);
                        File::delete($destinationPath.$value);
                    }
                    //pr($images_array);exit;
                    $newimages_array = array_diff($images_array,array($value));
                    //echo public_path().'/asset/userImages/'.$value;exit; 
                }
            }
            $portfolio_images  =array('portfolio_images'=> json_encode(array_values($newimages_array))); 
            $userProfile_image =  UserProfiles::where('user_id',$userId)->update($portfolio_images); 

            $this->resultapi('1','Images deleted sucessfully',$portfolio_images); 
             
        }
    }

    public function postPostProject(Request $request){  

        if($request->all())
        {
            $validator = Validator::make($request->all(), [
                'job_category'           => 'required',
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
                'job_skills'             => 'required', 
                'job_visible_duration'   => 'required',
                'job_availble_for'       => 'required|max:255',
                'terms_conditions'       => 'required|integer',
            ]);

            if ($validator->fails()) 
            {
                $this->resultapi('0','Fail',$validator->errors()->all());
            }
            else
            {
                $job = new JobDetail;
                  
                $job->prop_id               = "";
                $job->user_id               = 1;
                $job->status                = "Active";
                $job->job_title             = $request->job_title;
                $job->job_subtitle          = $request->job_subtitle;
                $job->job_description       = $request->job_description;
                $job->job_comments          = $request->job_comments;
                $job->job_cost_min          = $request->job_cost_min;
                $job->job_cost_max          = $request->job_cost_max;
                $job->job_keywords          = $request->job_keywords;
                $job->job_stattime          = $request->job_stattime;
                $job->job_endtime           = $request->job_endtime;
                $job->terms_conditions      = $request->terms_conditions;
                $job->job_visible_duration  = $request->job_visible_duration;
                $job->job_category          = $request->job_category;
                $job->job_skills            = $request->job_skills;
                $job->job_availble_for      = $request->job_availble_for;
                $job->job_stage             = $request->job_stage;
              
               // job_images 
                //job_documents 
                //$job->save();
            }
        }


    }

    public function resultapi($status,$message,$result = array()) {
            $finalArray['STATUS'] = $status;
            $finalArray['MESSAGE'] = $message;
            $finalArray['RESULT'] = $result;
            echo json_encode($finalArray);
            die;
    }


}
