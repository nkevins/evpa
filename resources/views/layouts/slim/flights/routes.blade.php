@extends('app')
@section('title', 'Route Map')

@section('content')
    <div class="slim-pageheader">
        <ol class="breadcrumb slim-breadcrumb">
        </ol>
        <h6 class="slim-pagetitle">Route Map</h6>
    </div><!-- slim-pageheader -->
    
    <div class="row row-sm">
        <div class="col-md-12">
            <div class="card bd-0">
            <div class="card-header tx-medium bd-0 tx-white bg-danger">
                Destination Map
            </div><!-- card-header -->
            <div class="card-body bd bd-t-0">
                {{ Widget::scheduleMap(['selected_airport' => 'OMDB']) }}
            </div>
        </div>
    </div>
@endsection
