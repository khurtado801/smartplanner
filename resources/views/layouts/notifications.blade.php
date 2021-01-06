@if(Session::has('error_msg'))
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="fa fa-ban-circle"></i> {{Session::get('error_msg')}}
</div>
@endif
 
@if(Session::has('success_msg'))
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="fa fa-ok-sign"></i> {{Session::get('success_msg')}}
</div>
@endif
