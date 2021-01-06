@extends('layouts.admin')
@section('content')

<div class="panel-body">
    {!! Form::open(array('url'=>'/admin/country','class'=>'form-horizontal','method'=>'POST','id'=>'grade')) !!}

    <div class="form-group">
        {!! Form::label('name', 'Country Name'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div>
    
    <div class="form-group">
        {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/country')}}">Cancel</a> 
    </div>
    {!! Form::close() !!}
</div>

<script type="text/javascript">
    
    $("#grade").validate({
        ignore: [],
        highlight: function (element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).parent('div').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        errorElement: 'div',
        rules: {
            name: {required: true}
     },
        messages: {
            name: {required: "Please enter country name."}
        },
        errorPlacement: function (error, element) {         
                error.insertAfter(element);           
        },
        success: function (element) {
            $(element).parent('.form-group').removeClass('has-error');
        },
    });
</script>

@endsection
