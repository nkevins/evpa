@extends('app')
@section('title', 'Profile')

@section('content')
    <div class="row mg-t-20">
        <div class="col-6">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    My Profile
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <div class="row profile-page content-center text-color-dark-beige">
                        <div class="col-md-6">
                            <h3 class="title">{{ $user->name }}</h3>
                            <h6><span class="flag-icon flag-icon-{{ $user->country }}"></span></h6>
                            <h6>{{ $user->pilotId }}</h6>
                            <h6>{{ $user->rank->name }}</h6>
                            <p class="description" style="color: #9A9A9A;">
                                {{ $user->airline->name }}
                            </p>
                        </div>
                        <div class="col-md-6 content-center">
                            <div class="content">
                                <div class="social-description">
                                    <h2>{{ $user->flights}}</h2>
                                    <p>Flights</p>
                                </div>
                    
                                <div class="social-description">
                                    <h2>{{ \App\Facades\Utils::minutesToTimeString($user->flight_time, false) }}</h2>
                                    <p>Flight Hours</p>
                                </div>
                    
                                @if($user->home_airport)
                                    <div class="social-description">
                                        <h2>{{ $user->home_airport->icao }}</h2>
                                        <p>Home Airport</p>
                                    </div>
                                @endif
                    
                                @if($user->current_airport)
                                    <div class="social-description">
                                        <h2>{{ $user->current_airport->icao }}</h2>
                                        <p>Current Airport</p>
                                    </div>
                                @endif
                    
                            </div>
                        </div>
                    </div>
                    
                    @if(Auth::check() && $user->id === Auth::user()->id)
                        <div class="clearfix" style="height: 50px;"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="text-right">
                                    <a href="{{ route('frontend.profile.regen_apikey') }}" class="btn btn-warning"
                                       onclick="return confirm('Are you sure? This will reset your API key.')">new api key</a>
                                    &nbsp;
                                    <a href="{{ route('frontend.profile.edit', ['id' => $user->id]) }}"
                                       class="btn btn-primary">edit</a>
                                </div>
                    
                                <h3 class="description">Your Profile</h3>
                                <table class="table table-full-width">
                                    <tr>
                                        <td>Email</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>API Key&nbsp;&nbsp;<span class="description">don't share this!</span></td>
                                        <td>{{ $user->api_key }}</td>
                                    </tr>
                                    <tr>
                                        <td>Timezone</td>
                                        <td>{{ $user->timezone }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
        <div class="col-6">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    Logbook
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
    </div>
@endsection