<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Paypal;
use PayPal\Api\Agreement;
use PayPal\Api\CreditCard;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
// use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Plan;
use Stripe\Subscription;
use Stripe\Card;


use DB;
use App\User;
use App\UserSubscribe;
// This sample code demonstrate how you can create a billing plan, as documented here at:
// https://developer.paypal.com/webapps/developer/docs/api/#create-a-plan
// API used: /v1/payments/billing-plans
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
//update plan
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;
// # Suspend an agreement
use PayPal\Api\AgreementStateDescriptor;
//bootstrap
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;


use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\ActivationDetailsType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\BillingPeriodDetailsType;
use PayPal\EBLBaseComponents\CreateRecurringPaymentsProfileRequestDetailsType;
use PayPal\EBLBaseComponents\CreditCardDetailsType;
use PayPal\EBLBaseComponents\RecurringPaymentsProfileDetailsType;
use PayPal\EBLBaseComponents\ScheduleDetailsType;
use PayPal\PayPalAPI\CreateRecurringPaymentsProfileReq;
use PayPal\PayPalAPI\CreateRecurringPaymentsProfileRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\EBLBaseComponents\ReferenceCreditCardDetailsType;

use PayPal\EBLBaseComponents\PayerInfoType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\PersonNameType;
use PayPal\EBLBaseComponents\CreditCardNumberTypeType;

use PayPal\EBLBaseComponents\DoDirectPaymentRequestDetailsType;
use PayPal\PayPalAPI\DoDirectPaymentReq;
use PayPal\PayPalAPI\DoDirectPaymentRequestType;

use PayPal\EBLBaseComponents\ManageRecurringPaymentsProfileStatusRequestDetailsType;
use PayPal\PayPalAPI\ManageRecurringPaymentsProfileStatusReq;
use PayPal\PayPalAPI\ManageRecurringPaymentsProfileStatusRequestType;


use App\EmailTemplates;
use mikehaertl\wkhtmlto\Pdf;

class Configuration
{
    // For a full list of configuration parameters refer in wiki page (https://github.com/paypal/sdk-core-php/wiki/Configuring-the-SDK)
    public static function getConfig()
    {
        $config = array(
                // values: 'sandbox' for testing
                //         'live' for production
                "mode" => "live",
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'FINE'
    
                // These values are defaulted in SDK. If you want to override default values, uncomment it and add your value.
                // "http.ConnectionTimeOut" => "5000",
                // "http.Retry" => "2",
        );
        return $config;
    }
    
    // Creates a configuration array containing credentials and other required configuration parameters.
    public static function getAcctAndConfig()
    {
        $config = array(
                // Signature Credential
                /*"acct1.UserName" => "amitstest-1_api1.techuz.com",
                "acct1.Password" => "GQ4EPS6GZXQW9KG4",
                "acct1.Signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31ACzEtGBUslzV-APZFTib8Fwd.JcR",*/
                "acct1.UserName" => "michelle_api2.evolvededucator.com",
                "acct1.Password" => "AQJHF5TT26HE6TBH",
                "acct1.Signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31AoFIlUB4vrY-4UO78.t8HbcrFdfW"
                // Subject is optional and is required only in case of third party authorization
                // "acct1.Subject" => "",
                
                // Sample Certificate Credential
                // "acct1.UserName" => "certuser_biz_api1.paypal.com",
                // "acct1.Password" => "D6JNKKULHN3G5B8A",
                // Certificate path relative to config folder or absolute path in file system
                // "acct1.CertPath" => "cert_key.pem",
                // Subject is optional and is required only in case of third party authorization
                // "acct1.Subject" => "",
        
                );
        
        return array_merge($config, self::getConfig());
    }

}


class PaymentPlan extends Model {

    protected $table = 'payment_plan';

    public function payplan() {
        return $this->belongsToMany('App\UserSubscribe', 'UserSubscribe');
    }

