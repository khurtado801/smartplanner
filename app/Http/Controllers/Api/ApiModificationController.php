<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Api\User;
use App\Modification;
use Tymon\JWTAuth\JWTAuth;

class ApiModificationController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth, Modification $modification) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        $this->wemodification = $modification;
    }

    /**
     * Get all Modifications
     */
    public function getAllModifications(Request $request) {

        // used to track result status
        $countFilter = count($request->all());

        $modificationDetails = DB::table('modification')
        ->select('id', 'name')
        ->orderBy('id', 'ASC')
        ->where('status', 'Active')
        ->get();

        // check if any $modificationDetailswere returned from API call
        if(count($modificationDetails) > 0) {
            // if $webbDetails found call resultApi with status set to 1, message set to string, result array set to $$modificationDetails
            $this->resultapi('1', 'Modifications Found', $modificationDetails);
        } else {
            // if $modificationDetails not found call resultapi with parameters: status set to 0, message set to string, result array set to $modificationDetails
        $this->resultapi('0', 'No Modifications Found', $modificationDetails);
        }
    }

    public function getModificationsByStandards(Request $request) {
        if ($request->all() && count($request->all()) > 0) {
            $deliveries = Modification::getModificationsByStandards($request);

            // if count greater than 0, than Webb was found
            if (count($deliveries) > 0) {
                $this->resultapi('1', 'Modifications Found', $deliveries);
            } else {
                // If not found 
                $this->resultapi('0', 'No Modifications Found', $deliveries);
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