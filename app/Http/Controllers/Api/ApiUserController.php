<?php

namespace App\Http\Controllers\Api;

use App\Api\User;
use App\EmailTemplates;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Hash;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;
use Aws\Ses\SesClient;

require 'vendor/autoload.php';



class ApiUserController extends Controller
{
    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth)
    {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        //$this->middleware('auth');
    }

    public function users()
    {
        $users = DB::table('users')->select('id', 'firstname', 'lastname', 'email')->paginate(5);
        return $users;
    }

    public function getForgotPassword(Request $request)
    {

        if ($request->email && $request->email != "") {
            $validator = Validator::make($request->all(), [
                'email' => 'required|max:100|email',
            ]);

            if ($validator->fails()) {
                $this->resultapi('0', $validator->errors()->all(), null);
            } else {
                $email = $request->email;
                $verifyCode = str_random(8);

                $user = User::where("email", $email)->where('usertype', 'Teacher')->first();

                if (count($user) > 0) {
                    $websiteLink = config('constant.base_url') . "passwordReset/id/" . $user['id'] . "/vcode/" . $verifyCode;
                    $search = array("[FIRSTNAME]", "[WEBSITELINK]", "[VCODE]");
                    $replace = array($user['firstname'], $websiteLink, $verifyCode);

                    $params = array(
                        'subject' => 'Smart Planner Password Reset',
                        'from' => "smartplanner@devken.me",
                        'to' => $user['email'],
                        'template' => 'forgot-password',
                        'search' => $search,
                        'replace' => $replace
                    );
                    //$user->verify_forgot_password = $verifyCode;
                    //$user->save();
                    //print_r($websiteLink);
                    // exit;
                    $result = $this->SendEmail($params);

                    if ($result == true) {
                        $user->verify_forgot_password = $verifyCode;
                        $user->save();

                        $this->resultapi('1', 'Check your email to reset your password', $user['id']);
                    } else {
                        $this->resultapi('0', 'Something went wrong with server,Please try again');
                        return false;
                    }
                } else {
                    $this->resultapi('0', 'Email Not Registered.', null);
                }
            }
        } else { }
    }

    public function getPasswordReset(Request $request)
    {

        if ($request->user_id && $request->all() && count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [

                'user_id' => 'required|numeric',
                'password_verify_code' => 'required|max:255|min:6|max:10',
                'password' => 'required|max:255|min:6',
                'confirm_password' => 'required|max:255|min:6|same:password',
            ]);

            if ($validator->fails()) {
                $this->resultapi('0', $validator->errors()->all(), null);
            } else {
                $userId = $request->user_id;
                $user = User::find($userId);

                if (trim($request->password_verify_code) === trim($user->verify_forgot_password)) {
                    $user->password = bcrypt($request->password);
                    $user->verify_forgot_password = "";
                    $user->save();

                    $this->resultapi('1', 'Password Updated Successfully, Please Login.', null);
                } else {
                    $this->resultapi('0', 'Wrong password verification code.', null);
                }
            }
        } else {
            $this->resultapi('0', 'Operation not not possible', null);
        }
    }

    public function SendEmail($params) {

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'mail.devken.me',
            'smtp_port' => 465,
            'smtp_user' => "smartplanner@devken.me", //SITE_EMAIL_ID_SHK change it to yours
            'smtp_pass' => "O(&fW%i;tX*q", // SITE_PASSWORD_SHK change it to yours 
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE
        );

        $email_template = EmailTemplates::where('slug', '=', $params["template"])->first();

        $message = str_replace($params["search"], $params["replace"], $email_template["email_content"]);
        //print_r($message);exit;

        $user = User::where('email', '=', $params['to'])->first();

        /* $sent = Mail::raw($message, function ($m) use ($params) {
          $m->from($params['from'], 'Smartplanner');
          $m->to($params['to'])->subject($params['subject']);
          });
         */
        $mail_data = array('content' => $message, 'toEmail' => $params["to"], 'subject' => $email_template["subject"], 'from' => "smartplanner@devken.me");


        $sent = Mail::send('emails.mail-template', $mail_data, function($message) use ($mail_data) {
                    $message->from($mail_data['from'], 'Smartplanner');
                    $message->to($mail_data['toEmail']);
                    $message->subject($mail_data['subject']);
                });
    }

    /**
     * Get a CSRF-TOKEN.
     *
     * @return Response
     */
    public function getFormToken()
    {
        $formToken['_token'] = csrf_token();
        echo json_encode($formToken);
    }

    /**
     * Get a user by the token from the header.
     *
     * @return Response
     */
    public function getByToken()
    {
        try {
            header('Content-Type: application/json');
            $userdata = array($this->jwtAuth->parseToken()->authenticate());
            $this->resultapi('1', 'Login Successfully', $userdata);
            //return $this->jwtAuth->parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return [
                'error' => true,
                'code' => 11,
                'message' => 'Token Expired'
            ];
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return [
                'error' => true,
                'code' => 12,
                'message' => 'Invalid Token'
            ];
        } catch (JWTException $e) {
            return [
                'error' => true,
                'code' => 13,
                'message' => ''
                //Token absent
            ];
        }
    }

    public function verification($number, $id)
    {
        if ($id != "" && $number != "") {

            $is_verified = User::where('id', $id)->where('email_verified', 'Yes')->first();
            if (count($is_verified) > 0) {
                // update verification status  
                $this->resultapi('0', "your email is already verified, please Login");
            } else {
                $users = User::where('id', $id)->first();
                if (count($users) > 0) {
                    $array_updated = array('email_verified' => 'Yes', 'email_verify_code' => '');
                    DB::table('users')->where('id', $id)->where('email_verify_code', $number)->update($array_updated);
                    $this->resultapi('1', 'Your email has been successfully verified');
                } else {
                    $this->resultapi('0', "Your email doesn't match");
                }
            }
        } else {
            $this->resultapi('0', "your email doesn't verified, please try again");
        }
    }

    public function logout(Request $request)
    {
        $user_id = $request->user_id;
        Auth::logout();
        $this->resultapi('1', 'Success');
    }

    public function register(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'country' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
            'usertype' => 'required',
        ]);

        if ($validator->fails()) {
            $this->resultapi('0', 'Registration is Failed.', $validator->errors()->all());
        }

        $user = User::where('email', '=', $request->email)->first();

        if ($user === null) {
            $create = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'country' => $request->country,
                'phone_number' => $request->phone_number ? $request->phone_number : "",
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'usertype' => $request->usertype,
            ]);

            $user = User::find($create->id);

            $rand = str_random(8);
            DB::table('users')->where('id', $user['id'])->update(['email_verify_code' => $rand]);

            // NOTE: Before deployment set config equal to 'constant.base_url'
            $VerificationLink = config('constant.base_url') . "verification/" . $rand . '/' . $user['id'];
            $search = array("[FIRSTNAME]", "[WEBSITELINK]");
            $replace = array($user['firstname'], $VerificationLink);

            $params = array(
                'subject' => 'Smartplanner account confirmation',
                'from' => "smartplanner@devken.me", // SITE_EMAIL_ID
                'to' => $user['email'],
                'template' => 'new-register',
                'search' => $search,
                'replace' => $replace
            );

            // TODO setup emails to be sent out
            $result = $this->SendEmail($params);
            $result = true;
            if ($result == true) {
                DB::commit();
                $this->resultapi('1', 'Please check your email and verify account.', $user);
            } else {
                DB::rollBack();
                $this->resultapi('0', 'Something went wrong with server,Please try again.');
            }
        } else {
            $this->resultapi('0', 'Email already exist.');
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            $this->resultapi('0', 'Fail', $validator->errors()->all());
        }

        if (Auth::attempt(array('email' => $request->email, 'password' => $request->password, 'usertype' => 'Teacher'))) {
            $user = Auth::user();

            $user['tokenId'] = $this->jwtAuth->fromUser($user);
            $userdata = array($user);
            //print_r($userdata);

            if ($user->status == "Inactive") {
                $this->resultapi('0', 'Your account is inactive, please contact administrator.');
            } else if ($user->email_verified == "No") {
                $this->resultapi('0', 'Your email is not verified yet, please check your inbox.');
            }
            //pr($user);
            $this->resultapi('1', 'Login Successfully', $userdata);
        } else {
            $this->resultapi('0', 'Username or password is not valid. Please try again!');
        }
    }

    public function forgotPassword(Request $request)
    {

        $user = User::where('email', '=', $request->email)
            ->where('password', '!=', '')->first();

        if ($user == null) {
            $this->resultapi('0', 'We can\'t find a user with that e-mail address.');
        }

        $this->validate($request, ['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                $this->resultapi('1', 'Success', trans($response));

            case Password::INVALID_USER:
                $this->resultapi('0', 'Fail', trans($response));
        }
    }

    public function profile()
    {
        $users = User::find();
        return $users;
    }

    public function getSubscribedUserData(Request $request)
    {
        $user_id = $request->user_id;

        if ($user_id != "" && $user_id != "0") {
            //$from = date('Y-m-d', time());
            //$to = date('Y-m-d', strtotime(date('Y-m-d', time()) . " + 365 days"));

            $query = DB::table('user_subscribe as us');
            $query->select('*');
            $query->where('us.status', "Active");
            $query->where('us.user_id', $user_id);
            $attributes = array('us.transaction_status' => 'Success', 'us.transaction_status' => 'SuccessWithWarning');
            $query->where(function ($query) {
                $query->where('us.transaction_status', 'Success')
                    ->orWhere('us.transaction_status', 'SuccessWithWarning');
            });
            $result = $query->first();
            //echo "<pre>"; print_r($is_subscribed); exit;
            if (count($result) > 0) {
                $this->resultapi('1', 'You are subscribed.');
            } else {
                $users = DB::table('user_subscribe')->where('user_id', $user_id)->count();
                if ($users == 0) {
                    $this->resultapi('0', "You are not subscribed!");
                } else {
                    $this->resultapi('0', "Your subscription is suspended, cancelled or inactive");
                }
            }
        } else {
            $this->resultapi('0', "You are unauthorized.");
        }
    }

    public function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required'
        ]);
        if ($validator->fails()) {
            $this->resultapi('0', 'Fail', $validator->errors()->all());
        }

        $user_id = $request->user_id;

        if ($user_id != "") {

            $current_password = $request->current_password;
            $new_password = $request->new_password;
            $user = User::where("id", $user_id)->first();

            if (count($user) > 0) {
                if (Hash::check($current_password, $user->password) == true) {
                    $user->password = Hash::make($new_password);
                    $user->save();

                    $this->resultapi('1', 'Password has been changed successfully.');
                } else {
                    $this->resultapi('0', "Current password is wrong. Please try again.");
                }
            } else {
                $this->resultapi('0', "User not found.");
            }
        } else {
            $this->resultapi('0', "User not found.");
        }
    }

    public function myProfile(Request $request)
    {
        //print_r($request->all()); exit;
        $loggedin_user_id = $request->user_id;
        //print_r($loggedin_user_id); exit;
        if ($loggedin_user_id != "") {
            $user_profile = User::find($loggedin_user_id);
            //print_r($user_profile); exit;
            //return $user_profile;
            if (count($user_profile) > 0) {
                $this->resultapi('1', 'Requested user found.', $user_profile);
            } else {
                $this->resultapi('0', "Requested user not found.");
            }
        } else {
            $this->resultapi('0', "Requested user not found.");
        }
    }

    public function updateProfile(Request $request)
    {
        //print_r($request->all()); exit;
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'country' => 'required',
            //'phone_number' => 'required',
            'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            $this->resultapi('0', 'Fail', $validator->errors()->all());
        }

        $user_id = $request->user_id;

        if ($user_id != "") {
            $user = User::where("id", $user_id)->first();
            if (count($user) > 0) {
                $user->firstname = $request->firstname;
                $user->lastname = $request->lastname;
                $user->email = $request->email;
                $user->country = $request->country;
                $user->phone_number = $request->phone_number ? $request->phone_number : "";;
                $user->save();

                $this->resultapi('1', 'User profile has been updated successfully.');
            } else {
                $this->resultapi('0', "Requested user not found.");
            }
        } else {
            $this->resultapi('0', "Requested user not found.");
        }
    }

    public function getMyAllPayments(Request $request)
    {

        $user_id = $request->user_id;

        if ($user_id != "") {

            $my_payments = DB::table('user_subscribe as us')
                ->select('us.*', 'pp.name', 'pp.price', 'pp.duration')
                ->join('payment_plan as pp', 'us.plan_id', '=', 'pp.id')
                ->where('user_id', $user_id)
                //->where('transaction_status', "Suspended")
                //->whereIn('transaction_status', ['Suspended','Active'])
                ->get();

            if (count($my_payments) > 0) {
                $this->resultapi('1', 'You are subscribed for these plans.', array('my_payments' => $my_payments));
            } else {
                $this->resultapi('0', "You are not subscribed for any plan.");
            }
        } else {
            $this->resultapi('0', "You are unauthorized.");
        }
    }

    public function resultapi($status, $message, $result = array())
    {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['RESULT'] = $result;
        echo json_encode($finalArray);
        die;
    }
}
