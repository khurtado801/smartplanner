@extends('layouts.admin')
@section('content')
<div class="panel-body">

    {!! Form::open(array('url'=>'/admin/user', 'class'=>'form-horizontal','method'=>'POST','id'=>'user')) !!}        

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
            {!! Form::hidden('email_verified',1,array('class'=>'form-control')) !!}
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-6">
            {!! Form::label('password', 'Password'); !!}
            {!! Form::password('password',array('class'=>'form-control')) !!}
        </div>
        <div class="col-md-6">
            {!! Form::label('Confirm Password', 'Confirm Password'); !!}
            {!! Form::password('confirmpassword',array('class'=>'form-control')) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            {!! Form::label('country', 'Country'); !!}
            {!! Form::select('country', ['' => '--- Select Country ---']+$countries,null, ['class' => 'form-control']) !!}
        </div>
        <div class="col-md-6">            
            {!! Form::label('phone_number', 'Phone Number'); !!}
            {!! Form::text('phone_number',null,array('class'=>'form-control')) !!}
        </div>       
    </div>

    <div class="form-group">
        <div class="col-md-6">
            {!! Form::label('usertype', 'User Type'); !!}
            {!! Form::select('usertype',  ['' => '--- Select User Type ---']+$usertype, null, ['class' => 'form-control']) !!}
        </div>

        <div class="col-md-6">
            {!! Form::label('status', 'User Status'); !!}
            {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
        </div>
    </div>
    <!--    <div class="form-group">
            <div class="col-md-12">
                {!! Form::checkbox('terms_conditions',1,array('class'=>'form-control')) !!}
                {!! Form::label('terms_conditions', 'Accept general business terms and conditions'); !!}            
            </div>
        </div>        -->

    <div class="form-group">
        <div class="col-md-12">
            {!! Form::hidden('user_type',2,array('class'=>'form-control')) !!}
            {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
            <a class="btn btn-default" href="{{ url('/admin/user')}}">Cancel</a>
        </div>
    </div>           
    {!! Form::close() !!}
</div>

@endsection
