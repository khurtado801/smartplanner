<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Api\User;
use App\Activity;
use Tymon\JWTAuth\JWTAuth;
use App\Bloom;
use App\Webb;

class ApiActivityController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth, Activity $activity, Bloom $bloom, Webb $webb) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        $this->activity = $activity;
    }

    /**
     * Get all activities
     */
    public function getAllActivities(Request $request) {

        // used to track result status
        $countFilter = count($request->all());

        // set activities table, select all id-name-description-status
        $activityDetails = DB::table('activity')
        ->select('id', 'name', 'description', 'status')
        ->orderBy('id', 'ASC')
        ->where('status', 'Active')
        ->get();


        //  check if any $activityDetails were returned from API call
        if (count($activityDetails) > 0) {
            // if $activityDetails found call resultapi with parameters: status set to 1, message set to string, result array set to activityDetails
            $this->resultapi('1', 'Activities Found', $activityDetails);
        } else {
            // if $activityDetails not found call resultapi with parameters: status set to 0, message set to string, result array set to activityDetails
            $this->resultapi('0', 'No Activities Found', $activityDetails);
        }
    }

    public function getActivitiesByBloomAndWebb(Request $request) {
        if ($request->all() && count($request->all()) > 0) {
            $activities = Activity::getActivitiesByBloomAndWebb($request);

            // if count greater than 0, than Webb was found
            if (count($activities) > 0) {
                $this->resultapi('1', 'Activities Found', $activities);
            } else {
                // If not found 
                $this->resultapi('0', 'No Activities Found', $activities);
            }
        }
        
    }

    // used for results from api
    public function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);

        exit;
    }

}