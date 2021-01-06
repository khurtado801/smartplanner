@extends('layouts.admin')
@section('content')

<div class="panel-body">

    {!! Form::open(array('url'=>'/admin/themes','class'=>'form-horizontal','method'=>'POST','id'=>'theme')) !!}

    <div class="form-group">
        {!! Form::label('name', 'Name'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group extra-attribute">
        <div class="row">
            <div class="col-md-5">
                {!! Form::label('grade_id', 'Grade'); !!}
                <!--        {!! Form::select('grade_id', ['' => ' -- Select Grade --']+$grades, null, ['id' => 'grade_id','class' => 'form-control']) !!}-->
                {!! Form::select('grade_id[0][]', $grades, null, ['multiple' => 'multiple','id' => 'grade_id','class' => 'form-control gradelist']) !!}
            </div>
            <div class="col-md-6">
                {!! Form::Label('Subject', 'Subject') !!}
        <!--        <select id="subject_id" class="form-control" name="subject_id[]" multiple="multiple">
                    <option value=""></option>
                </select>-->
                {!! Form::select('subject_id[]', $subjects, null, ['id' => 'subject_id','class' => 'form-control']) !!}
            </div>
            <div class="col-md-1">
                <div class="form-group no-margin">
                    <label class="control-label">&nbsp;</label>
                    <a class="form-control btn btn-sm btn-circle green easy-pie-chart-reload add_options" style="line-height: 1.8;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;" href="javascript:;">
                       <i class="fa fa-plus">&nbsp;Add</i>
                    </a>
                </div>
            </div>
        </div>
    </div>		

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/themes')}}">Cancel</a> 
    </div>
    {!! Form::close() !!}
</div>
<script type="text/javascript">
    $(document).ready(function () {

        $('#grade_id').SumoSelect({
            selectAll: true,
            triggerChangeCombined: false,
            placeholder: '--- Select Grade ---'
        });

//        $('#subject_id').SumoSelect({
//            selectAll: true,
//            triggerChangeCombined: false,
//            placeholder: '--- Select Subject ---'
//        });

        $('#grade_id').on('change', function (e) {
            //console.log(e);        
            var grade_id = e.target.value;
            $.get('{{ url('information') }}/create/ajax-grade-subject?grade_id=' + grade_id, function (data) {
                //console.log(data);
                $('#subject_id').empty();
                //$('#subject_id').append("<option value=''>--- Select Subject  ---</option>");
                $.each(data, function (index, subCatObj) {
                    //$('.subjs').attr('multiple', 'multiple');
                    $('#subject_id').append("<option value='" + subCatObj.id + "'>" + subCatObj.name + "</option>");
                });
                $('#subject_id')[0].sumo.reload();
            });
        });
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

</script>
@endsection
