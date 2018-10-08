@extends('app')
@section('title', __('profile.editprofile'))

@section('content')
<div class="row mg-t-20">
    <div class="col-md-12">
        <div class="section-wrapper">
        <h2 class="description">@lang('profile.edityourprofile')</h2>
          @include('flash::message')
          {{ Form::model($user, ['route' => ['frontend.profile.update', $user->id], 'files' => true, 'method' => 'patch']) }}
             @include("profile.fields")
          {{ Form::close() }}
        </div>
    </div>
</div>
@endsection