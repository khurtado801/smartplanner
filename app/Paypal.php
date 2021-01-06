<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paypal extends Model {

    protected $table = 'user_subscribe';

    public function create_records($data) {
        
        $from = $data['payment_create_date'];
        $to = date('Y-m-d', strtotime(date('Y-m-d', time()) . " + 365 days"));        
        $paypal = new Paypal;
        $paypal->user_id = $data['user_id'];
        $paypal->plan_id = $data['plan_id'];
        $paypal->subscribe_date = $from;
        $paypal->expire_date = $to;
        $paypal->transaction_id = $data['transaction_id'];
        $paypal->transaction_status = $data['transaction_status'];
        $paypal->payment_create_date = $data['payment_create_date'];
        $paypal->amount = $data['amount'];
        $paypal->currency = $data['currency'];
        //$paypal->status = $data['transaction_state'];
        $paypal->status = "Active";
        //echo '<pre>'; print_r($paypal); die();
        $result = $paypal->save($data);
        return $paypal;
    }

}
