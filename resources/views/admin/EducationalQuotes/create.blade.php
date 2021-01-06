@extends('layouts.admin')
@section('content')

<div class="panel-body">
    {!! Form::open(array('url'=>'/admin/educationalquotes','class'=>'form-horizontal','method'=>'POST','id'=>'educationalquote')) !!}

    <div class="form-group">
        <div class="col-md-12">
            {!! Form::label('quote_line1', 'Quote Line 1'); !!}
            {!! Form::text('quote_line1',null,array('class'=>'form-control')) !!}
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-12">
            {!! Form::label('quote_line2', 'Quote Line 2'); !!}
            {!! Form::text('quote_line2',null,array('class'=>'form-control')) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            {!! Form::label('author', 'Philosopher'); !!}
            {!! Form::text('author',null,array('class'=>'form-control')) !!}
        </div>

        <div class="col-md-6">
            {!! Form::label('status', 'Status'); !!}
            {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
            <a class="btn btn-default" href="{{ url('/admin/educationalquotes')}}">Cancel</a> 
        </div>
    </div>
    {!! Form::close() !!}
</div>

<script type="text/javascript">

    $("#educationalquote").validate({
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
                    quote_line1: {required: true},
                    author: {required: true}
                },
        messages: {
            quote_line1: {required: "Please enter educational quote line1."},
            author: {required: "Please enter philosopher."}
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
