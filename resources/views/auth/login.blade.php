@extends('layouts.app')
@section('content')
<form role="form" action="{{ url('/admin/login') }}" method="post" class="panel-body wrapper-lg">
   {!! csrf_field() !!}
   <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
      <label class="control-label">E-mail</label>
      <input  name="email"  type="email" placeholder="E-mail" class="form-control input-lg" value="{{ old('email') }}" />
      @if ($errors->has('email'))
      <span class="help-block">
        <strong>{{ $errors->first('email') }}</strong>
    </span>
    @endif
    </div>
    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
      <label class="control-label">Password</label>
      <input name="password" type="password" id="inputPassword" placeholder="Password" class="form-control input-lg">
      @if ($errors->has('password'))
      <span class="help-block">
        <strong>{{ $errors->first('password') }}</strong>
    </span>
    @endif
    </div>

    <a href="{{ url('/admin/password/reset') }}" class="pull-right m-t-xs"><small>Forgot password?</small></a>
    <button type="submit" class="btn btn-primary">Sign in</button>
    <div class="line line-dashed"></div>

          <!-- <p class="text-muted text-center"><small>Do not have an account?</small></p>
          <a href="{{ url('/admin/register') }}" class="btn btn-default btn-block">Create an account</a> -->
</form>
@endsection
