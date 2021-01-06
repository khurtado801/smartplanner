@extends('layouts.admin')
@section('content')

<table id="mylessons_index" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th width="5%">Id</th>
            <th>Unit Title</th>
            <th>Grade</th>
            <th>Subject</th>
            <th>Theme</th>
            <th>Status</th>
            <!--<th width="15%">Actions</th>-->
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function () {
        
        var action_url = '<?php echo url('/admin/lessons/userLessons/'.Request::segment(3)); ?>';
        
        oTable = $('#mylessons_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            "aaSorting": [[0,'']],
            //ajax: '{{ url('/admin/lessons/userLessons/user_id') }}',//action_url,
            ajax: action_url,
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'id', name: 'ls.id'},
                {data: 'unit_title', name: 'ls.unit_title'},
                {data: 'grade_name', name: 'gr.name'},
                {data: 'subject_name', name: 'sub.name'},
                {data: 'theme_name', name: 'th.name'},
                {data: 'status', name: 'ls.status'}
//                {data: 'id', name: 'ls.id'}
            ],
            aoColumnDefs: [
                {
                    bSearchable: false,
                    bSortable: false,
                    aTargets: [0]
                },
            ],
            fnCreatedRow: function (nRow, aData, iDataIndex) {
                
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                $('td:eq(0)',nRow).html(iDisplayIndex +1);
                //nRow.setAttribute('id', "row_" + aData.id);
            }
        });
    });
</script>
@endsection
