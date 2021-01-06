@extends('layouts.admin')
@section('content')

        <div class="panel-body">
                <div class="panel-body">

                    {!! Form::open(array('url'=>'admin/changepassword/update','class'=>'form-horizontal','method'=>'POST','id'=>'changePasswordForm')) !!}
                    
                    <div class="form-group">
                      {!! Form::label('old_password', 'Old Password',array('class'=>'col-sm-2 control-label')); !!}
                      <div class="col-sm-10">
                          {!! Form::password('old_password',array('class'=>'form-control')) !!}
                      </div>
                    </div>
                    <div class="line line-dashed line-lg pull-in"></div>
                    
                    <div class="form-group">
                      {!! Form::label('password', 'New Password',array('class'=>'col-sm-2 control-label')); !!}
                      <div class="col-sm-10">
                          {!! Form::password('password',array('class'=>'form-control')) !!}
                      </div>
                    </div>
                    <div class="line line-dashed line-lg pull-in"></div>
                    
                    <div class="form-group">
                      {!! Form::label('confirm_password', 'Confirm Password',array('class'=>'col-sm-2 control-label')); !!}
                      <div class="col-sm-10">
                          {!! Form::password('confirm_password',array('class'=>'form-control')) !!}
                      </div>
                    </div>
                    <div class="line line-dashed line-lg pull-in"></div>
                    
                    <div class="form-group">
                      <div class="col-sm-4 col-sm-offset-2">
                        {!! Form::submit('Update Password',array('class'=>'btn btn-primary')); !!}
                        <a href="{{ URL::to('admin/user')}}" class="btn btn-default">Cancel</a>
                      </div>
                    </div>
                     {!! Form::close() !!}
                </div>
        </div>

  <script>
    //on click change password form
    $("#changePasswordForm").validate({
        rules: {
            old_password:{
                required:true,
                minlength:6,
            },
            password:{
                required:true,
                minlength:6,
            },
            confirm_password:{
                required:true,
                minlength:6,
                equalTo: "#password",
            }
	},
	messages: {
            old_password: {
                required:"You must enter old password",
            },
            password: {
                required:"You must enter password",
            },
            confirm_password:{
                required:"You must enter confirm password",
            }
        }
        
      });

    
</script>

@endsection
