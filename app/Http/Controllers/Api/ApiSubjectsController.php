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
use App\Subject;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiSubjectsController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth, Subject $subject) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        $this->subject = $subject;
    }

    /*
     * Added By: Devang Mavani 
     * Reason: Get All Subjects
     */

    public function getAllSubjects(Request $request) {

        $countFilter = count($request->all());

        $subjectDetails = DB::table('subjects')
                ->select('id', 'name', 'status')
                ->orderBy('id', 'desc')
                ->where('status', 'Active')
                ->get();
        
        if (count($subjectDetails) > 0) {
            $this->resultapi('1', 'Subjects Found', $subjectDetails);
        } else {
            $this->resultapi('0', 'No Subjects Found', $subjectDetails);
        }
    }

    /*
     * Added By: Devang Mavani 
     * Reason: Get Subject By Grade Id
     */

    public function getSubjectsByGrade(Request $request) {

        if ($request->all() && count($request->all()) > 0) {
            
            $subjects = Subject::getSubjectsByGrade($request->grade_id);
            
            if (count($subjects) > 0) {
                $this->resultapi('1', 'Subjects Found', $subjects);
            } else {
                $this->resultapi('0', 'No Subjects Found', $subjects);
            }
        }
    }

    public function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);

        exit;
    }

}
