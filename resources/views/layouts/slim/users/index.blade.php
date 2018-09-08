@extends('app')
@section('title', 'Crew List')

@section('content')
    <div class="slim-pageheader">
        <ol class="breadcrumb slim-breadcrumb">
        </ol>
        <h6 class="slim-pagetitle">Crew List</h6>
    </div><!-- slim-pageheader -->
    
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="section-wrapper">
                @include('users.table')
                {{ $users->links('pagination.default') }}
            </div>
        </div>
    </div>
@endsection