@extends('layouts.admin')
@section('content')

<table id="users_index" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th>Id</th>
            <th>First Name</th>                
            <th>Last Name</th> 
            <th>Email</th>
            <th>Verified</th>
            <th>User Type</th>
            <th>Status</th>
            <th width="15%">Actions</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function () {
        oTable = $('#users_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            "aaSorting": [[0,'']],
            ajax: '{{ url('/admin/user/getData') }}',
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'firstname', name: 'firstname'},
                {data: 'lastname', name: 'lastname'},
                {data: 'email', name: 'email'},
                {data: 'email_verified', name: 'email_verified'},
                {data: 'usertype', name: 'usertype'},
                {data: 'status', name: 'status'},
                {data: 'id', name: 'id'},
            ],
            aoColumnDefs: [
                {
                    bSearchable: false,
                    bSortable: false,
                    aTargets: [0,7]
                },
            ],
            fnCreatedRow: function (nRow, aData, iDataIndex) {
                if (aData.usertype == 'Super Admin') {
                    $('td:eq(5)', nRow).html('Admin');
                }
                $('td:eq(7)', nRow).html(
                        '<a href="<?php echo url("/admin/user") ?>/viewPlans/' + aData.id + '" target="_blank"><i class="fa fa-briefcase icon-muted fa-fw icon-space" aria-hidden="true"></i></a>' +
                        '<a href="<?php echo url("/admin/user") ?>/' + aData.id + '/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>' +
                        '<a href="javascript:deleteUser(' + aData.id + ');"><i class="fa icon-muted fa-times icon-space"></i></a>');
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                //nRow.setAttribute('id', "row_" + aData.id);
                $('td:eq(0)',nRow).html(iDisplayIndex +1);
            }
        });
    });

    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                type: "DELETE",
                url: 'user/' + id, //resource
                success: function (affectedRows) {
                    //if something was deleted, we redirect the user to the users page, and automatically the user that he deleted will disappear
                    if (affectedRows > 0)
                    {
                        window.location = 'user';
                    }
                }
            });
        }
    }
</script>
@endsection
