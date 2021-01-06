<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use App\Api\User;
use App\EducationalQuote;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiEducationalQuotesController extends Controller {

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
     * Added By: Karnik
     * Reason: Get All EducationalQuotes
     */

    public function getAllEducationalQuotes(Request $request) {
        //echo "zzz"; exit;
        //$countFilter =  count($request->all());
        $educational_quotesDetails = DB::table('educational_quotes')
                ->select('id', 'quote_line1', 'quote_line2', 'author', 'status')
                ->orderBy(DB::raw('RAND()'))
                ->where('status', 'Active')
                ->first();
                //->random(1);

        //print_r($educational_quotesDetails); exit;
        if (count($educational_quotesDetails) > 0) {
            $this->resultapi('1', 'Educational Quote Found', $educational_quotesDetails);
        } else {
            $this->resultapi('0', 'No EducationalQuotes Found', $educational_quotesDetails);
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