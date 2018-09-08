@extends('app')
@section('title', 'Crew Promotion')

@section('content')
    <div class="slim-pageheader">
        <ol class="breadcrumb slim-breadcrumb">
        </ol>
        <h6 class="slim-pagetitle">Crew Promotion</h6>
    </div><!-- slim-pageheader -->
    
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="section-wrapper">
                @include('users.table_promotions')
            </div>
        </div>
    </div>
@endsection