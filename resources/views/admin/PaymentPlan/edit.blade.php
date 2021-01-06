@extends('layouts.admin')
@section('content')
<div class="panel-body">
    {!! Form::model($paymentplan,array('route' => array('admin.paymentplan.update', $paymentplan->id),'class'=>'form-horizontal','method'=>'PUT','id'=>'paymentplan')) !!}
    
    <div class="form-group">
        {!! Form::label('name', 'Plan Name'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>
    
    <div class="form-group" id="div_duration">
        {!! Form::label('duration', 'Duration (in months)'); !!}
        {!! Form::text('duration', null, array('type' => 'number', 'class'=>'form-control', 'id'=>'duration')) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('price', 'Price'); !!}
        {!! Form::text('price',null,array('class'=>'form-control', 'id'=>'price')) !!}
    </div>

    <div class="form-group">        
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Update',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/paymentplan')}}">Cancel</a> 
    </div>
    {!! Form::close() !!}
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#duration, #price").ForceNumericOnly();
        
        $("#paymentplan").validate({
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
                        name: {required: true},
                        frequency: {required: true},
                        price: {required: true}
                    },
            messages: {
                name: {required: "Please enter payment plan name."},
                frequency: {required: "Please select frequency."},
                price: {required: "Please enter price."}
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            success: function (element) {
                $(element).parent('.form-group').removeClass('has-error');
            },
        });
        
    });
</script>
@endsection
