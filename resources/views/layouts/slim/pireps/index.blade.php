@extends('app')
@section('title', 'PIREPS')

@section('content')
    <div class="slim-pageheader">
        <ol class="breadcrumb slim-breadcrumb">
        </ol>
        <h6 class="slim-pagetitle">PIREPS</h6>
    </div><!-- slim-pageheader -->
    
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="section-wrapper">
                @include('pireps.table')
            </div>
        </div>
    </div>
@endsection