<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Hash;
use App\EmailTemplates; 
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\URL;
use Mail; 
use App\Api\User;
use App\Grade;

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiGradeController extends Controller {
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
	
	/*
     * Added By: Devang Mavani 
     * Reason: Get All Grades
    */

	public function getAllGrades(Request $request) {
        
        //$gradeDetails = Grade::where('status','Active')->get();    

        $gradeDetails = Grade::where('status','Active')
                ->orderBy(DB::raw("name * 1, name"), 'DESC')
                ->get();
        
        if(count($gradeDetails) > 0)
        {
            $this->resultapi('1','Grades Found', $gradeDetails);
        }
        else
        {                
            $this->resultapi('0','No Grades Found', $gradeDetails);
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
