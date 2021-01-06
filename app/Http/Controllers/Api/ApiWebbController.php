<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Api\User;
use App\Webb;
use Tymon\JWTAuth\JWTAuth;

class ApiWebbController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth, Webb $webb) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        $this->webb = $webb;
    }

    /**
     * Get all Webb levels
     */
    public function getAllWebbs(Request $request) {

         // used to track result status
        $countFIlter = count($request->all());

  
        $webbDetails = DB::table('webbs')
        ->select('id', 'level', 'description')
        ->orderBy('id', 'ASC')
        ->where('status', 'Active')
        ->get();


        // check if any $webbDetails were returned from API call
        if(count($webbDetails) > 0) {
            // if $webbDetails found call resultApi with status set to 1, message set to string, result array set to $webbDetails
            $this->resultapi('1', 'Webbs Found', $webbDetails);
        } else {
            // if $webbDetails not found call resultapi with parameters: status set to 0, message set to string, result array set to webbDetails
        $this->resultapi('0', 'No Webbs Found', $webbDetails);
        }
    }

    // public function getAllWebbs(Request $request) {

    //     return DB::table('webbs as wb')
    //     ->join('blooms_webbs as bw', 'bw.webb_id', '=', 'wb.id')
    //     ->join('blooms as bl', 'bl.id', '=', 'bw.bloom_id')
    //     ->select('wb.level as webb_level', 'wb.id as wb_id')
    //     ->where('bw.bloom_id', '=', '1')
    //     ->get();
    // }

    /**
     * Used to get webb level associated with bloom
     */
    public function getWebbByBloom(Request $request) {
        if ($request->all() && count($request->all()) > 0) {
            $webbs = Webb::getWebbByBloom($request->bloom_id);

            // if count greater than 0, than Webb was found
            if (count($webbs) > 0) {
                $this->resultapi('1', 'Webbs Found', $webbs);
            } else {
                // If not found 
                $this->resultapi('0', 'No Webbs Found', $webbs);
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