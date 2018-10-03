@extends('app')
@section('title', 'Profile')

@section('content')
    <div class="row mg-t-20">
        <div class="col-6">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    Profile
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    @include('profile.profile_data')
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
        <div class="col-6">
            <div class="row">
                <div class="col-12">
                    <div class="card bd-0">
                        <div class="card-header tx-medium bd-0 tx-white bg-danger">
                            Voyage Map
                        </div><!-- card-header -->
                        <div class="card-body bd bd-t-0">
                            {{ Widget::VoyageMap([
                                'width' => '100%',
                                'height' => '300px',
                                'user' => $user,
                            ]) }}
                        </div><!-- card-body -->
                    </div><!-- card -->
                </div>
            </div>
            
            <div class="row mg-t-20">
                <div class="col-12">
                    <div class="card bd-0">
                        <div class="card-header tx-medium bd-0 tx-white bg-danger">
                            Statistic
                        </div><!-- card-header -->
                        <div class="card-body bd bd-t-0">
                            @include('profile.stats_data')
                        </div><!-- card-body -->
                    </div><!-- card -->
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mg-t-20">
        <div class="col-12">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    Logbook
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <div class="row">
                        @include('profile.pirep_table')
                    </div>
                    <div class="row mg-t-20">
                        <div class="col-12 text-center">
                            {{ $pireps->links('pagination.default') }}
                        </div>
                    </div>
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
    </div>
@endsection