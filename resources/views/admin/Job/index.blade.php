@extends('layouts.admin')
@section('content')

<table id="job_index" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th width="10%">Id</th>
            <th width="15%">Job Title</th>
            <th width="25%">Job Description</th>
            <th width="15%">Status</th>
            <th width="15%">Posted On</th>            
            <th width="20%">Actions</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function(){

        oTable =  $('#job_index').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            ajax: '{{ url('/admin/job/getData') }}',
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
            { data: 'id', name: 'id' },
            { data: 'job_title', name: 'job_title' },
            { data: 'job_description', name: 'job_description' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },            
            { data: 'updated_at', name: 'updated_at' }
            ],
            aoColumnDefs: [
            {
                bSortable : false,
                aTargets : [ 5 ]
            },
            ],
            fnCreatedRow: function(nRow, aData, iDataIndex) {
                $('td:eq(5)', nRow).html('<a href="<?php echo url("/admin/job") ?>/'+aData.id+'"><i class="fa fa-eye icon-muted fa-fw icon-space"></i></a>'+
                    '<a href="<?php echo url("/admin/job") ?>/'+aData.id+'/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>'
                    );                                      
            },
            
            fnRowCallback: function(nRow, aData, iDisplayIndex) {
                nRow.setAttribute('id',"row_"+aData.id);
            }
        });         
    });
</script>
@endsection