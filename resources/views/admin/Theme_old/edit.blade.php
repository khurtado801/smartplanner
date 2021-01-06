@extends('layouts.admin')
@section('content')
<div class="panel-body">
    {!! Form::model($theme,array('route' => array('admin.themes.update', $theme->id),'class'=>'form-horizontal','method'=>'PUT','id'=>'theme')) !!}

    <div class="form-group">
        {!! Form::label('name', 'Name'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('grade_id', 'Grade'); !!} <?php //print_r($grades_selected); ?>
<!--        {!! Form::select('grade_id', ['' => ' -- Select Grade --']+$grades,$grades_selected, ['class' => 'form-control']) !!}-->
        {!! Form::select('grade_id[]', $grades,$grades_selected, ['multiple' => 'true','id' => 'grade_id','class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('subject_id', 'Subject'); !!} <?php //print_r($grades_selected); ?>
        {!! Form::select('subject_id[]',$subjects,$subjects_selected, ['multiple' => 'true','id' => 'subject_id','class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Update',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/themes')}}">Cancel</a> 
    </div>
    {!! Form::close() !!}
</div>
<script type="text/javascript">
    $(document).ready(function () {
        
        $('#grade_id').SumoSelect({
            selectAll: true,
            placeholder: '--- Select Grade ---'
        });
        
        $('#subject_id').SumoSelect({
            selectAll: true,
            placeholder: '--- Select Subject ---'
        });
        
        $("#theme").validate({
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
                        "subject_id[]": {
                            required: true,
                            minlength: 1
                        },
                    },
            messages: {
                name: {required: "Please enter theme name."},
                "grade_id[]": {required: "Please select grade."},
                "subject_id[]": {required: "Please select subject."},
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
    });
</script>
@endsection
