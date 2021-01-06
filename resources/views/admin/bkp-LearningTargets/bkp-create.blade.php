@extends('layouts.admin')
@section('content')

<div class="panel-body">

    {!! Form::open(array('url'=>'/admin/learningtargets','files'=>true,'class'=>'form-horizontal','method'=>'POST','id'=>'learningtargets')) !!}

    <div class="form-group">
        {!! Form::label('name', 'Learning Targets'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('keyconcepts_id', 'Key Concepts'); !!}
        {!! Form::select('keyconcepts_id[]', $keyconcepts, null, ['multiple' => 'multiple','id' => 'keyconcepts_id','class' => 'form-control']) !!}
    </div>  

    <div class="form-group">
        {!! Form::label('overview_summary', 'Overview/Summary'); !!}
        {!! Form::textarea('overview_summary',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div>  

    <div class="form-group">
        {!! Form::label('standards', 'Standards'); !!}
        {!! Form::textarea('standards',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('essential_questions', 'Essential Questions'); !!}
        {!! Form::textarea('essential_questions',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('core_ideas', 'Core Ideas'); !!}
        {!! Form::textarea('core_ideas',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('academic_vocabulary', 'Academic Vocabulary'); !!}
        {!! Form::textarea('academic_vocabulary',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
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
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $('#keyconcepts_id').SumoSelect({
            selectAll: true,
            placeholder: '--- Select Key Concepts ---'
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
            "keyconcepts_id[]": {
                required: true,
                minlength: 1
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
            "keyconcepts_id[]": {required: "Please select key concepts."},
            overview_summary: {required: "Please enter overview summary."},
            standards: {required: "Please enter standards."},
            essential_questions: {required: "Please enter essential questions."},
            core_ideas: {required: "Please enter core ideas."},
            academic_vocabulary: {required: "Please enter academic vocabulary."},
        },
        errorPlacement: function (error, element) {
            if (element.attr('id') == 'overview_summary') {
                error.insertAfter($('#cke_'+element.attr("id")+''));
            } else if (element.attr('id') == 'standards') {
                error.insertAfter($('#cke_'+element.attr("id")+''));
            } else if (element.attr('id') == 'essential_questions') {
                error.insertAfter($('#cke_'+element.attr("id")+''));
            } else if (element.attr('id') == 'core_ideas') {
                error.insertAfter($('#cke_'+element.attr("id")+''));
            } else if (element.attr('id') == 'academic_vocabulary') {
                error.insertAfter($('#cke_'+element.attr("id")+''));
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
