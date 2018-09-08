@extends('app')
@section('title', 'Flight Assignment')

@section('content')
    <div class="slim-pageheader">
        <ol class="breadcrumb slim-breadcrumb">
        </ol>
        <h6 class="slim-pagetitle">Flight Assignment</h6>
    </div><!-- slim-pageheader -->
    
    <div class="row row-sm">
        @include('flash::message')
        <div class="col-lg-4">
            @include('flights.search')
        </div>
        <div class="col-lg-8">
            @include('flights.table')
        </div>
    </div>
@endsection

@include('flights.scripts')
