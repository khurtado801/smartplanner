@extends('layouts.admin')
@section('content')

<table id="lesson_index" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th width="5%">Id</th>
            <th>Unit Title</th>
            <th>Grade</th>
            <th>Subject</th>
            <th>Theme</th>
            <th>User</th>
            <th>Status</th>
            <th width="15%">Actions</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function () {

        oTable = $('#lesson_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            "aaSorting": [[0,'']],
            ajax: '{{ url('/admin/lessons/getData') }}',
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'id', name: 'us.lastname'},//'ls.id'
                {data: 'unit_title', name: 'ls.unit_title'},
                {data: 'grade_name', name: 'gr.name'},
                {data: 'subject_name', name: 'sub.name'},
                {data: 'theme_name', name: 'th.name'},
                {data: 'user_fname', name: 'us.firstname'},
                {data: 'status', name: 'ls.status'},
                {data: 'id', name: 'ls.id'}
            ],
            aoColumnDefs: [
                {
                    bSearchable: false,
                    bSortable: false,
                    aTargets: [7]
                },
            ],
            fnCreatedRow: function (nRow, aData, iDataIndex) {
                $('td:eq(7)', nRow).html(
                        //'<a href="<?php echo url("/admin/lessons/") ?>/' + aData.id + '/destroy"><i class="fa fa-eye icon-muted fa-fw icon-space"></i></a>' +
                '<a href="<?php echo url("/admin/lessons") ?>/' + aData.id  + '/lessonsContent"><i class="fa fa-file-text icon-space" aria-hidden="true"></i></a>' +
                        '<a href="<?php echo url("/admin/lessons") ?>/' + aData.user_id +'"><i class="fa fa-list-alt icon-space" aria-hidden="true"></i></a>' +
                        '<a href="<?php echo url("/admin/lessons") ?>/' + aData.id + '/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>' +
                        '<a href="javascript:deleteLesson(' + aData.id + ');"><i class="fa icon-muted fa-times icon-space"></i></a>');
                //<a href="javascript:void(0);" class="delete_record" onclick="delete_record('+aData.id+')"><i class="fa icon-muted fa-times icon-space"></i></a>
                
                $('td:eq(5)',nRow).html(aData.user_fname+" "+aData.user_lname);
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                $('td:eq(0)',nRow).html(iDisplayIndex +1);
                //nRow.setAttribute('id', "row_" + aData.id);
            }
        });
    });

    function deleteLesson(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                type: "DELETE",
                url: 'lessons/' + id, //resource
                success: function (affectedRows) {
                    //if something was deleted, we redirect the user to the users page, and automatically the user that he deleted will disappear
                    if (affectedRows > 0)
                    {
                        window.location = 'lessons';
                    }
                }
            });
        }
    }
</script>
@endsection
