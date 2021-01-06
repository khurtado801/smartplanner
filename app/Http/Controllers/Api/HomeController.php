<?php
namespace App\Http\Controllers\Api;
use App\Libraries\Miscellaneous;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Newsletter;
use App\JobDetail;
use DB;
use Input;
use Validator;
use Hash;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;


class HomeController extends Controller
{
    private $req;
    private $user;
    private $jwtAuth;

    function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth)
    {
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
    }
   
    public function getTopRatedJobs(){ 
    
        $topRatedJobs = DB::table('job_details')            
        ->select('id','job_title','job_images','job_cost_max')     
        ->orderBy('job_cost_max', 'desc')
        ->where('status','Active')
        ->take(7)
        ->get();

        if(count($topRatedJobs) > 0)
        {
            $this->resultapi('1','Projects Found', $topRatedJobs);
        }
        else
        {                
            $this->resultapi('0','No Projects Found', $topRatedJobs);
        }
    }

    public function getNewsletterSubscribe(Request $request){
       
        if($request->email && count($request->all()) > 0)
        {  
            $validator = Validator::make($request->all(), [

                'email'           =>'required|email|unique:newsletters',
            ]);
             
            if ($validator->fails()) 
            {
                $this->resultapi('0',$validator->errors()->all(), null);
            }
            else
            {
                $newsletter = new Newsletter;
                $newsletter->email   = trim($request->email);
                $newsletter->status  = 1;     
                $newsletter->save();

                $this->resultapi('1','Newsletter subscription Done.', 'true');
            }
        }
        else
        {
            $this->resultapi('0',' ! Problem in Newsletter subscription.', 'false');
        }
    }

    public function getSearchResult(Request $request){

        $searchtext     = "";
        $job_category   = "";
        $job_location   = "";

        if($request->all() && !empty($request->all()))
        {  
            $searchtext    = $request->searchtext;
            $job_category  = $request->job_category;
            $job_location  = "Aesch";

            $searchResults = jobdetail::where('job_title', 'LIKE', "{$searchtext}")
                ->orWhere('job_category', 'LIKE', "{$job_category}")
                ->orWhere('job_location', 'LIKE', "{$job_location}")
                ->select('id','job_title','job_skills','job_images','job_subtitle','job_cost_min','job_cost_max','job_description','job_availble_for') 
                ->orderBy('id', 'desc')
                ->where('status', 'Active')
                ->get();

                $msg = "Total ".count($searchResults)." Search Result(s) Found."; 

                $this->resultapi('1',$msg, $searchResults);
        }
        else
        {
             $this->resultapi('0',' ! Search Fields are not filled properly.', $searchResults);
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
