@extends('layouts.admin')
@section('content')

<table id="theme_index" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th>Id</th>
            <th width="70%">Themes</th>
            <!--<th width="25%">Subject</th>-->
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function () {

        oTable = $('#theme_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            "aaSorting": [[0,'']],
            ajax: '{{ url('/admin/themes/getData') }}',
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
//                {data: 'subject_name', name: 'subject_name'},
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
                        //'<a href="<?php echo url("/admin/themes") ?>/' + aData.id + '"><i class="fa fa-eye icon-muted fa-fw icon-space"></i></a>' +
                        '<a href="<?php echo url("/admin/themes") ?>/' + aData.id + '/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>' +
                        '<a href="javascript:deleteTheme(' + aData.id + ');"><i class="fa icon-muted fa-times icon-space"></i></a>');
                //<a href="javascript:void(0);" class="delete_record" onclick="delete_record('+aData.id+')"><i class="fa icon-muted fa-times icon-space"></i></a>
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                $('td:eq(0)',nRow).html(iDisplayIndex +1);
                //nRow.setAttribute('id', "row_" + aData.id);
            }
        });
    });

    function deleteTheme(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                type: "DELETE",
                url: 'themes/' + id, //resource
                success: function (affectedRows) {
                    //if something was deleted, we redirect the user to the users page, and automatically the user that he deleted will disappear
                    if (affectedRows > 0)
                    {
                        window.location = 'themes';
                    }
                }
            });
        }
    }
</script>
@endsection
