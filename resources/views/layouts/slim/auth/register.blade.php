@extends('auth.layout')
@section('title', 'Sign Up')

@section('content')
    <div class="signin-box signup">
        <h3 class="signin-title-primary">Sign Up</h3>
        
        @if (count($errors) > 0)
          <div class="alert alert-danger">
            <button type="button"class="close" data-dismiss="alert">&times;</button>
            {{ $errors->first() }}
          </div>
        @endif
        {{ Form::open(['url' => '/register', 'class' => 'form-signin']) }}
            <label for="name" class="control-label">Full Name</label>
            <div class="input-group form-group-no-border {{ $errors->has('name') ? 'has-danger' : '' }}">
                {{ Form::text('name', null, ['class' => 'form-control']) }}
            </div>
            
            <label for="email" class="control-label">Email Address</label>
            <div class="input-group form-group-no-border {{ $errors->has('email') ? 'has-danger' : '' }}">
                {{ Form::text('email', null, ['class' => 'form-control']) }}
            </div>
            
            <label for="airline" class="control-label">Airline</label>
            <div class="input-group form-group-no-border {{ $errors->has('airline') ? 'has-danger' : '' }}">
                {{ Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) }}
            </div>
            
            <label for="home_airport" class="control-label">Home Airport</label>
            <div class="input-group form-group-no-border {{ $errors->has('home_airport') ? 'has-danger' : '' }}">
                {{ Form::select('home_airport_id', $airports, null , ['class' => 'form-control select2']) }}
            </div>
            
            <label for="country" class="control-label">Country</label>
            <div class="input-group form-group-no-border {{ $errors->has('country') ? 'has-danger' : '' }}">
                {{ Form::select('country', $countries, null, ['class' => 'form-control select2' ]) }}
            </div>
            
            <label for="timezone" class="control-label">Timezone</label>
            <div class="input-group form-group-no-border {{ $errors->has('timezone') ? 'has-danger' : '' }}">
                {{ Form::select('timezone', $timezones, null, ['id'=>'timezone', 'class' => 'form-control select2' ]) }}
            </div>
            
            @if (setting('pilots.allow_transfer_hours') === true)
                <label for="transfer_time" class="control-label">Transfer Hours</label>
                <div class="input-group form-group-no-border {{ $errors->has('transfer_time') ? 'has-danger' : '' }}">
                    {{ Form::text('transfer_time', 0, ['class' => 'form-control']) }}
                </div>
                @if ($errors->has('transfer_time'))
                    <p class="text-danger">{{ $errors->first('transfer_time') }}</p>
                @endif
            @endif
            
            <label for="password" class="control-label">Password</label>
            <div class="input-group form-group-no-border {{ $errors->has('password') ? 'has-danger' : '' }}">
                {{ Form::password('password', ['class' => 'form-control']) }}
            </div>
            
            <label for="password_confirmation" class="control-label">Confirm Password</label>
            <div class="input-group form-group-no-border {{ $errors->has('password_confirmation') ? 'has-danger' : '' }}">
                {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
            </div>
            
            @if(config('captcha.enabled'))
                <label for="g-recaptcha-response" class="control-label">Fill out the captcha</label>
                <div class="input-group form-group-no-border {{ $errors->has('g-recaptcha-response') ? 'has-danger' : '' }}">
                    {!! NoCaptcha::display(config('captcha.attributes')) !!}
                </div>
                @if ($errors->has('g-recaptcha-response'))
                    <p class="text-danger">{{ $errors->first('g-recaptcha-response') }}</p>
                @endif
            @endif

            @include('auth.toc')
            
            <div style="width: 100%; text-align: right; padding-top: 20px;">
                By registering, you agree to the Term and Conditions<br /><br />
                {{ Form::submit('Register!', ['class' => 'btn btn-primary']) }}
            </div>
        {{ Form::close() }}
        
        <p class="mg-t-40 mg-b-0">Already have an account? <a href="{{ url('/') }}">Sign In</a></p>
    </div><!-- signin-box -->
@endsection

@section('scripts')
{!! NoCaptcha::renderJs(config('app.locale')) !!}
@endsection