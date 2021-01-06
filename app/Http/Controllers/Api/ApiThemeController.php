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
use App\Theme;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiThemeController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
    }

    /*
     * Added By: Devang Mavani 
     * Reason: Get All Themes
     */

    public function getAllThemes(Request $request) {

        $countFilter = count($request->all());

        $themeDetails = DB::table('themes')
                ->select('id', 'name', 'status')
                ->orderBy('id', 'desc')
                ->where('status', 'Active')
                ->get();

        if (count($themeDetails) > 0) {
            $this->resultapi('1', 'Themes Found', $themeDetails);
        } else {
            $this->resultapi('0', 'No Themes Found', $themeDetails);
        }
    }

    /*
     * Added By: Devang Mavani 
     * Reason: Get Themes By Grade Id AND Subject Id
     */

    public function getThemesByGradeAndSubjects(Request $request) {

        if ($request->all() && count($request->all()) > 0) {
            $themes_selected = Theme::getThemesByGradeAndSubjects($request->grade_id,$request->subject_id);
            
            if (count($themes_selected) > 0) {
                $this->resultapi('1', 'Themes Found', $themes_selected);
            } else {
                $this->resultapi('0', 'No Themes Found', $themes_selected);
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