    public static function createBillingAgreementWithCreditCard($data, $plan_data, $user_id) {

//        echo "<pre>";
//            print_r($plan_data); //exit;

        $user_details = new User();
        $user_details = User::user_details($user_id);

        $tomorrow = date("Y-m-d", strtotime("+ 1 day"));
        $start_date = $tomorrow . 'T' . date('00:00:00') . 'Z';


        $agreement = new Agreement();

        $agreement->setName($plan_data->name)
                ->setDescription('Payment with credit Card')
                //->setStartDate("2020-12-31T09:13:49Z");
                ->setStartDate($start_date);
        //print_r($agreement); exit;
        // Add Plan ID
        // Please note that the plan Id should be only set in this case.        
        $plan = new Plan();

        //$plan->setId($createdPlan->getId());
        $plan->setId($plan_data->plan_key);
        $agreement->setPlan($plan);

        // Add Payer
        $payer = new Payer();
        $payer->setPaymentMethod('credit_card')
                ->setPayerInfo(new PayerInfo(array('email' => $user_details->email)));

        // Add Credit Card to Funding Instruments
        $card = new CreditCard();

        $card_holder_name = (string) $data['card_holder_name'];
        $card_number = (string) $data['card_number'];
        $cvv = (string) $data['cvv'];
        $expiry_month = (string) $data['expiry_month'];
        $expiry_year = (string) $data['expiry_year'];
        $card_type = self::getCreditCardType($card_number);

        $card->setType($card_type)
                ->setFirstName($card_holder_name)
                ->setLastName($card_holder_name)
                ->setNumber($card_number)
                ->setExpireMonth($expiry_month)
                ->setExpireYear($expiry_year)
                ->setCvv2($cvv)
                ->setBillingAddress(array("line1" => "065769 Holcomb Bridge Road #141",
                    "line2" => "5713 E Dimond Boulevard #B9",
                    "city" => "Wichita",
                    "state" => "KS",
                    "postal_code" => "67202",
                    "country_code" => "US",
                    "phone" => "+1 6202311066"));

        $fundingInstrument = new FundingInstrument();
        $fundingInstrument->setCreditCard($card);
        $payer->setFundingInstruments(array($fundingInstrument));
        //Add Payer to Agreement
        $agreement->setPayer($payer);

        // Add Shipping Address
//        $shippingAddress = new ShippingAddress();
//        $shippingAddress->setLine1('111 First Street')
//                ->setCity('Saratoga')
//                ->setState('CA')
//                ->setPostalCode('95071')
//                ->setCountryCode('US');
//        $agreement->setShippingAddress($shippingAddress);
        // For Sample Purposes Only.
        //$request = clone $agreement;
        // ### Create Agreement
        try {
            $clientId = 'ASkBw0dThM5D2JfUnQG0mn2b0Ylu3kQpKXfiNmV5huY8MqNKlBZcLlpXEtL5UBDxomYzlrOVgCgRjC15';
            $clientSecret = 'EBC4Vst0vMIt956D9E1bHg3DHnW83DpFU9VJyQTE6Dc2eaBOCq94FZ0nxrxWTMfk6tYNKOgEwUjAU71x';

            /** @var \Paypal\Rest\ApiContext $apiContext */
            $apiContext = self::getApiContext($clientId, $clientSecret);
            //$required_path = require base_path() . "\\vendor\paypal\\rest-api-sdk-php\sample\bootstrap.php";
            //print_r($agreement); exit;
            // Please note that as the agreement has not yet activated, we wont be receiving the ID just yet.
            $agreement = $agreement->create($apiContext);
        } catch (PayPal\Exception\PayPalConnectionException $e) {
            echo $e->getData();
            // This should give you a json object explaining what exactly is causing that 400 exception.
            exit(1);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit(1);
        }

        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        //ResultPrinter::printResult("Created Billing Agreement.", "Agreement", $agreement->getId(), $request, $agreement);
        //echo "<pre>"; //echo $agreement->getId(); 
        //print_r($agreement); exit;
        return $agreement;
    }

    public static function getCreditCardType($str, $format = 'string') {
        if (empty($str)) {
            return false;
        }

        $matchingPatterns = [
            'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'mastercard' => '/^5[1-5][0-9]{14}$/',
            'amex' => '/^3[47][0-9]{13}$/',
            'diners' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
            'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
            'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/',
            'any' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/'
        ];

        $ctr = 1;
        foreach ($matchingPatterns as $key => $pattern) {
            if (preg_match($pattern, $str)) {
                return $format == 'string' ? $key : $ctr;
            }
            $ctr++;
        }
    }

