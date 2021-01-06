@extends('layouts.admin')
@section('content')

<table id="subcategory_index" class="table table-striped m-b-none">
        <thead>
            <tr>
                <th width="15%">Id</th>
                <th width="25%">Sub Categories</th>
                <th width="10%">Status</th>
                <th width="25%">Created At</th>
                <th width="25%">Actions</th>
            </tr>
        </thead>
</table>
<script>
$(document).ready(function(){
    
    oTable =  $('#subcategory_index').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    bRetrieve: true,
                    sPaginationType: "full_numbers",
                    ajax: '{{ url('/admin/sub_categories/getData') }}',
                    sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'name', name: 'name' },
                        { data: 'status', name: 'status' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'updated_at', name: 'updated_at' }
                    ],
                    aoColumnDefs: [
                        {
                            bSortable : false,
                            aTargets : [ 4 ]
                        },
                    ],
                    fnCreatedRow: function(nRow, aData, iDataIndex) {
                        $('td:eq(4)', nRow).html('<a href="<?php echo url("/admin/sub_categories") ?>/'+aData.id+'"><i class="fa fa-eye icon-muted fa-fw icon-space"></i></a>'+
                                                '<a href="<?php echo url("/admin/sub_categories") ?>/'+aData.id+'/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>'
                                                );
                                        //<a href="javascript:void(0);" class="delete_record" onclick="delete_record('+aData.id+')"><i class="fa icon-muted fa-times icon-space"></i></a>
                    },
                    fnRowCallback: function(nRow, aData, iDisplayIndex) {
                            nRow.setAttribute('id',"row_"+aData.id);
                    }
    });         
});
</script>
@endsection