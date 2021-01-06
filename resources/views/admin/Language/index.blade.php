@extends('layouts.admin')
@section('content')
<table id="language_label" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th width="10%">Id</th>
            <th width="10%">Code</th> 
            <th width="35%">Static Label</th>           
            <th width="35%">Changed Label</th>
            <th width="10%">Actions</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function(){
        
        oTable =  $('#language_label').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            ajax: '{{ url('/admin/language/getData') }}',
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
            { data: 'id', name: 'id' },
            { data: 'lang_code', name: 'lang_code' },
            { data: 'label', name: 'label' },     
            { data: 'changed_label', name: 'changed_label' },
            { data: 'created_at', name: 'created_at' }
            ],
            aoColumnDefs: [
            {
                bSortable : false,
                aTargets : [ 4 ]
            },
            ],
            fnCreatedRow: function(nRow, aData, iDataIndex) {
                $('td:eq(4)', nRow).html('<a href="<?php echo url("/admin/language") ?>/'+aData.id+'/edit"><i class="fa icon-muted fa-pencil icon-space"></i></a>');
            },
            fnRowCallback: function(nRow, aData, iDisplayIndex) {
                nRow.setAttribute('id',"row_"+aData.id);
            }
        });         
    });
</script>
@endsection