    public static function createPlan($plan_data) {
        //$required_path = include base_path() . "\\vendor\paypal\\rest-api-sdk-php\sample\bootstrap.php";
        //require __DIR__ . '/../bootstrap.php';
        //require $required_path;

        $clientId = 'ASkBw0dThM5D2JfUnQG0mn2b0Ylu3kQpKXfiNmV5huY8MqNKlBZcLlpXEtL5UBDxomYzlrOVgCgRjC15';
        $clientSecret = 'EBC4Vst0vMIt956D9E1bHg3DHnW83DpFU9VJyQTE6Dc2eaBOCq94FZ0nxrxWTMfk6tYNKOgEwUjAU71x';

        /** @var \Paypal\Rest\ApiContext $apiContext */
        $apiContext = PaymentPlan::getApiContext($clientId, $clientSecret);
        // # Create Plan Sample
// Create a new instance of Plan object
        $plan = new Plan();

// # Basic Information
// Fill up the basic information that is required for the plan
        $plan->setName($plan_data->name)
                ->setDescription('Smartplanner recurring payment plan is creation.')
                ->setType('Infinite');

// # Payment definitions for this billing plan.
        $paymentDefinition = new PaymentDefinition();

// The possible values for such setters are mentioned in the setter method documentation.
// Just open the class file. e.g. lib/PayPal/Api/PaymentDefinition.php and look for setFrequency method.
// You should be able to see the acceptable values in the comments.
        $paymentDefinition->setName('Regular Payments')
                ->setType('REGULAR')
                ->setFrequency($plan_data->frequency)
                ->setFrequencyInterval($plan_data->duration)
                ->setCycles('0')
                ->setAmount(new Currency(array('value' => $plan_data->price, 'currency' => 'USD')));

// Charge Models
        $chargeModel = new ChargeModel();
        $chargeModel->setType('SHIPPING')
                ->setAmount(new Currency(array('value' => 0, 'currency' => 'USD')));

        $paymentDefinition->setChargeModels(array($chargeModel));

        $merchantPreferences = new MerchantPreferences();
        $baseUrl = self::getBaseUrl();
// ReturnURL and CancelURL are not required and used when creating billing agreement with payment_method as "credit_card".
// However, it is generally a good idea to set these values, in case you plan to create billing agreements which accepts "paypal" as payment_method.
// This will keep your plan compatible with both the possible scenarios on how it is being used in agreement.
        $merchantPreferences->setReturnUrl("$baseUrl/ExecuteAgreement.php?success=true")
                ->setCancelUrl("$baseUrl/ExecuteAgreement.php?success=false")
                ->setAutoBillAmount("yes")
                ->setInitialFailAmountAction("CONTINUE")
                ->setMaxFailAttempts("0")
                ->setSetupFee(new Currency(array('value' => 0, 'currency' => 'USD')));


        $plan->setPaymentDefinitions(array($paymentDefinition));
        $plan->setMerchantPreferences($merchantPreferences);

// For Sample Purposes Only.
        //$request = clone $plan;
// ### Create Plan
        try {
            $output = $plan->create($apiContext);
        } catch (Exception $ex) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            //ResultPrinter::printError("Created Plan", "Plan", null, $request, $ex);
            exit(1);
        }

// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        //ResultPrinter::printResult("Created Plan", "Plan", $output->getId(), $request, $output);
        //echo "<pre>"; print_r($output); exit;
        return $output;
    }

    public static function updatePlan($plan_key) {
        //$required_path = require base_path() . "\\vendor\paypal\\rest-api-sdk-php\sample\bootstrap.php";

        $clientId = 'ASkBw0dThM5D2JfUnQG0mn2b0Ylu3kQpKXfiNmV5huY8MqNKlBZcLlpXEtL5UBDxomYzlrOVgCgRjC15';
        $clientSecret = 'EBC4Vst0vMIt956D9E1bHg3DHnW83DpFU9VJyQTE6Dc2eaBOCq94FZ0nxrxWTMfk6tYNKOgEwUjAU71x';

        /** @var \Paypal\Rest\ApiContext $apiContext */
        $apiContext = PaymentPlan::getApiContext($clientId, $clientSecret);
        $createdPlan = Plan::get($plan_key, $apiContext);

        try {
            $patch = new Patch();

            $value = new PayPalModel('{
	       "state":"ACTIVE"
	     }');

            $patch->setOp('replace')
                    ->setPath('/')
                    ->setValue($value);

            $patchRequest = new PatchRequest();
            $patchRequest->addPatch($patch);

            $createdPlan->update($patchRequest, $apiContext);
            $plan = Plan::get($plan_key, $apiContext);
        } catch (Exception $ex) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            //ResultPrinter::printError("Updated the Plan Payment Definition", "Plan", null, $patchRequest, $ex);
            exit(1);
        }
// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        //ResultPrinter::printResult("Updated the Plan Payment Definition", "Plan", $plan->getId(), $patchRequest, $plan);
        return $plan;
    }

    public static function UpdateBillingAgreement() {
        $patch = new Patch();
        $patch->setOp('replace')
                ->setPath('/')
                ->setValue(json_decode('{
            "description": "New Description"
        }'));

        $patchRequest = new PatchRequest();
        $patchRequest->addPatch($patch);
        try {
            $createdAgreement->update($patchRequest, $apiContext);
            $agreement = Agreement::get($createdAgreement->getId(), $apiContext);
        } catch (Exception $ex) {
            exit(1);
        }
        return $agreement;
    }

    /**
     * ### getBaseUrl function
     * // utility function that returns base url for
     * // determining return/cancel urls
     *
     * @return string
     */
    public static function getBaseUrl() {
        if (PHP_SAPI == 'cli') {
            $trace = debug_backtrace();
            $relativePath = substr(dirname($trace[0]['file']), strlen(dirname(dirname(__FILE__))));
            echo "Warning: This sample may require a server to handle return URL. Cannot execute in command line. Defaulting URL to http://localhost$relativePath \n";
            return "http://localhost" . $relativePath;
        }
        $protocol = 'http';
//        if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')) {
//            $protocol .= 's';
//        }
        $host = $_SERVER['HTTP_HOST'];
        $request = $_SERVER['PHP_SELF'];
        return dirname($protocol . '://' . $host . $request);
    }

    public static function checkSubscribe($user_id, $plan_key) {
        $check_subscribe = DB::table('user_subscribe as us')
                ->join('payment_plan as pp', 'pp.plan_key', '=', 'us.plan_id')
                ->select('us.id', 'us.plan_id')
                ->where('us.id', $user_id)
                ->where('us.plan_id', $plan_key)
                ->where('us.transaction_status', 'approved')
                ->where('us.status', 'Active')
                ->get();
        //print_r($standard_list);
        return $check_subscribe;
    }

    public static function getPlanDetails($plan_id) {
        $plan_details = DB::table('payment_plan as pp')
                ->select('pp.*')->where('pp.id', $plan_id)
                ->first();
        return $plan_details;
    }

    /**
     * Get all Stripe plans, even inactive plans
     */
    public static function getAllStripePans() {
        try {
            // Set Stripe API key
            $stripeSecret = \Stripe\Stripe::setApiKey("sk_test_jADDmKxvpdyxUDaf2fceOhKR00FyXAmSM0");

            // Active plans
            $planDetails = \Stripe\Plan::all();
        } catch (Exception $ex) {

        }

        // Display active plan details
        return $planDetails;
    }

    /**
     * Get all active Stripe Plans
     */
    public static function getStripeDetails() {
        try {
            // Set Stripe API key
            $stripeSecret = \Stripe\Stripe::setApiKey("sk_test_jADDmKxvpdyxUDaf2fceOhKR00FyXAmSM0");

            // Active plans
            $planDetails = \Stripe\Plan::all(["active" => true], ["limit" => 3]);
        } catch (Exception $ex) {

        }

        // Display active plan details
        return $planDetails;
    }

    public static function getBillingAgreement($agreement_id) {
        try {
            $clientId = 'ASkBw0dThM5D2JfUnQG0mn2b0Ylu3kQpKXfiNmV5huY8MqNKlBZcLlpXEtL5UBDxomYzlrOVgCgRjC15';
            $clientSecret = 'EBC4Vst0vMIt956D9E1bHg3DHnW83DpFU9VJyQTE6Dc2eaBOCq94FZ0nxrxWTMfk6tYNKOgEwUjAU71x';

            /** @var \Paypal\Rest\ApiContext $apiContext */
            $apiContext = PaymentPlan::getApiContext($clientId, $clientSecret);
            $agreement_detail = Agreement::get($agreement_id, $apiContext);
            //$success_array['transaction_status'] = $agreement_detail->state;
        } catch (Exception $ex) {
            
        }
        return $agreement_detail;
    }


    public static function SuspendBillingAgreement($transaction_id) {
        $agreementStateDescriptor = new AgreementStateDescriptor();
        $agreementStateDescriptor->setNote("Suspending the agreement");
        try {
            $clientId = 'ASkBw0dThM5D2JfUnQG0mn2b0Ylu3kQpKXfiNmV5huY8MqNKlBZcLlpXEtL5UBDxomYzlrOVgCgRjC15';
            $clientSecret = 'EBC4Vst0vMIt956D9E1bHg3DHnW83DpFU9VJyQTE6Dc2eaBOCq94FZ0nxrxWTMfk6tYNKOgEwUjAU71x';

            /** @var \Paypal\Rest\ApiContext $apiContext */
            $apiContext = PaymentPlan::getApiContext($clientId, $clientSecret);
            $createdAgreement = Agreement::get($transaction_id, $apiContext);
            $createdAgreement->suspend($agreementStateDescriptor, $apiContext);

            // Lets get the updated Agreement Object
            $agreement = Agreement::get($createdAgreement->getId(), $apiContext);
        } catch (Exception $ex) {
            
        }

        return $agreement;
    }

    public static function ReactivateBillingAgreement($transaction_id) {
        //Create an Agreement State Descriptor, explaining the reason to suspend.
        $agreementStateDescriptor = new AgreementStateDescriptor();
        $agreementStateDescriptor->setNote("Reactivating the agreement");

        try {
            $clientId = 'ASkBw0dThM5D2JfUnQG0mn2b0Ylu3kQpKXfiNmV5huY8MqNKlBZcLlpXEtL5UBDxomYzlrOVgCgRjC15';
            $clientSecret = 'EBC4Vst0vMIt956D9E1bHg3DHnW83DpFU9VJyQTE6Dc2eaBOCq94FZ0nxrxWTMfk6tYNKOgEwUjAU71x';

            /** @var \Paypal\Rest\ApiContext $apiContext */
            $apiContext = PaymentPlan::getApiContext($clientId, $clientSecret);
            $suspendedAgreement = Agreement::get($transaction_id, $apiContext);
            $suspendedAgreement->reActivate($agreementStateDescriptor, $apiContext);

            // Lets get the updated Agreement Object
            $agreement = Agreement::get($suspendedAgreement->getId(), $apiContext);
        } catch (Exception $ex) {
            
        }
        return $agreement;
    }

    public static function changeRecurringStatus($transaction_id, $agreement_status) {
        if ($agreement_status == "Active") {
            $change_status = "Active";
        } else {
            $change_status = "Inactive";
        }
        $final_rec_status = DB::table('user_subscribe as us')
                ->where('us.transaction_id', $transaction_id)
                ->update(['us.transaction_status' => $agreement_status, 'us.status' => $change_status]);
        return $final_rec_status;
    }

    /**
     * Helper method for getting an APIContext for all calls
     * @param string $clientId Client ID
     * @param string $clientSecret Client Secret
     * @return PayPal\Rest\ApiContext
     */
    public static function getApiContext($clientId, $clientSecret) {

        // #### SDK configuration
        // Register the sdk_config.ini file in current directory
        // as the configuration source.
        /*
          if(!defined("PP_CONFIG_PATH")) {
          define("PP_CONFIG_PATH", __DIR__);
          }
         */


        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com

        $apiContext = new ApiContext(
                new OAuthTokenCredential(
                $clientId, $clientSecret
                )
        );

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration

        $apiContext->setConfig(
                array(
                    'mode' => 'sandbox',
                    'log.LogEnabled' => true,
                    'log.FileName' => '../PayPal.log',
                    'log.LogLevel' => 'DEBUG', // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                    'validation.level' => 'log',
                    'cache.enabled' => true,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                )
        );

        // Partner Attribution Id
        // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
        // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
        // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');

        return $apiContext;
    }

    public static function getTransactionDetail($agreement_id) {
        $agreementId = $agreement_id;

        $params = array('start_date' => date('Y-m-d', strtotime('-15 years')), 'end_date' => date('Y-m-d', strtotime('+5 days')));

        try {
            $clientId = 'ASkBw0dThM5D2JfUnQG0mn2b0Ylu3kQpKXfiNmV5huY8MqNKlBZcLlpXEtL5UBDxomYzlrOVgCgRjC15';
            $clientSecret = 'EBC4Vst0vMIt956D9E1bHg3DHnW83DpFU9VJyQTE6Dc2eaBOCq94FZ0nxrxWTMfk6tYNKOgEwUjAU71x';

            /** @var \Paypal\Rest\ApiContext $apiContext */
            $apiContext = PaymentPlan::getApiContext($clientId, $clientSecret);

            $result = Agreement::searchTransactions($agreementId, $params, $apiContext);
            //echo "<pre>"; print_r($result); exit;
        } catch (Exception $ex) {
            
        }
        return $result;
    }

    public static function DoDirectWithCreditCard($data){
        require_once(base_path() . '/vendor/paypal/merchant-sdk-php/samples/PPBootStrap.php');
        
        $card_holder_name = (string) $data['card_holder_name'];
        $card_number = (string) $data['card_number'];
        $cvv = (string) $data['cvv'];
        $expiry_month = (string) $data['expiry_month'];
        $expiry_year = (string) $data['expiry_year'];

        $arr_customer_name = explode(" ",$card_holder_name);
        if($arr_customer_name[0]!=""){
            $firstName = $arr_customer_name[0];
        }
        if(isset($arr_customer_name[1])){
            $lastName = $arr_customer_name[1];
        }else{
            $lastName = $firstName;
        }
        $plan_id = $data['plan_id'];
        $plan = PaymentPlan::getPlanDetails($plan_id);
        

        /*
         * shipping adress
        */
        $address = new AddressType();
        $address->Name = "$firstName $lastName";
        $address->Street1 = "B-12";
        $address->Street2 = "N1Y";
        $address->CityName = "Surat";
        $address->StateOrProvince = "NY";
        $address->PostalCode = "2336";
        $address->Country = "US";
        $address->Phone = "12555";

        $paymentDetails = new PaymentDetailsType();
        $paymentDetails->ShipToAddress = $address;
        $paymentDetails->OrderTotal = new BasicAmountType('USD', $plan->price);

        $personName = new PersonNameType();
        $personName->FirstName = $firstName;
        $personName->LastName = $lastName;

        //information about the payer
        $payer = new PayerInfoType();
        $payer->PayerName = $personName;
        $payer->Address = $address;
        $payer->PayerCountry = "US";

        $card_type = self::getCreditCardType($card_number);

        $cardDetails = new CreditCardDetailsType();
        $cardDetails->CreditCardNumber = trim($card_number);
        $cardDetails->CreditCardType = (string)ucfirst(trim($card_type));
        $cardDetails->ExpMonth = $expiry_month;
        $cardDetails->ExpYear = $expiry_year;
        $cardDetails->CVV2 = $cvv;
        $cardDetails->CardOwner = $payer;

        $ddReqDetails = new DoDirectPaymentRequestDetailsType();
        $ddReqDetails->CreditCard = $cardDetails;
        $ddReqDetails->PaymentDetails = $paymentDetails;

        $doDirectPaymentReq = new DoDirectPaymentReq();
        $doDirectPaymentReq->DoDirectPaymentRequest = new DoDirectPaymentRequestType($ddReqDetails);

        
        $paypalService = new PayPalAPIInterfaceServiceService(Configuration::getAcctAndConfig());
        
        try {
            /* wrap API method calls on the service object with a try catch */
            $doDirectPaymentResponse = $paypalService->DoDirectPayment($doDirectPaymentReq);

            /*echo "<pre>";
            print_r($doDirectPaymentResponse);*/
            
            // if transation is success than save details to database

            if($doDirectPaymentResponse->Ack=="Success" || $doDirectPaymentResponse->Ack=="SuccessWithWarning"){
                $user_subscribe_id = PaymentPlan::SavePaymentdetail($doDirectPaymentResponse,$data);
                
                PaymentPlan::CreateRecurringPaymentsProfile($data,$plan,$user_subscribe_id);
                PaymentPlan::SendInvoice($data,$user_subscribe_id);

                $error_json = array('STATUS'=>1,'MESSAGE'=>"Plan has been subscribed successfully.");
                echo json_encode($error_json);
                exit;
            }else{
                $error_json = array('STATUS'=>0,'MESSAGE'=>$doDirectPaymentResponse->Errors[0]->LongMessage);
                echo json_encode($error_json);
                exit;
            }

        }catch (PayPal\Exception\PayPalConnectionException $ex) {
            $ex_message = $ex->getMessage();
            $error_json = array('STATUS'=>0,'MESSAGE'=>$ex_message);
            echo json_encode($error_json);
            exit;

        }catch (Exception $ex) {
            $ex_message = $ex->getMessage();
            $error_json = array('STATUS'=>0,'MESSAGE'=>$ex_message);
            echo json_encode($error_json);
            exit;
        }
    }

    public static function CreateRecurringPaymentsProfile($data,$plan,$user_subscribe_id){
        require_once(base_path() . '/vendor/paypal/merchant-sdk-php/samples/PPBootStrap.php');

        $card_holder_name = (string) $data['card_holder_name'];
        $card_number = (string) $data['card_number'];
        $cvv = (string) $data['cvv'];
        $expiry_month = (string) $data['expiry_month'];
        $expiry_year = (string) $data['expiry_year'];

        $arr_customer_name = explode(" ",$card_holder_name);
        if($arr_customer_name[0]!=""){
            $firstName = $arr_customer_name[0];
        }
        if(isset($arr_customer_name[1])){
            $lastName = $arr_customer_name[1];
        }else{
            $lastName = $firstName;
        }

        //$today = date('Y-m-d H:i:s');
        $futureDate=date('Y-m-d', strtotime('+'.$plan->duration.' months'));
        
        $start_date = date(DATE_ATOM, strtotime($futureDate));
        $paypal = Paypal::find($user_subscribe_id);
        $paypal->expire_date = $futureDate;
        $paypal->save();
        
        $currencyCode = "USD";
        $shippingAddress = new AddressType();
        $shippingAddress->Name = "$firstName $lastName";
        $shippingAddress->Street1 = "B-12, sdsd";
        $shippingAddress->Street2 = "NY New sd";
        $shippingAddress->CityName = "Surat";
        $shippingAddress->StateOrProvince = "NY";
        $shippingAddress->PostalCode = "361012";
        $shippingAddress->Country = "US";
        $shippingAddress->Phone = "225526";



        $RPProfileDetails = new RecurringPaymentsProfileDetailsType();
        $RPProfileDetails->SubscriberName = "$firstName $lastName";
        $RPProfileDetails->BillingStartDate = $start_date;
        $RPProfileDetails->SubscriberShippingAddress  = $shippingAddress;
        //$RPProfileDetails->BillingAddress  = $shippingAddress;
        $activationDetails = new ActivationDetailsType();

        //print_r($plan); exit;
        $paymentBillingPeriod =  new BillingPeriodDetailsType();
        $paymentBillingPeriod->BillingFrequency = $plan->duration;
        $paymentBillingPeriod->BillingPeriod = "Month";
        $paymentBillingPeriod->TotalBillingCycles = "0";
        $paymentBillingPeriod->Amount = new BasicAmountType($currencyCode,$plan->price);
        $paymentBillingPeriod->ShippingAmount = new BasicAmountType($currencyCode, 0);
        $paymentBillingPeriod->TaxAmount = new BasicAmountType($currencyCode, 0);


        /*
         *   Describes the recurring payments schedule, including the regular
        payment period, whether there is a trial period, and the number of
        payments that can fail before a profile is suspended which takes
        mandatory params:

        * `Description` - Description of the recurring payment.
        `Note:
        You must ensure that this field matches the corresponding billing
        agreement description included in the SetExpressCheckout request.`
        * `Payment Period`
        */
        $scheduleDetails = new ScheduleDetailsType();
        $scheduleDetails->Description = "corresponding";
        $scheduleDetails->ActivationDetails = $activationDetails;

               

        $scheduleDetails->PaymentPeriod = $paymentBillingPeriod;
        $scheduleDetails->MaxFailedPayments =  0;
        $scheduleDetails->AutoBillOutstandingAmount = "NoAutoBill";        
       

        $createRPProfileRequestDetail = new CreateRecurringPaymentsProfileRequestDetailsType();

        // biling address
        $billingAddress = new AddressType();
        $billingAddress->Name = "$firstName $lastName";
        $billingAddress->Street1 = "B-12";
        $billingAddress->CityName = "surat";
        $billingAddress->StateOrProvince = "GJ";
        $billingAddress->Country = "US";
        $billingAddress->PostalCode = "2255";

        // credit card details
        

        $personName = new PersonNameType();
        $personName->FirstName = "$firstName";
        $personName->LastName = "$lastName";

        //information about the payer
        $payer = new PayerInfoType();
        $payer->PayerName = $personName;
        $payer->Address = $billingAddress;
        $payer->PayerCountry = "US";
        
        $creditCard = new CreditCardDetailsType();
        $creditCard->CreditCardNumber = trim($card_number);
        $card_type = self::getCreditCardType($card_number);
        $creditCard->CreditCardType = (string)ucfirst(trim($card_type));
        $creditCard->CVV2 = $cvv;
        $creditCard->ExpMonth = $expiry_month;
        $creditCard->ExpYear = $expiry_year;
        $creditCard->CardOwner = $payer;

        $createRPProfileRequestDetail->CreditCard = $creditCard;
        $createRPProfileRequestDetail->ScheduleDetails = $scheduleDetails;
        $createRPProfileRequestDetail->RecurringPaymentsProfileDetails = $RPProfileDetails;

        $createRPProfileRequest = new CreateRecurringPaymentsProfileRequestType();
        $createRPProfileRequest->CreateRecurringPaymentsProfileRequestDetails = $createRPProfileRequestDetail;

        $createRPProfileReq =  new CreateRecurringPaymentsProfileReq();
        $createRPProfileReq->CreateRecurringPaymentsProfileRequest = $createRPProfileRequest;
        
        $paypalService = new PayPalAPIInterfaceServiceService(Configuration::getAcctAndConfig());
        try {
            /* wrap API method calls on the service object with a try catch */
            $createRPProfileResponse = $paypalService->CreateRecurringPaymentsProfile($createRPProfileReq);

            if($createRPProfileResponse->Ack=="Success" || $createRPProfileResponse->Ack=="SuccessWithWarning"){
                PaymentPlan::SaveRecurringPaymentdetail($createRPProfileResponse, $user_subscribe_id);
            }else{
                $error_json = array('STATUS'=>0,'MESSAGE'=>$createRPProfileResponse->Errors[0]->LongMessage);
                echo json_encode($error_json);
                exit;
            }

        }catch (PayPal\Exception\PayPalConnectionException $ex) {
            $ex_message = $ex->getMessage();
            $error_json = array('STATUS'=>0,'MESSAGE'=>$ex_message);
            echo json_encode($error_json);
            exit;
        }catch (Exception $ex) {
            $ex_message = $ex->getMessage();
            $error_json = array('STATUS'=>0,'MESSAGE'=>$ex_message);
            echo json_encode($error_json);
            exit;
        }


        return $shippingAddress;


    }

    public static function SavePaymentdetail($response, $data){ 
        $paypal = new Paypal;
        $paypal->user_id = $data["user_id"];
        $paypal->plan_id = $data["plan_id"];
        $paypal->transaction_id = $response->TransactionID;
        $paypal->transaction_status = $response->Ack;
        $paypal->amount = $response->Amount->value;
        $paypal->currency = $response->Amount->currencyID;
        $paypal->status = 'Active';
        $result = $paypal->save();
        return $paypal->id;
    }

    public static function SaveRecurringPaymentdetail($response, $user_subscribe_id){ 
        $paypal = Paypal::find($user_subscribe_id);        
        $paypal->subscribe_date = date('Y-m-d');
        $paypal->recurring_profile = $response->CreateRecurringPaymentsProfileResponseDetails->ProfileID;
        $paypal->recurring_status = $response->CreateRecurringPaymentsProfileResponseDetails->ProfileStatus;
        $paypal->save();
    }

    public static function SendInvoice($data,$user_subscribe_id){ 
        $date = date('jS,F,Y');
        $user = User::where("id", $data["user_id"])->where('usertype', 'Teacher')->first();
        $plan = PaymentPlan::where("id", $data["plan_id"])->first();

        if (count($user) > 0) {
                $search = array("[FIRSTNAME]", "[PLAN_NAME]", "[PLAN_PRICE]", "[DATE]");
                $replace = array($user['firstname'], $plan['name'], $plan['price'], $date);

                $rand = rand(50000, 5225525);

                $pdf = new Pdf(array(
                   'binary' => '/usr/bin/xvfb-run -- /usr/local/bin/wkhtmltopdf',
                   'ignoreWarnings' => true,
                ));

                $pdf->addPage(config('constant.base_url_new') . 'pdf/invoice/' . $user_subscribe_id);

                if (!$pdf->saveAs('storage/invoice/' . $rand . '.pdf')) {
                   //echo $pdf->getError();
                }

                $pdffile = config('constant.base_url_new') . "storage/invoice/" . $rand . ".pdf";
                //$pdffile = "";

                $params = array(
                    'subject' => 'Smart Planner Invoice',
                    'from' => "hello@evolvededucator.com",
                    'to' => $user['email'],
                    'template' => 'invoice',
                    'search' => $search,
                    'replace' => $replace,
                    'file' => $pdffile
                );
                $email_obj = new EmailTemplates();
                $result = $email_obj->SendEmail($params);
                $result = true;
                if ($result == true) {
                    
                } else {
                    
                }
            }
    }

    public static function RecurringProfileStatus($data){
        require_once(base_path() . '/vendor/paypal/merchant-sdk-php/samples/PPBootStrap.php');

  
        $manageRPPStatusReqestDetails = new ManageRecurringPaymentsProfileStatusRequestDetailsType();

        $manageRPPStatusReqestDetails->Action =  $data['status'];
        $manageRPPStatusReqestDetails->ProfileID =  $data['transaction_id'];

        $manageRPPStatusReqest = new ManageRecurringPaymentsProfileStatusRequestType();
        $manageRPPStatusReqest->ManageRecurringPaymentsProfileStatusRequestDetails = $manageRPPStatusReqestDetails;


        $manageRPPStatusReq = new ManageRecurringPaymentsProfileStatusReq();
        $manageRPPStatusReq->ManageRecurringPaymentsProfileStatusRequest = $manageRPPStatusReqest;
        
        $paypalService = new PayPalAPIInterfaceServiceService(Configuration::getAcctAndConfig());

        try {
            /* wrap API method calls on the service object with a try catch */
            $manageRPPStatusResponse = $paypalService->ManageRecurringPaymentsProfileStatus($manageRPPStatusReq);

            if($manageRPPStatusResponse->Ack=="Success"){

                $paypal = Paypal::where('recurring_profile',$data['transaction_id'])->first();        
                
                $user_status = 'Inactive';
                if($data['status']=="Reactivate"){
                    $user_status = 'Active';
                }
                $paypal->recurring_status = $data['status'];
                $paypal->status = $user_status;
                $paypal->save();

                $error_json = array('STATUS'=>1,'MESSAGE'=>"Prifile status change successfully.");
                echo json_encode($error_json);
                exit;
            }else{
                $error_json = array('STATUS'=>0,'MESSAGE'=>$manageRPPStatusResponse->Errors[0]->LongMessage);
                echo json_encode($error_json);
                exit;
            }

        } catch (Exception $ex) {
           
        }        
    }

}
