@extends('layouts.admin')
@section('content')
<style>
    .import-btn-listing.btn.btn-info.pull-right {
        margin-left: 1%;
    }
    .btn-info,.btn-info:hover{
        background-color: #f7f7f7;
        border-color: #f7f7f7;
    }
    .modal-footer{
        border: none;
    }
</style>
<!--<div class="panel-body">
    <div class="form-group">
        <div class=" col-md-3"> Total User</div>
  
        <div class=" col-md-3"> Total Jobs</div>
   
        <div class=" col-md-3"> Total User</div>
   
        <div class=" col-md-3"> Total Categories</div>
    </div>
    <div class="form-group">
        <div class=" col-md-3"> Total User</div>
    </div>
</div>-->
<!--//import test start-->
<?php //if ($action == '' && $controller == 'dashboard') {
?>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Import csv file for code</h4>
            </div>
            <div class="modal-body import_section">
                {!! Form::open(array('url' => 'admin/dashboard/csvUpload','files'=>'true','class'=>'form-horizontal','method'=>'POST','id'=>'imports')) !!}
                <div class="col-md-12">
                    {!! Form::Label('Subject', 'Subject') !!}
            <!--        <select id="subject_id" class="form-control" name="subject_id[]" multiple="multiple">
                        <option value=""></option>
                    </select>-->
                    {!! Form::select('subject_id[]', ['' => '--- Select Subject ---']+$subjects, null, ['id' => 'subject_id','class' => 'form-control']) !!}
                </div>
                <div class="col-md-12">
                    {!! Form::label('file_path', 'Csv file'); !!}
                    {!! Form::file('file_path') !!}
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    {!! Form::button('Import',array('type'=>'submit','class'=>'btn btn-primary')); !!}

                    {!! Form::close() !!}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div><?php
echo '<a class="import-btn-listing btn btn-info pull-right" data-toggle="modal" data-target="#myModal">Import</a>';
//                                            echo '<a href="'.$controller.'/csvExport" class="export-btn-listing btn btn-success pull-right">Export</a>';
//                                    echo '<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Import</button>';
//}
?>
<?php //import test end.... ?>
<script>

    $("#imports").validate({
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
                    "subject_id[]": {
                        required: true,
                        minlength: 1
                    },
                    "file_path": {required: true}

                },
        messages: {
            "subject_id[]": {required: "Please select subject."},
            "file_path": {required: "Please select csv file."},
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


