@extends('layouts.admin')
@section('content')

<div class="panel-body">

    {!! Form::open(array('url'=>'/admin/subjects','class'=>'form-horizontal','method'=>'POST','id'=>'subject')) !!}

    <div class="form-group">
        {!! Form::label('name', 'Name'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('grade_id', 'Grade'); !!}
        {!! Form::select('grade_id[]', $grades, null, ['multiple' => 'multiple','id' => 'grade_id','class' => 'form-control']) !!}
    </div>    

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/subjects')}}">Cancel</a> 
    </div>
    {!! Form::close() !!}
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#grade_id').SumoSelect({
            selectAll: true,
            placeholder: '--- Select Grade ---'
        });
    });

    $("#subject").validate({
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
                    "grade_id[]": {
                        required: true,
                        minlength: 1
                    },
                },
        messages: {
            name: {required: "Please enter subject name."},
            "grade_id[]": {required: "Please select grade."},
        },
        errorPlacement: function (error, element) {
            if (element.attr('multiple') === "multiple") {
                error.insertAfter(element.parent().find('.optWrapper'));
            } else {
                error.insertAfter(element);
            }
        },
        success: function (element) {
            $(element).parent('.form-group').removeClass('has-error');
        },
    });

</script>
@endsection
