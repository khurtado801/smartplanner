<?php
// echo '<pre>';
// print_r($_SESSION);
// die;
?>
@extends('layouts.admin')
@section('content')

@if(Session::has('msg'))
<div class="alert alert-info">
    <a class="close" data-dismiss="alert">×</a>
    <strong>Heads Up!</strong> {!!Session::get('msg')!!}
</div>
@endif
<table id="grade_index" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th>Id</th>
            <th width="70%">Grade Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function () {
        oTable = $('#grade_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            "aaSorting": [[0,'']],
            ajax: '{{ url('/admin/grades/getData') }}',
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'status', name: 'status'},
                {data: 'id', name: 'id'}
            ],
            aoColumnDefs: [
                {
                    bSearchable: false,
                    bSortable: false,
                    aTargets: [0,3]
                },
            ],
            fnCreatedRow: function (nRow, aData, iDataIndex) {
                $('td:eq(3)', nRow).html(
                        //'<a href="<?php echo url("/admin/grades") ?>/' + aData.id + '"><i class="fa fa-eye icon-muted fa-fw icon-space"></i></a>' +
                        '<a href="<?php echo url("/admin/grades") ?>/' + aData.id + '/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>' +
                        '<a href="javascript:deleteGrade(' + aData.id + ');"><i class="fa icon-muted fa-times icon-space"></i></a>');
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                $('td:eq(0)',nRow).html(iDisplayIndex +1);
                nRow.setAttribute('id', "row_" + aData.id);
            }
        });
    });

    function deleteGrade(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                type: "DELETE",
                url: 'grades/' + id, //resource
                success: function (affectedRows) {
                    //if something was deleted, we redirect the user to the users page, and automatically the user that he deleted will disappear
                    if (affectedRows > 0)
                    {
                        window.location = 'grades';
                    }
                }
            });
        }
    }
</script>
@endsection
