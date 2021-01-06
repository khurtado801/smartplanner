<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Plan;
use Stripe\Subscription;
use Stripe\Card;
use App\PaymentPlan;
use App\ActivityLog;
use App\UserSubscribe;


class StripePaymentController extends Controller
{

    /**
     * Set Stripe API keys
     * @param
     * string $_StripeKey
     */

     private $_StripeKey = 'sk_test_jADDmKxvpdyxUDaf2fceOhKR00FyXAmSM0';

         public function addApiKey(Request $request, Response $response, Next $next) {
        Stripe::setApiKey(getenv('STRIPE_SERCRET_KEY'));
        return $next($request, $response);
    }

    public function setup(Request $request, Response $response, array $args) {
        $pub_key = getenv('STRIPE_PUBLIC_KEY');
        return $response -> withJson(['publicKey' => $pub_key, 'subscriptionPlan' => getenv('planId')]);
    }

    // public function createCheckoutSession(Request $request, Response $response, array $args) {
    //     $checkout_session = \Stripe\Checkout\Session::create([
    //        'success_url' => '//localhost:8080/#/login?session_id={CHECKOUT_SESSION_ID}',
    //        'cancel_url' => '//localhost:8080/#/',
    //        'payment_method_types' => ['card'],
    //        'subscription_data' => ['items' => [[
    //            'plan' =>'planId',
    //            'quantity' => '1'
    //        ]]],
    //        'billingAddressCollection' => 'required'
    //     ]);
    // }


    /**
     * Used to get all plans
     */
    public function getAllPlans() {
        // Return all plans
        $plan_all  = PaymentPlan::getAllStripePans();
  
        // Check if any plans found
        if (count($plan_all) > 0) {
          $this->resultapi('1', 'Plans Found', $plan_all);
        } else {
          $this->resultapi('0', 'No Plans Found', $plan_all);
        }
      }

      /**
       * Used to get active plan details
       */
      public function getPlanDetails() {
          $planDetails = PaymentPlan::getStripeDetails();

          if (count($planDetails) > 0) {
            $this->resultapi('1', 'Plan Found', $planDetails);
        } else {
            $this->resultapi('0', 'No Plan Found', $planDetails);
        }
      }

    /**
     * Create checkout session
     */
    public  function createCheckoutSession(Request $request, Response $response, array $args) {
        $body = json_decode($request->getBody());
        $stripeSecret = \Stripe\Stripe::setApiKey("sk_test_jADDmKxvpdyxUDaf2fceOhKR00FyXAmSM0");

        $checkout_session = \Stripe\Checkout\Session::create([
            'success_url' => '//localhost:8080/#/login?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => '//localhost:8080/#/login',
            'payment_method_types' => ['card'],
            'subscription_data' => ['items' => [[
                'plan' => $body->planId,
            ]]]
        ]);

        return $response->withJson(array('sessionId' => $checkout_session['id']));
}



    /**
     * Stripe session plan should not be hard coded
     */
    public function store(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_jADDmKxvpdyxUDaf2fceOhKR00FyXAmSM0');

        $session = \Stripe\Checkout\Session::create([
            'billing_address_collection' => 'required',
            'payment_method_types' => ['card'],
            'subscription_data' => [
              'items' => [[
                'plan' => 'plan_F4C0MUbwIPbC8j',
              ]],
            ],
            'success_url' => '//localhost:8000/#/',
            'cancel_url' => '//localhost:8000/#/login',
        ]);
        $stripeSession = array($session);
        $sessId = ($stripeSession[0]['id']);
    }

    public function charge(Request $request)
    {
        dd($request->all());
    }


    /**
     * Redirect user to the Payment Gatewate
     * 
     * @return Response
     */
    // public funciton payStripe(Request, $request)
    // {

    // }


        /**
     * For saving/displaying results
     */
    function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);
        exit;
    }
}
