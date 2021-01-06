@extends('layouts.admin')
@section('content')
<div class="panel-body">
    {!! Form::model($user,array('route' => array('admin.user.update', $user->id),'class'=>'form-horizontal','method'=>'PUT','id'=>'user','files'=>true)) !!}

    <div class="form-group">
        <div class=" col-md-6">
            <h4>Personal Details</h4>
        </div>
    </div>

    <div class="form-group">
        <div class=" col-md-6">
            {!! Form::label('firstname', 'First Name'); !!}
            {!! Form::text('firstname',null,array('class'=>'form-control')) !!}
        </div>

        <div class="col-md-6">
            {!! Form::label('lastname', 'Last Name'); !!}
            {!! Form::text('lastname',null,array('class'=>'form-control')) !!}
        </div>           
    </div>

    <div class="form-group">
        <div class="col-md-12">
            {!! Form::label('email', 'Email'); !!}
            {!! Form::text('email',null,array('class'=>'form-control')) !!} 
        </div>
    </div>

    <div class="form-group">
        <div class=" col-md-6">
            {!! Form::label('country', 'Country'); !!}
            {!! Form::select('country', ['' => '--- Select Country ---']+$countries,$user->country, ['class' => 'form-control']) !!}
        </div>
        <div class=" col-md-6">
            {!! Form::label('phone_number', 'Phone Number'); !!}
            {!! Form::text('phone_number',null,array('class'=>'form-control')) !!}
        </div>
    </div>

    <?php $style = "style='display: none'"; ?>
    @if($user->usertype == "Super Admin" || $user->usertype == "Admin")
    @if($isAdmin == true )
    <?php $style = ""; ?>
    @endif
    @endif
    <!--    <div class="form-group">
            <div class="col-md-12">
                {!! Form::checkbox('update_password',1,array('id' => 'other')) !!}
                {!! Form::label('update_password', 'Change Password'); !!} 
    
            </div>
        </div>
    
        <div class="form-group">
            <div class="col-md-6">
                {!! Form::label('password', 'Password'); !!}
                {!! Form::text('password',null,array('class'=>'form-control')) !!}
            </div>
            <div class="col-md-6">
                {!! Form::label('confirmpassword', 'Confirm Password'); !!}
                {!! Form::text('confirmpassword',null,array('class'=>'form-control')) !!}
            </div>
        </div>           -->
    <div class="form-group profileImg" <?php echo $style; ?>>
        <div class="col-md-12">
            <?php
            //if(Auth::check())
            //{                        
            $profileImage = Auth::user()->profile_image;
            $defaultPath = URL::to('/storage/adminProfileImages/') . '/' . 'admin.jpg';
            if ($profileImage && $profileImage != "") {
                $imgPath = URL::to('/storage/adminProfileImages/') . '/' . $profileImage;
                if (file_exists('storage/adminProfileImages/'.$profileImage)) {
                    $imgPath = $imgPath;
                } else {
                    $imgPath = $defaultPath;
                }
            } else {
                $imgPath = $defaultPath;
            }
            //}
            ?>
            <img class="m-r-sm" alt="Admin" src="<?php echo $imgPath; ?>" width="165px" height="200px">
        </div>
    </div>

    <div class="form-group profileImg" <?php echo $style; ?>>
        <div class="input_fields_wrap2 col-md-12">
            <button class="add_field_button2 btn btn-primary">Change Profile Image</button>
        </div>                          
    </div>

    <div class="form-group">
        <div class="col-md-6">
            {!! Form::label('usertype', 'User Type'); !!}
            {!! Form::select('usertype',  ['' => '--- Select User Type ---']+$usertype, null, ['class' => 'form-control', 'id'=>'usertype']) !!}
        </div>

        <div class="col-md-6">
            {!! Form::label('status', 'User Status'); !!}
            {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-12">
            {!! Form::submit('Update',array('class'=>'btn btn-primary')); !!}
            <a class="btn btn-default" href="{{ url('/admin/user')}}">Cancel</a>
        </div>
    </div>

    {!! Form::close() !!}

    <script>
        $(document).ready(function () {
            $('#usertype').on('change', function () { //debugger;
                if ($('#usertype').val() == "Teacher") {
                    $('.profileImg').hide();
                } else {
                    $('.profileImg').show();
                }
            });
        });
    </script>
</div>
@endsection
