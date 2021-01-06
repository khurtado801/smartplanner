@extends('layouts.admin')
@section('content')
{{Html::script("/resources/assets/js/jquery.dataTables.columnFilter.js")}}
<div class="panel-body">
    <form method="POST" id="search-form" role="form">
        <div class="form-group col-sm-6">
            <div class="control-label">
            <label for="lt_name">Learning Target</label>
            </div>
            <div>
            <input type="text" class="form-control" name="lt_name" id="lt_name">
            </div>
        </div>
        <div class="col-sm-6 form-group">
            <div class="control-label">
            {!! Form::label('g_name', 'Grade'); !!}
            </div>
            <div>
                 <select name="g_name" id="g_name" class="form-control">
                    <option value="" key="">-- Select --</option>
                    @foreach ($grades as $gkey => $grade)
                        <option key="{{$gkey}}" value="{{$grade}}">{{$grade}}</option>
                    @endforeach
                </select>
            <!--{!! Form::select('g_name', array(''=>'--- Select Grade ---')+$grades, null, ['id' => 'g_name','class' => 'form-control']) !!}-->
            </div>
        </div>
        <div class="form-group col-sm-6">
            <div class="control-label">
            {!! Form::label('s_name', 'Subjects'); !!}
            </div>
            <div>
<!--            <select name="s_name" id="s_name" class="form-control">
                <option value="" key="">-- Select --</option>
                @foreach ($subjects as $skey => $subject)
                    <option key="{{$skey}}" value="{{$subject}}">{{$subject}}</option>
                @endforeach
            </select>-->
            {!! Form::select('s_name', array(''=>'--- Select Subjects ---'), null, ['id' => 's_name','class' => 'form-control','disabled' => '']) !!}
            </div>
        </div>
        <div class="form-group col-sm-6">
            <div class="control-label">
            {!! Form::label('t_name', 'Theme'); !!}
            </div>
            <div>
<!--            <select name="t_name" id="t_name" class="form-control">
                <option value="" key="">-- Select --</option>
                @foreach ($theme as $tkey => $th)
                    <option key="{{$tkey}}" value="{{$th}}">{{$th}}</option>
                @endforeach
            </select>-->
            {!! Form::select('t_name', array(''=>'--- Select Theme ---'), null, ['id' => 't_name','class' => 'form-control','disabled' => '']) !!}
            </div>
        </div>
        <div class="form-group col-sm-6">
            <div class="control-label">
            {!! Form::label('kc_name', 'Key Concepts'); !!}
            </div>
            <div>
            {!! Form::select('kc_name', array(''=>'--- Key Concepts ---'), null, ['id' => 'kc_name','class' => 'form-control','disabled' => '']) !!}
            </div>
        </div>
    </form>
