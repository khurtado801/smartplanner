@extends('layouts.admin')
@section('content')

<div class="panel-body">

    {!! Form::model($learningtargetsname,array('route' => array('admin.learningtargetsname.update', $learningtargetsname->id),'files'=>true,'class'=>'form-horizontal','method'=>'PUT','id'=>'learningtargetsname')) !!}
    <div class="form-group">
        {!! Form::label('name', 'Learning Targets'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>
    
    <div class="form-group">        
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div> 

    <div class="form-group">
        {!! Form::submit('Update',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/learningtargets')}}">Cancel</a> 
    </div>

    {!! Form::close() !!}
</div>

<style>
    .cke_contents{height: 100px!important;}
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $("#learningtargets").validate({
            ignore: [],
            highlight: function (element) {
                $(element).parent('div').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).parent('div').removeClass('has-error');
            },
            errorElement: 'div',
            errorClass: 'help-block help-block-error',
            //errorElement: 'span',
            rules: {
                name: {required: true},
               
            },
            messages: {
                name: {required: "Please enter learning target name."},
                
            },
            errorPlacement: function (error, element) {
            },
            success: function (element) {
                $(element).parent('.form-group').removeClass('has-error');
            },
        });
        
    });
</script>
@endsection