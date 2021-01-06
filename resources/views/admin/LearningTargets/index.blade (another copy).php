@extends('layouts.admin')
@section('content')

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
    <tfoot>
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
    </tfoot>
    <tbody>
        @foreach($learningTargets as $key => $cntr)    
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$cntr->lt_name}}</td>
                <td>{{$cntr->g_name}}</td>
                
                <td>{{$cntr->s_name}}</td>
                <td>{{$cntr->t_name}}</td>
                <td>{{$cntr->kc_name}}</td>
                <td>{{$cntr->lt_status}}</td>
                <td>
                    <a href="{{ URL::to('admin/learningtargets/' . $cntr->lt_status . '/edit') }}" ><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" class="delete_record" onclick="delete_record({{$cntr->lt_status}})"><i class="fa icon-muted fa-times icon-space"></i></a>
<!--                    {!! Form::open(array('url' => 'admin/country/' . $cntr->id,'method' => 'DELETE','style'=>'display:inline-block')) !!}
                        {!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'btn-link','onclick'=>'return confirm("Are you sure you want to delete this record")']) !!}
                    {!! Form::close() !!}-->
                </td>
          </tr>
        @endforeach
    </tbody>
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

        oTable = $('#learning_targets_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            "aaSorting": [[0, '']],
            ajax: '{{ url(' / admin / learningtargets / getData') }}',
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'lt_id', name: 'learningtargets.id'},
                {data: 'lt_name', name: 'learningtargets.name'},
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
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                        );

                                column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                            });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            },
            fnCreatedRow: function (nRow, aData, iDataIndex) {
//                        var status;
//                        if(aData.status == 1){
//                            status = "Active";
//                        }else{
//                            status = "Inactive";
//                        }   
//                        $('td:eq(2)', nRow).html(status);

                $('td:eq(7)', nRow).html(
                        //'<a href="<?php echo url("/admin/learningtargets") ?>/' + aData.id + '"><i class="fa fa-eye icon-muted fa-fw icon-space"></i></a>' +
                        '<a href="<?php echo url("/admin/learningtargets") ?>/' + aData.id + '/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>' +
                        '<a href="javascript:void(0);" class="delete_record" onclick="delete_record(' + aData.id + ')"><i class="fa icon-muted fa-times icon-space"></i></a>');
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                $('td:eq(0)', nRow).html(iDisplayIndex + 1);
                //nRow.setAttribute('id', "row_" + aData.id);
            }
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