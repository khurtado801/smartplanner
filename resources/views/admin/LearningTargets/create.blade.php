@extends('layouts.admin')
@section('content')

<div class="panel-body">

    {!! Form::open(array('url'=>'/admin/learningtargets','files'=>true,'class'=>'form-horizontal','method'=>'POST','id'=>'learningtargets')) !!}

<!--    <div class="form-group">
        {!! Form::label('name', 'Learning Targets'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>-->
    <div class="form-group">
        {!! Form::label('learningtargetsName_id', 'Learning Targets'); !!}
        {!! Form::select('learningtargetsName_id', array(''=>'--- Select Learning Targets ---')+$learningtargetsName, null, ['id' => 'learningtargetsName_id','class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('grade_id', 'Grade'); !!}
        {!! Form::select('grade_id', array(''=>'--- Select Grade ---')+$grades, null, ['id' => 'grade_id','class' => 'form-control']) !!}
    </div>

    <div class="form-group subject_form">
        {!! Form::label('subject_id', 'Subjects'); !!}
        {!! Form::select('subject_id', array(''=>'--- Select Subjects ---'), null, ['id' => 'subject_id','class' => 'form-control','disabled' => '']) !!}
    </div>
    
    <div class="form-group theme_form">
        {!! Form::label('Theme', 'Theme'); !!}
        {!! Form::select('theme_id', array(''=>'--- Select Theme ---'), null, ['id' => 'theme_id','class' => 'form-control','disabled' => '']) !!}
    </div>
    
    <div class="form-group keyconcepts_form">
        {!! Form::label('keyconcepts_id', 'Key Concepts'); !!}
        {!! Form::select('keyconcepts_id', array(''=>'--- Key Concepts ---'), null, ['id' => 'keyconcepts_id','class' => 'form-control','disabled' => '']) !!}
    </div>  

    <div class="form-group">
        {!! Form::label('overview_summary', 'Overview/Summary'); !!}
        {!! Form::textarea('overview_summary',null,array('class'=>'form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div>  

    <div class="form-group">
        {!! Form::label('standards', 'Standards'); !!}
        {!! Form::textarea('standards',null,array('class'=>'form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('essential_questions', 'Essential Questions'); !!}
        {!! Form::textarea('essential_questions',null,array('class'=>'form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('core_ideas', 'Core Ideas'); !!}
        {!! Form::textarea('core_ideas',null,array('class'=>'form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('academic_vocabulary', 'Academic Vocabulary'); !!}
        {!! Form::textarea('academic_vocabulary',null,array('class'=>'form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div>  

    <div class="form-group">
        {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/learningtargets')}}">Cancel</a> 
    </div>

    {!! Form::close() !!}
</div>

<style>
    .cke_contents{height: 100px!important;}
    .SumoSelect.disabled .placeholder {
        cursor: not-allowed;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
//        $('#keyconcepts_id').SumoSelect({
//            selectAll: true,
//            placeholder: '--- Select Key Concepts ---'
//        });
//        $('#learningtargetsName_id').SumoSelect({
//            selectAll: true,
//            placeholder: '--- Select LT ---'
//        });

        // Get subjects by grade id
        $('#grade_id').change(function () {
            $('#subject_id').prop('disabled', 'disabled');
            $('#theme_id').prop('disabled', 'disabled');
            $('#keyconcepts_id').prop('disabled', 'disabled');
//            $('.keyconcepts_form .SumoSelect').remove();
//            $('.keyconcepts_form').append('<select name="keyconcepts_id" class="form-control" id="keyconcepts_id" disabled=""></select>');
//            $('#keyconcepts_id').SumoSelect({
//                selectAll: true,
//                placeholder: '--- Select Key Concepts ---'
//            });
            var grade_id = $("#grade_id option:selected").val();
            var getSubjectsByGrade = '<?php echo url('/admin/learningtargets/getSubjectsByGrade') ?>';
            
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            $.ajax({
                url: getSubjectsByGrade,
                type: "post",
                data: {grade_id: grade_id},
                success: function (data)
                {
                    var dataObj = jQuery.parseJSON(data);
                    $('#theme_id').html('<option selected="selected" value="">--- Select Theme ---</option>');
                    $('#subject_id').html('<option selected="selected" value="">--- Select Subjects ---</option>');
                    if(dataObj.STATUS == '1'){
                        
                        $.each(dataObj.DATA, function (key, value) {
                            $('#subject_id').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                        $('#subject_id').prop('disabled', false);
                    }
                }
            });
        });
        
        // Get subjects by grade id
        $('#subject_id').change(function () {
            $('#theme_id').prop('disabled', 'disabled');
            $('#keyconcepts_id').prop('disabled', 'disabled');
//            $('.keyconcepts_form .SumoSelect').remove();
//            $('.keyconcepts_form').append('<select name="keyconcepts_id" class="form-control" id="keyconcepts_id" disabled=""></select>');
//            $('#keyconcepts_id').SumoSelect({
//                selectAll: true,
//                placeholder: '--- Select Key Concepts ---'
//            });
            var grade_id = $("#grade_id option:selected").val();
            var subject_id = $("#subject_id option:selected").val();
            var getThemesByGradeAndSubjects = '<?php echo url('/admin/learningtargets/getThemesByGradeAndSubjects') ?>';
            
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            $.ajax({
                url: getThemesByGradeAndSubjects,
                type: "post",
                data: {grade_id: grade_id,subject_id:subject_id},
                success: function (data)
                {
                    var dataObj = jQuery.parseJSON(data);
                    $('#theme_id').html('<option selected="selected" value="">--- Select Theme ---</option>');
                    if(dataObj.STATUS == '1'){
                        $.each(dataObj.DATA, function (key, value) {
                            $('#theme_id').append('<option value="'+value.theme_id+'">'+value.name+'</option>');
                        });
                        $('#theme_id').prop('disabled', false);
                    }else{
                        $('#theme_id').prop('disabled', false);
                    }
                }
            });
        });
        
        // Get subjects by grade id
        $('#theme_id').change(function () {
            var theme_id = $("#theme_id option:selected").val();

            var getKeyconceptByTheme = '<?php echo url('/admin/learningtargets/getKeyconceptByTheme') ?>';
            
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            $.ajax({
                url: getKeyconceptByTheme,
                type: "post",
                data: {theme_id: theme_id},
                success: function (data)
                {
                    var dataObj = jQuery.parseJSON(data);
//                    $('#keyconcepts_id').prop('disabled', 'disabled');
//                    $('.keyconcepts_form .SumoSelect').remove();
//                    $('.keyconcepts_form').append('<select name="keyconcepts_id[]" class="form-control" id="keyconcepts_id" multiple="multiple"></select>');
                    if(dataObj.STATUS == '1'){
                        $.each(dataObj.DATA, function (key, value) {
                            $('#keyconcepts_id').append('<option value="'+value.kc_id+'">'+value.keyconcept_name+'</option>');
                        });
                        $('#keyconcepts_id').prop('disabled', false);
                    }
//                    $('#keyconcepts_id').SumoSelect({
//                        selectAll: true,
//                        placeholder: '--- Select Key Concepts ---'
//                    });
                }
            });
        });
        
        
                
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
                "keyconcepts_id": {
                    required: true,
                },
                "grade_id": {
                    required: true,
                },
                "theme_id": {
                    required: true,
                },
                "subject_id": {
                    required: true,
                },
                overview_summary: {required: function () {
                        CKEDITOR.instances.overview_summary.updateElement();
                    }
                },
                standards: {required: function () {
                        CKEDITOR.instances.standards.updateElement();
                    }
                },
                essential_questions: {required: function () {
                        CKEDITOR.instances.essential_questions.updateElement();
                    }
                },
                core_ideas: {required: function () {
                        CKEDITOR.instances.core_ideas.updateElement();
                    }
                },
                academic_vocabulary: {required: function () {
                        CKEDITOR.instances.academic_vocabulary.updateElement();
                    }
                },
            },
            messages: {
                name: {required: "Please enter learning target name."},
                "keyconcepts_id": {required: "Please select key concepts."},
                "grade_id": {required: "Please select grade."},
                "subject_id": {required: "Please select subject."},
                "theme_id": {required: "Please select theme."},
                overview_summary: {required: "Please enter overview summary."},
                standards: {required: "Please enter standards."},
                essential_questions: {required: "Please enter essential questions."},
                core_ideas: {required: "Please enter core ideas."},
                academic_vocabulary: {required: "Please enter academic vocabulary."},
            },
            errorPlacement: function (error, element) {
                if (element.attr('id') == 'overview_summary') {
                    error.insertAfter($('#cke_' + element.attr("id") + ''));
                } else if (element.attr('id') == 'standards') {
                    error.insertAfter($('#cke_' + element.attr("id") + ''));
                } else if (element.attr('id') == 'essential_questions') {
                    error.insertAfter($('#cke_' + element.attr("id") + ''));
                } else if (element.attr('id') == 'core_ideas') {
                    error.insertAfter($('#cke_' + element.attr("id") + ''));
                } else if (element.attr('id') == 'academic_vocabulary') {
                    error.insertAfter($('#cke_' + element.attr("id") + ''));
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