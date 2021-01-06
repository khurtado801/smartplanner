<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use App\User;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use Session;

class ChangePasswordController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        
        return view('admin/User/changepassword', ['title_for_layout' => 'Change Password']);
    }

    public function update(Request $request) {
        
        $validator = Validator::make($request->all(), [
        'old_password' => 'required',
        'password' => 'required|min:6',
        'confirm_password' => 'required|min:6|same:password'
        ]);

        if ($validator->fails()) {
            return redirect('/admin/changepassword')
                            ->withErrors($validator)
                            ->withInput();
        }
        
        if (!Auth::validate(array('email' => Auth::user()->email, 'password' => $request->old_password))) {            
            $validator->getMessageBag()->add('password', 'Password is incorrect. Please try again.');
            return redirect("/admin/changepassword")->withErrors($validator);
        }

        $user = Auth::user();
        //echo '<pre>'; print_r($request->password); exit;
        $user->fill([
            'password' => Hash::make($request->confirm_password)
        ])->save();
        
        Session::flash('flash_message', 'Password updated successfully.');
        return redirect('/admin/user');
    }

}
