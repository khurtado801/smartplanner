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
<table id="educationalquote_index" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th>Id</th>
            <th width="30%">Quote Line 1</th>
            <th width="30%">Quote Line 2</th>
            <th width="15%">Philosopher</th>
            <th width="10%">Status</th>
            <th width="10%">Actions</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function () {
        oTable = $('#educationalquote_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            "aaSorting": [[0,'']],
            ajax: '{{ url('/admin/educationalquotes/getData') }}',
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'quote_line1', name: 'quote_line1'},
                {data: 'quote_line2', name: 'quote_line2'},
                {data: 'author', name: 'author'},
                {data: 'status', name: 'status'},
                {data: 'updated_at', name: 'updated_at'}
            ],
            aoColumnDefs: [
                {
                    bSearchable: false,
                    bSortable: false,
                    aTargets: [0,5]
                },
            ],
            fnCreatedRow: function (nRow, aData, iDataIndex) {
                $('td:eq(5)', nRow).html(
                        //'<a href="<?php echo url("/admin/educationalquotes") ?>/' + aData.id + '"><i class="fa fa-eye icon-muted fa-fw icon-space"></i></a>' +
                        '<a href="<?php echo url("/admin/educationalquotes") ?>/' + aData.id + '/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>' +
                        '<a href="javascript:deleteEducationalQuote(' + aData.id + ');"><i class="fa icon-muted fa-times icon-space"></i></a>');
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                $('td:eq(0)',nRow).html(iDisplayIndex +1);
                //nRow.setAttribute('id', "row_" + aData.id);
            }
        });
    });

    function deleteEducationalQuote(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                type: "DELETE",
                url: 'educationalquotes/' + id, //resource
                success: function (affectedRows) {
                    //if something was deleted, we redirect the user to the users page, and automatically the user that he deleted will disappear
                    if (affectedRows > 0)
                    {
                        window.location = 'educationalquotes';
                    }
                }
            });
        }
    }
</script>
@endsection
