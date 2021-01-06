<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Api\User;
use App\Bloom;

use Tymon\JWTAuth\JWTAuth;
use function GuzzleHttp\json_encode;

class ApiBloomController extends Controller {
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

    /**
     * Get all Blooms Taxonomy function
     */
    public function getAllBlooms(Request $request) {

        // $bloomDetails = Bloom::where('status', 'Active')
        $bloomDetails = DB::table('blooms')
        ->orderBy('id', 'ASC')
        ->get();

        if(count($bloomDetails) > 0) {
            $this->resultapi('1', 'Blooms Taxonomy Names Found', $bloomDetails);
        }
        else {
            $this->resultapi('0', 'No Blooms Taxonomy Names Found', $bloomDetails);
        }

    }

    /**
     * Resultapi function for API call results, returns array
     */
    public function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode(($finalArray));

        exit;
    }
}