<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Api\User;
use App\Delivery;
use Tymon\JWTAuth\JWTAuth;

class ApiLessonDeliveryController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth, Delivery $delivery) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        $this->delivery = $delivery;
    }

    // Get all delivery
    public function getAllDeliveries(Request $request) {

         // used to track result status
        $countFilter = count($request->all());

        // set lesson_delivery table, select id, name, status
        $deliveryDetails = DB::table('delivery')
        ->select('id', 'name', 'status')
        ->orderBy('id', 'ASC')
        ->where('status', 'Active')
        ->get();

        // check if any $deliveryDetails were returned from API call
        if(count($deliveryDetails) > 0) {
            // if $deliveryDetails found call resultApi with status set to 1, message set to string, result array set to $deliveryDetails
            $this->resultApi('1', 'Lesson Delivery Found', $deliveryDetails);
        } else {
            // if $deliveryDetails not found call resultapi with parameters: status set to 0, message set to string, result array set to activityDetails
            $this->resultApi('0', 'No Lesson Delivery Found', $deliveryDetails);
        }
    }

    public function getDeliveriesByBloomWebbActivity(Request $request) {
        if ($request->all() && count($request->all()) > 0) {
            $deliveries = Delivery::getDeliveriesByBloomWebbActivity($request);

            // if count greater than 0, than Webb was found
            if (count($deliveries) > 0) {
                $this->resultapi('1', 'Deliveries Found', $deliveries);
            } else {
                // If not found 
                $this->resultapi('0', 'No Deliveries Found', $deliveries);
            }
        }

    }

    // results from API request 
    public function resultApi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);

        exit;
    }

}