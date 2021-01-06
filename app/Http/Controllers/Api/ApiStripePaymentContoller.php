<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Next;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use Dotenv;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Plan;
use Stripe\Subscription;
use Stripe\Card;
use App\PaymentPlan;
use App\ActivityLog;
use App\UserSubscribe;

require 'vendor/autoload.php';

$ENV_PATH = '../../../';
$dotenv = Dotenv\Dotenv::create(realpath($ENV_PATH));
$dotenv -> load();

require './config.php';

class ApiStripePaymentController extends Controller {

    /**
     * Used to get all plans
     */
    // public function getAllPlans() {

    //   $plan_all  = PaymentPlan::getAllStripePlans();

    //   echo $plan_all;

    //   if (count($plan_all) > 0) {
    //     $this->resultapi('1', 'Plans Found', $plan_all);
    //   } else {
    //     $this->resultapi('0', 'No Plans Found', $plan_all);
    //   }
    // }

    public function addApiKey(Request $request, Response $response, Next $next) {
        Stripe::setApiKey(getenv('STRIPE_SERCRET_KEY'));
        return $next($request, $response);
    }

    public function setup(Request $request, Response $response, array $args) {
        $pub_key = getenv('STRIPE_PUBLIC_KEY');
        return $response -> withJson(['publicKey' => $pub_key, 'subscriptionPlan' => getenv('planId')]);
    }

    public function createCheckoutSession(Request $request, Response $response, array $args) {
        $checkout_session = \Stripe\Checkout\Session::create([
           'success_url' => '//localhost:8080/#/login?session_id={CHECKOUT_SESSION_ID}',
           'cancel_url' => '//localhost:8080/#/',
           'payment_method_types' => ['card'],
           'subscription_data' => ['items' => [[
               'plan' =>'planId',
               'quantity' => '1'
           ]]],
           'billingAddressCollection' => 'required'
        ]);
    }


    public function getPlanDetails() {
        $planDetails = PaymentPlan::getStripeDetails();

        if (count($planDetails) > 0) {
          $this->resultapi('1', 'Plan Found', $planDetails);
      } else {
          $this->resultapi('0', 'No Plan Found', $planDetails);
      }
    }


    public function getDetails() {
      $details = PaymentPlan::getDetails();
      echo $details;

      // Check if any active plans found
      if (count($details) > 0) {
        $this->resultapi('1', 'Active Plan Found', $details);
      } else {
        $this->resultapi('0', 'No Active Plans Found', $details);
      }
    }

    /**
     * success response method
     * 
     * @return \Illuminate\Http\Response
     */


    
    public function stripeCharge(Request $request) {
        $token = $_POST['stripeToken'];

        \Stripe\Stripe::setApiKey('sk_test_jADDmKxvpdyxUDaf2fceOhKR00FyXAmSM0');

        // Create s customer
        $customer = \Stripe\Customer::create(array(
          'email' => $_POST['stripeEmail'],
          'source' => $_POST['stripeToken'],
        ));

        // Create subscription
        $subscription = \Stripe\Subscription::create([
          'customer' => $customer->id,
          'items' => [[
            'plan' => 'plan_123',
          ]],
        ]);

        // Charge the customer instead of the card
        $charge = \Stripe\Charge::create(array(
            "amount" => 5000,
            "currency" => "USD",
            "customer" => $customer->id
        ));

        $session = \Stripe\Checkout\Session::create([
            'billing_address_collection' => 'required',
            'payment_method_types' => ['card'],
            'subscription_data' => [
              'items' => [[
                'plan' => 'plan_F4C0MUbwIPbC8j',
              ]],
            ],
            'success_url' => '//localhost:8000/#/?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => '//localhost:8000/#/login',

        ]);
    }

    public function stripeAll(Request $request) {
        dd($request->all());
    }

    // public function store(Request $request) {
    //   \Stripe\Stripe::setApiKey('sk_live_07MfNeure0UHSzuv5bWcHNUF00X3vZAcDJ');


    //   $token = $_POST['stripeToken'];

    //   $session = \Stripe\Checkout\Session::create([
    //     'payment_method_types' => ['card'],
    //     'subscription_data' => [
    //     'items' => [[
    //       'plan' => 'plan_123',
    //       ]],
    //     ],
    //     'success_url' => 'https://example.com/success',
    //     'cancel_url' => 'https://example.com/cancel',
    //     ]);
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

?>