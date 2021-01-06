<?php
namespace App\Http\Controllers\Api;
use App\Libraries\Miscellaneous;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Api\User;
use DB;
use Input;
use Validator;
use Hash;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Session;



class GlobalController extends Controller
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

    public function getLanguageData(){ 
       
        $labals = DB::table('language_tag')
            ->orderBy('created_at')
            ->where('lang_code', 'de')
            ->pluck('labal_de', 'labal');
       
        $labalsInSession = Session()->put('labals_data', $labals);

        $labalsFromSession = Session()->get('labals_data',$labals);
        
        
        return $labalsFromSession;     

    }

    public function getAllSkills(){

        $skills = DB::table('skills')
            ->select('id','skill')
            ->orderBy('skill')            
            ->where('status', 'Active')->get();

        return $this->resultapi('1','success',$skills);

    }

    public function getQualifications(){
    
       $qualifications = DB::table('qualifications')
            ->select('id','name')            
            ->where('status', '1')
            ->orderBy('orderno', 'asc')
            ->get();

        return $this->resultapi('1','success',$qualifications);

    }

    public function getAllCategories(){
    
       $categories = DB::table('categories')
            ->select('id','name')            
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        return $this->resultapi('1','success',$categories);

    }

    public function getPageDetails(Request $request){
        $pageId = $request->id;
        $pageDetails = DB::table('cms')
            ->select('title','description')
             ->where('id', $pageId)           
            ->first();

        return $this->resultapi('1','success',$pageDetails);

    }

    public function getAllBlogs(){        
        $allBlogs = DB::table('blogs')
            ->where('status', '1')
            ->orderBy('id')        
            ->get();

        return $this->resultapi('1','success',$allBlogs);

    }

    public function resultapi($status, $message, $result = array()) {
        
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        
        echo json_encode($finalArray);
        die;
    }
       

}

