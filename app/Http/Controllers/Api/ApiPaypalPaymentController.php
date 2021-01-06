<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Paypalpayment;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\Paypal;
use App\PaymentPlan;
use App\ActivityLog;
use App\UserSubscribe;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use App\Api\User;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\EmailTemplates;
use mikehaertl\wkhtmlto\Pdf;
use PayPal\Api\Agreement;
use App\TransactionHistory;

class ApiPaypalPaymentController extends Controller {

    /**
     * object to authenticate the call.
     * @param object $_apiContext
     */
    private $_apiContext;

    /**
     * Set the ClientId and the ClientSecret.
     * @param
     * string $_ClientId
     * string $_ClientSecret
     */
    private $_ClientId = 'ASkBw0dThM5D2JfUnQG0mn2b0Ylu3kQpKXfiNmV5huY8MqNKlBZcLlpXEtL5UBDxomYzlrOVgCgRjC15';
    private $_ClientSecret = 'EBC4Vst0vMIt956D9E1bHg3DHnW83DpFU9VJyQTE6Dc2eaBOCq94FZ0nxrxWTMfk6tYNKOgEwUjAU71x';

    /*
     *   These construct set the SDK configuration dynamiclly,
     *   If you want to pick your configuration from the sdk_config.ini file
     *   make sure to update you configuration there then grape the credentials using this code :
     *   $this->_cred= Paypalpayment::OAuthTokenCredential();
     */

    public function __construct() {

// ### Api Context
// Pass in a `ApiContext` object to authenticate
// the call. You can also send a unique request id
// (that ensures idempotency). The SDK generates
// a request id if you do not pass one explicitly.
//$this->_apiContext = Paypalpayment::ApiContext($this->_ClientId, $this->_ClientSecret);

        $this->_apiContext = Paypalpayment::apiContext(config('paypal_payment.Account.ClientId'), config('paypal_payment.Account.ClientSecret'));
    }

    /*
     * Display form to process payment using credit card
     */


    /*
     * Process payment using credit card
     */

    public function store(Request $request) {
        $data = $request->all();
        PaymentPlan::DoDirectWithCreditCard($data);
        exit;
    }

    /*
      Use this call to get a list of payments.
      url:payment/
     */

