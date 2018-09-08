@extends('app')
@section('title', 'Briefing Room')

@section('content')
    <div class="slim-pageheader">
        <ol class="breadcrumb slim-breadcrumb">
        </ol>
        <h6 class="slim-pagetitle">Briefing Room</h6>
    </div><!-- slim-pageheader -->
    
    <div class="row row-sm">
        @include('flash::message')
        <div class="col-lg-12">
            @include('flights.table_bids')
        </div>
    </div>
@endsection

@include('flights.scripts')
