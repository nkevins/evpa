@extends('auth.layout')
@section('title', 'Log In')

@section('content')
  <div class="signin-box">
    <h2 class="signin-title-primary">Integrated Operation Crew System (IOCS)</h2>
    <h5 class="signin-title-secondary">You should have received an e-mail with login credentials</h5>
    
    @if (count($errors) > 0)
      <div class="alert alert-danger">
        <button type="button"class="close" data-dismiss="alert">&times;</button>
        {{ $errors->first() }}
      </div>
    @endif
    {{ Form::open(['url' => url('/login'), 'method' => 'post']) }}
      <div class="form-group">
        {{
          Form::text('email', old('email'), [
              'id' => 'email',
              'placeholder' => 'Email',
              'class' => 'form-control',
              'required' => true,
          ])
        }}
      </div><!-- form-group -->
      <div class="form-group mg-b-50">
        {{
          Form::password('password', [
              'name' => 'password',
              'class' => 'form-control',
              'placeholder' => 'Password',
              'required' => true,
          ])
        }}
      </div><!-- form-group -->
      <input type="submit" class="btn btn-primary btn-block btn-signin" value="Sign In" /> 
    {{ Form::close() }}
    <p class="mg-b-0">Don't have an account? <a href="{{ url('/register') }}">Sign Up</a></p>
  </div>
@endsection