    public function index() {
//echo 'welcome here';die;
//        echo "<pre>";
//print_r($this->_apiContext);die;
        $payments = Paypalpayment::getAll(array('count' => 1, 'start_index' => 0), $this->_apiContext);



// echo '<pre>';
// print_r($payments);

        $success_array = array();

        $success_array['transaction_id'] = $payments->payments[0]->id;
        $success_array['transaction_status'] = $payments->payments[0]->state;
        $success_array['created_date'] = $payments->payments[0]->create_time;

// echo "#############";
// print_r($success_array);
// 		
// 		
// dd($payments);

        $Paypal = new Paypal;
        $result = $Paypal->create_records($success_array);


        $msg = 'Payment has been successfully.';
        $log = ActivityLog::createlog(Auth::Id(), "Payment", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/payment/create');
//die;
    }

    /*
      Use this call to get details about payments that have not completed,
      such as payments that are created and approved, or if a payment has failed.
      url:payment/PAY-3B7201824D767003LKHZSVOA
     */

    public function show($payment_id) {
        $payment = Paypalpayment::getById($payment_id, $this->_apiContext);

        dd($payment);
    }

    /*
     * Added By: Amit
     * Reason: Recheck payment status
     */

    public function getPaymentStatus(Request $request) {
        $user_id = $request->user_id;

        if ($user_id != "") {


            $subscribed = UserSubscribe::where('user_id', $user_id)->where('transaction_status', '!=', 'suspended')->first();
            if (count($subscribed) > 0) {
                $status = strtolower($subscribed->transaction_status);
                if ($status != "active" || $status != "cancelled" || $status != "suspended") {
                    if ($subscribed->transaction_id != "") {
                        //$payment_detail = Paypalpayment::getById($subscribed->transaction_id, $this->_apiContext);
                        $agreement_detail = PaymentPlan::GetBillingAgreement($subscribed->transaction_id);
                        //echo "<pre>"; print_r($agreement_detail); //exit;
                        $subscribed->transaction_status = strtolower($agreement_detail->state);
                        $subscribed->save();

                        if ($agreement_detail->id != "") {
                            $transaction_details = PaymentPlan::getTransactionDetail($agreement_detail->id);
                            //echo "<pre>"; print_r($transaction_details); //exit;
                            //$transaction_details = array_reverse($transaction_details->agreement_transaction_list);
                            $transaction_details = (array)($transaction_details->agreement_transaction_list);
                            $transaction_details = end($transaction_details);
                            //echo "<pre>"; print_r($transaction_details->transaction_id); exit;
                            if ($transaction_details) {
                                $transaction_history = TransactionHistory::where('transaction_id', $transaction_details->transaction_id)
                                                ->where('transaction_status', $transaction_details->status)->first();

                                if (count($transaction_history) == 0) {
                                    
                                    $transaction_history = new TransactionHistory;
                                    $transaction_history->agreement_id = $agreement_detail->id;
                                    $transaction_history->transaction_id = $transaction_details->transaction_id;
                                    $transaction_history->transaction_status = $transaction_details->status;
                                    //echo "<pre>"; print_r($transaction_history); exit;
                                    $transaction_history->save();
                                }

                                if ($transaction_history->transaction_status == 'Completed') {
                                    $this->resultapi('1', 'Status', strtolower($transaction_history->transaction_status));
                                } else {
                                    $this->resultapi('0', 'Payment is still processing.', '');
                                }
                            }

                            //$this->resultapi('1', 'Status', strtolower($agreement_detail->state));
                        }
                    }
                } else {
                    $this->resultapi('0', 'No need change', '');
                }
            }
        } else {
            $this->resultapi('0', 'You have not subscribed any plan.');
        }



//print_r($payment_detail);
//} else {
//}
    }

    /*
     * Added By: Karnik
     * Reason: Get All Plans
     */

    public function getAllPlans(Request $request) {

        $plan_all = PaymentPlan::where('status', 'Active')->get();
//print_r($plan_all); exit;
        if (count($plan_all) > 0) {
            $this->resultapi('1', 'Plans Found', $plan_all);
        } else {
            $this->resultapi('0', 'No Plans Found', $plan_all);
        }
    }

    public function getPlanDetails() {
//$payment_plan_id = $request->plan_id;
//$payment = PaymentPlan::getById($payment_plan_id);
        $planDetails = PaymentPlan::where('payment_plan.status', 'Active')
//->join('user_subscribe as us', 'us.plan_id', '=', 'payment_plan.id')
//->list('payment_plan.*','us.subscribe_date','us.expire_date')->all();
//->get();
                ->first();
        $planDetails->list = DB::table('payment_plan')
        ->where('payment_plan.status', 'Active')->get();
//print_r($planDetails); exit;
        if (count($planDetails) > 0) {
            $this->resultapi('1', 'Plan Found', $planDetails);
        } else {
            $this->resultapi('0', 'No Plan Found', $planDetails);
        }
    }

    public function getCreditCardType($str, $format = 'string') {
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

    public function download_invoice($id) {
//print_r($id); exit;

        $rand = rand(50000, 5225525);

// You can pass a filename, a HTML string, an URL or an options array to the constructor

        $pdf = new Pdf(array(
            'binary' => '/usr/bin/xvfb-run -- /usr/local/bin/wkhtmltopdf',
            'ignoreWarnings' => true,
        ));

        $pdf->addPage(config('constant.base_url_new') . 'pdf/invoice/' . $id);

        /* $pdf = new Pdf(config('constant.base_url_new') . 'pdf/invoice/' . $id);

          // On some systems you may have to set the path to the wkhtmltopdf executable
          $pdf->binary = '~/wkhtmltox/bin/wkhtmltopdf'; */

        if (!$pdf->saveAs('storage/pdf/' . $rand . '.pdf')) {
            echo $pdf->getError();
        }
        $pdf->send($rand . '.pdf');
        exit;
    }

    public function changeRecurringStatus(Request $request) {

        $data = $request->all();
        PaymentPlan::RecurringProfileStatus($data);     
        exit;
    }

    function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);
        exit;
    }
}

?>
