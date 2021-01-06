@extends('layouts.admin')
@section('content')
<div class="panel-body">
    {!! Form::model($lesson,array('route' => array('admin.lessons.update', $lesson->id),'class'=>'form-horizontal','method'=>'PUT','id'=>'lesson')) !!}

    <div class="form-group">
        {!! Form::label('unit_title', 'Unit Title'); !!}
        {!! Form::text('unit_title',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('grade_id', 'Grade'); !!}
        {!! Form::select('grade_id', ['' => '--- Select Grade ---']+$grades, $lesson->grade_id, ['id' => 'grade_id','class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('subject_id', 'Subject'); !!}
        {!! Form::select('subject_id',['' => '--- Select Subject ---']+$subjects,$lesson->subject_id, ['id' => 'subject_id','class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('theme_id', 'Theme'); !!}
        {!! Form::select('theme_id', ['' => '--- Select Theme ---']+$themes,$lesson->theme_id, ['id' => 'theme_id','class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Draft' => 'Draft', 'Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div> 

    <div class="form-group">
        {!! Form::submit('Update',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/lessons')}}">Cancel</a>  
    </div>

    {!! Form::close() !!}
</div>
<script type="text/javascript">
    $(document).ready(function () {        

        $('#grade_id').on('change', function (e) {
            //console.log(e); 

            $('#subject_id').empty();
            $('#subject_id').append("<option value=''>--- Select Subject ---</option>");
            var grade_id = e.target.value;
            $.get('{{ url('information') }}/create/ajax-grade-subject?grade_id=' + grade_id, function (data) {

                $.each(data, function (index, subjObj) {
                    $('#subject_id').append("<option value='" + subjObj.id + "'>" + subjObj.name + "</option>");
                });

            });
        });

        $('#subject_id').on('change', function (e) {
            //console.log(e); 

            $('#theme_id').empty();
            $('#theme_id').append("<option value=''>--- Select Theme ---</option>");
            
            var grade_id = $('#grade_id').val();
            var subject_id = e.target.value;
            $.get('{{ url('information') }}/create/ajax-grade-subject-theme?grade_id=' + grade_id +'&& subject_id='+ subject_id, function (data) {

                $.each(data, function (index, themeObj) {
                    $('#theme_id').append("<option value='" + themeObj.id + "'>" + themeObj.name + "</option>");
                });

            });
        });
    });
    
    $("#lesson").validate({
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
            unit_title: {required: true},
            "grade_id": { 
                    required: true, 
                    minlength: 1 
            },
            "subject_id": { 
                    required: true, 
                    minlength: 1 
            },
            "theme_id": { 
                    required: true, 
                    minlength: 1 
            }
     },
        messages: {
            unit_title: {required: "Please enter unit title."},
            "grade_id": {required: "Please select grade."},
            "subject_id": {required: "Please select subject."},
            "theme_id": {required: "Please select theme."}
        },
        errorPlacement: function (error, element) {
         if (element.attr("grade_id[]")) {
             error.insertAfter(".SumoSelect > .optWrapper");
                //error.insertAfter(element);
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
