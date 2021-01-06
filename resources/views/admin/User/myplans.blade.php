@extends('layouts.admin')
@section('content')

<table id="users_plans" class="table table-striped m-b-none">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Plan</th>
            <th>Start</th>
            <th width="10%">Status</th>
            <th>Subscription Status</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function () {

        var action_url = '<?php echo url('/admin/user/getMyPlans/' . $uid); ?>';

        oTable = $('#users_plans').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            bRetrieve: true,
            sPaginationType: "full_numbers",
            ajax: action_url,
            sDom: "<'row'<'col-lg-6 leave_filter'><'col-lg-3'l><'col-lg-3'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
            columns: [
                {data: 'id', name: 'user_subscribe.id'},
                {data: 'name', name: 'name'},
                {data: 'subscribe_date', name: 'subscribe_date'},
                {data: 'recurring_status', name: 'recurring_status'},
                {data: 'transaction_id', name: 'transaction_id'}
            ],
            aoColumnDefs: [
                {
                    bSortable: false,
                    aTargets: [0, 4]
                },
            ],
            fnCreatedRow: function (nRow, aData, iDataIndex) {
               /* $('td:eq(4)', nRow).html(
                        '<a href="javascript:changeAgreementStatus(' + aData.user_id + ','+iDataIndex+');" id="agreement_id_'+iDataIndex+'" title=' + aData.transaction_id + '><i class="fa icon-muted fa-credit-card icon-space"></i></a>');*/
                var htmlw= '<select name="action" onchange="changeAgreementStatus(\''+aData.recurring_profile+'\',this.value)">';
                    
                    var sel1 = '';    
                    var sel2 = '';
                    var sel3 = '';
                    var sel4 = '';
                    if(aData.recurring_status=='ActiveProfile'){
                        var sel1 = 'selected="selected"';
                        htmlw+='<option value="ActiveProfile" '+sel1+'>Active</option>';
                    }

                    if(aData.recurring_status=='Cancel'){
                        var sel2 = 'selected="selected"';
                    }
                    if(aData.recurring_status=='Suspend'){
                        var sel3 = 'selected="selected"';
                    }
                    if(aData.recurring_status=='Reactivate'){
                        var sel4 = 'selected="selected"';
                    }
                    
                    htmlw+='<option value="Cancel" '+sel2+'>Cancel</option>';
                    if(aData.recurring_status!=='Cancel'){
                        htmlw+='<option value="Suspend" '+sel3+'>Suspend</option>';
                    }
                    if(aData.recurring_status!='ActiveProfile' && aData.recurring_status!='Cancel'){
                        htmlw+='<option value="Reactivate" '+sel4+'>Reactivate</option>';
                    }
                '</select>';
                $('td:eq(4)', nRow).html(htmlw);
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                //nRow.setAttribute('id', "row_" + aData.transaction_id);
                $('td:eq(0)', nRow).html(iDisplayIndex + 1);
            }
        });
    });

    function changeAgreementStatus(transaction_id,status) {
        
        
        if (confirm('Are you sure you want to change?')) {
            var changeAgreementStatus = '<?php echo url('/admin/user/changeAgreementStatus') ?>';

            $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            $.ajax({
                url: changeAgreementStatus,
                type: "post",
                data: {transaction_id: transaction_id,status:status},
                success: function (response) {
                    var response = $.parseJSON(response);
                    
                    if(response.STATUS==1){
                        location.reload();
                    }else{
                        alert(response.MESSAGE);
                    }

                }
            });
        }
    }
</script>
@endsection
