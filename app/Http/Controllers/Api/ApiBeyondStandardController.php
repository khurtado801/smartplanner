<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Api\User;
use App\Standard;
use Tymon\JWTAuth\JWTAuth;

class ApiBeyondStandardController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth, Standard $standard) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        $this->standard = $standard;
    }

    /**
     * Get all Beyond the Standards
     */
    public function getAllBeyonds(Request $request) {

         // used to track result status
         $countFIlter = count($request->all());

         $beyondStandardsDetails = DB::table('standard')
         ->select('id', 'name')
         ->orderBy('id', 'ASC')
         ->where('status', 'Active')
         ->get();

        // check if any  $beyondStandardsDetails were returned from API call
        if(count( $beyondStandardsDetails) > 0) {
            // if $webbDetails found call resultApi with status set to 1, message set to string, result array set to  $beyondStandardsDetails
            $this->resultapi('1', 'Beyond the Standards Found',  $beyondStandardsDetails);
        } else {
            // if  $beyondStandardsDetailsnot found call resultapi with parameters: status set to 0, message set to string, result array set to  $beyondStandardsDetails
        $this->resultapi('0', 'No Beyond the Standards Found',  $beyondStandardsDetails);
        }
    }

    public function getStandardsByDelivery(Request $request) {
        if ($request->all() && count($request->all()) > 0) {
            $deliveries = Standard::getStandardsByDelivery($request);

            // if count greater than 0, than Webb was found
            if (count($deliveries) > 0) {
                $this->resultapi('1', 'Beyond The Standards Found', $deliveries);
            } else {
                // If not found 
                $this->resultapi('0', 'No Beyond The Standards Found', $deliveries);
            }
        }

    }

    // results from API request 
    public function resultapi($status, $messsage, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $messsage;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);

        exit;
    }

}