</div>
<table id="learning_targets_index" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th>Id</th>
            <th>Learning Targets</th>
            <th>Grade name</th>
            <th>Subject</th>
            <th>Theme</th>
            <th>Keyconcept</th>
            <th>status</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

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
                    {!! Form::select('subject_id', $subjects, null, ['id' => 'subject_id','class' => 'form-control']) !!}
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
</div>
<div class="row">
    <div class="col-lg-8">
        <h2 class="block">Import Excel sheet </h2>
        <a class="import-btn-listing btn btn-info pull-right" data-toggle="modal" data-target="#myModal">Import</a>
        <div class="note note-warning">            
            <h4 class="block">Step 1 </h4>    
            <p> Select CSV file and select appropriate subject</p>
            <h4 class="block">Step 2 </h4>    
            <p> Import data in temporary database</p>
            <h4 class="block">Step 3 </h4>    
            <p> Import in database</p>
            <div class="update_status">
                <h4 class="block">Result </h4>    
                <h3> Total <span class="total_rec">0</span> records pending to process.</h3>
            </div>
        </div>
        <h4 class="block" style="color:red;">Please don't close this tab while processing records</h4>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#g_name').change(function () {
            $('#t_name').html('<option selected="selected" value="">--- Select Theme ---</option>');
            $('#s_name').html('<option selected="selected" value="">--- Select Subjects ---</option>');
            $('#kc_name').html('<option selected="selected" value="" key="">--- Key Concepts ---</option>');
            $('#s_name').prop('disabled', 'disabled');
            $('#t_name').prop('disabled', 'disabled');
            $('#kc_name').prop('disabled', 'disabled');
            var grade_id = $("#g_name option:selected").attr('key');
            var getSubjectsByGrade = '<?php echo url('/admin/learningtargets/getSubjectsByGrade') ?>';
            
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            $.ajax({
                url: getSubjectsByGrade,
                type: "post",
                data: {grade_id: grade_id},
                success: function (data)
                {
                    var dataObj = jQuery.parseJSON(data);
                    
                    if(dataObj.STATUS == '1'){
                        
                        $.each(dataObj.DATA, function (key, value) {
                            $('#s_name').append('<option key="'+value.id+'" value="'+value.name+'">'+value.name+'</option>');
                        });
                        $('#s_name').prop('disabled', false);
                    }
                }
            });
        });
        
        // Get subjects by grade id
        $('#s_name').change(function () {
            $('#t_name').html('<option selected="selected" value="" key="">--- Select Theme ---</option>');
            $('#kc_name').html('<option selected="selected" value="" key="">--- Key Concepts ---</option>');

            $('#t_name').prop('disabled', 'disabled');
            $('#kc_name').prop('disabled', 'disabled');
            
            var grade_id = $("#g_name option:selected").attr('key');
            var subject_id = $("#s_name option:selected").attr('key');
            var getThemesByGradeAndSubjects = '<?php echo url('/admin/learningtargets/getThemesByGradeAndSubjects') ?>';
            
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            $.ajax({
                url: getThemesByGradeAndSubjects,
                type: "post",
                data: {grade_id: grade_id,subject_id:subject_id},
                success: function (data)
                {
                    var dataObj = jQuery.parseJSON(data);
                    
                    if(dataObj.STATUS == '1'){
                        $.each(dataObj.DATA, function (key, value) {
                            $('#t_name').append('<option key="'+value.theme_id+'" value="'+value.name+'">'+value.name+'</option>');
                        });
                        $('#t_name').prop('disabled', false);
                    }else{
                        $('#t_name').prop('disabled', false);
                    }
                }
            });
        });
        
        $('#t_name').change(function () {
            $('#kc_name').html('<option selected="selected" value="" key="">--- Key Concepts ---</option>');
            var theme_id = $("#t_name option:selected").attr('key');

            var getKeyconceptByTheme = '<?php echo url('/admin/learningtargets/getKeyconceptByTheme') ?>';
            
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            $.ajax({
                url: getKeyconceptByTheme,
                type: "post",
                data: {theme_id: theme_id},
                success: function (data)
                {
                    var dataObj = jQuery.parseJSON(data);
                    
                    if(dataObj.STATUS == '1'){
                        $.each(dataObj.DATA, function (key, value) {
                            $('#kc_name').append('<option key="'+value.kc_id+'" value="'+value.keyconcept_name+'">'+value.keyconcept_name+'</option>');
                        });
                        $('#kc_name').prop('disabled', false);
                    }
                }
            });
        });
        
        oTable = $('#learning_targets_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            "aaSorting": [[0, '']],
//            ajax: '{{ url('/admin/learningtargets/getData') }}',
            ajax: {
                url: '{{ url('/admin/learningtargets/getData') }}',
                data: function (d) {
                    d.s_name = $('select[name=s_name]').val();
                    d.t_name = $('select[name=t_name]').val();
                    d.g_name = $('select[name=g_name]').val();
                    d.lt_name = $('input[name=lt_name]').val();
                    d.kc_name = $('select[name=kc_name]').val();
                }
            },
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'lt_id', name: 'learningtargets.id'},
                {data: 'lt_name', name: 'learningtargets_name.name'},
                {data: 'g_name', name: 'grades.name'},
                {data: 's_name', name: 'subjects.name'},
                {data: 't_name', name: 'themes.name'},
                {data: 'kc_name', name: 'keyconcepts.name'},
                {data: 'lt_status', name: 'learningtargets.status'},
                {data: 'lt_id', name: 'learningtargets.id'}
            ],
            aoColumnDefs: [
                {
                    bSearchable: false,
                    bSortable: false,
                    aTargets: [0, 7]
                },
            ],
            fnCreatedRow: function (nRow, aData, iDataIndex) {
                $('td:eq(7)', nRow).html(
                        '<a href="<?php echo url("/admin/learningtargets") ?>/' + aData.lt_id + '/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>' +
                        '<a href="javascript:void(0);" class="delete_record" onclick="delete_record(' + aData.lt_id + ')"><i class="fa icon-muted fa-times icon-space"></i></a>');
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                $('td:eq(0)', nRow).html(iDisplayIndex + 1);
            }
        });
        $('#search-form').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

    window.pendingrec = null;
    $(document).ready(function () {
        function execute_target() {
            if (window.pendingrec != 0) {
                $.ajax({
                    url: '<?php echo url("/excute"); ?>',
                    error: function () {
                        $('#info').html('<p>An error has occurred</p>');
                    },
                    dataType: 'json',
                    success: function (data) {
                        //alert(data.pending);
                        window.pendingrec = data.pending;
                        $('.total_rec').html(data.pending);

                        if (data.pending != 0) {
                            $('.update_status').show();
                        }

                    },
                    type: 'POST'
                });
            }
        }
        setInterval(execute_target, 6000);


    });

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