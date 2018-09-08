@extends('app')
@section('title', 'Dashboard')

@section('content')
    <div class="slim-pageheader">
        <ol class="breadcrumb slim-breadcrumb">
        </ol>
        <h6 class="slim-pagetitle">Dashboard</h6>
    </div><!-- slim-pageheader -->
    
    <div class="row row-sm">
        <div class="col-lg-4">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fas fa-user"></i> Profile
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th scope="row">Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Nationality</th>
                            <td>
                                @if(filled($user->country))
                                    {{ App\Support\Countries::getSelectList()[$user->country] }} <span class="flag-icon flag-icon-{{ $user->country }}"></span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Home Airport</th>
                            <td>{{ $user->home_airport->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Contract</th>
                            <td>{{ Carbon\Carbon::parse($user->created_at)->format('d M y') }} - Open Ended</td>
                        </tr>
                        <tr>
                            <th scope="row">ICAO ATPL</th>
                            <td>
                                @if ($user->state == UserState::ACTIVE)
                                    <span class="badge badge-success">Valid</span>
                                @else
                                    <span class="badge badge-danger">Frozen</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Current Position</th>
                            <td>
                                @if($user->current_airport)
                                <a href="{{route('frontend.airports.show', [
                                                    'id' => $user->curr_airport_id
                                                    ])}}">{{ $user->curr_airport_id }}</a>
                                @else
                                <a href="{{route('frontend.airports.show', [
                                                    'id' => $user->home_airport_id
                                                    ])}}">{{ $user->home_airport_id }}</a>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
        <div class="col-lg-4 mg-t-20 mg-lg-t-0">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fas fa-trophy"></i> Airline Career
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th scope="row">Current Airline</th>
                            <td>{{ $user->airline->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Current Rating</th>
                            <td><img src="{{ $user->rank->image_url }}" title="{{ $user->rank->name }}" style="height:20px;" /></td>
                        </tr>
                        <tr>
                            <th scope="row">Flight Hour</th>
                            <td>{{ Utils::minutesToTimeString($user->flight_time) }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Flight</th>
                            <td>{{ $user->flights }}</td>
                        </tr>
                        <tr>
                            <th scope="row">UAE Citizen Permit</th>
                            <td>{{ $user->getCitizenshipStatus() }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Monthly Salary</th>
                            <td>AED {{ number_format($user->rank->acars_base_pay_rate, 2) }}</td>
                        </tr>
                    </table>
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
        <div class="col-lg-4 mg-t-20 mg-lg-t-0">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fas fa-envelope"></i> Company NOTAM
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    {{ Widget::latestNews(['count' => 5]) }}
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
    </div>
    
    <div class="row row-sm mg-t-20">
        <div class="col-lg-6">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fas fa-calendar"></i> Schedule
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <table class="table table-striped mg-b-0">
                        <thead>
                            <th>Flight No</th>
                            <th>Route</th>
                            <th>ETD</th>
                            <th>ETA</th>
                            <th>A/C</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($flights as $f)
                                <tr>
                                    <td><a href="{{ route('frontend.flights.show', [$f->id]) }}" target="_blank">{{ $f->airline->icao }}{{ $f->flight_number }}</a></td>
                                    <td>{{ $f->dpt_airport_id }} - {{ $f->arr_airport_id }}</td>
                                    <td>{{ Carbon\Carbon::parse($f->dpt_time)->format('H:i') }} Z</td>
                                    <td>{{ Carbon\Carbon::parse($f->arr_time)->format('H:i') }} Z</td>
                                    <td>
                                        <?php
                                            foreach ($f->subfleets as $sf) {
                                                $new_arr[] = $sf->type;
                                            }
                                        ?>
                                        {{ implode(', ', $new_arr) }}
                                        <?php 
                                            $new_arr = null;
                                        ?>
                                    </td>
                                    <td style="padding:0.4rem;">
                                        <button class="btn btn-primary btn-sm save_flight" x-id="{{ $f->id }}" x-saved-class="btn-info">Book</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
        <div class="col-lg-6 mg-t-20 mg-lg-t-0">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fas fa-map"></i> Maps
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <div id="map" style="width: 100%; height: 300px"></div>
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
    </div>
    
    <div class="row row-sm mg-t-20">
        <div class="col-lg-6">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fas fa-plane-arrival"></i> My Recent Flight
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    @if (count($last_pirep) == 0)
                        <p>No flights found</p>
                    @else
                        <table class="table table-striped mg-b-0">
                            <thead>
                                <th>Flight No</th>
                                <th>Route</th>
                                <th>Duration</th>
                                <th>Flight Date</th>
                            </thead>
                            <tbody>
                                @foreach ($last_pirep as $p)
                                    <tr>
                                        <td><a href="{{ route('frontend.pireps.show', [
                                            $p->id]) }}">{{ $p->airline->icao }}{{ $p->flight_number }}</a>
                                        </td>
                                        <td>{{ $p->dpt_airport_id }} - {{ $p->arr_airport_id }}</td>
                                        <td>{{ Utils::minutesToTimeString($p->flight_time) }}</td>
                                        <td>{{ Carbon\Carbon::parse($p->submitted_at)->format('d M y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
        <div class="col-lg-6 mg-t-20 mg-lg-t-0">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fas fa-plane-departure"></i> My Scheduled Flight
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    @if (count($bids) == 0)
                        <p>No scheduled flight found</p>
                    @else
                        <table class="table table-striped mg-b-0">
                            <thead>
                                <th>Flight No</th>
                                <th>Route</th>
                                <th>ETD</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($bids as $b)
                                    <tr>
                                        <td>{{ $b->flight->airline->icao }}{{ $b->flight->flight_number }}</td>
                                        <td>{{ $b->flight->dpt_airport_id }} - {{ $b->flight->arr_airport_id }}</td>
                                        <td>{{ Carbon\Carbon::parse($b->flight->dpt_time)->format('H:i') }} Z</td>
                                        <td><a href="#" class="btn btn-primary btn-sm">Briefing</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
    </div>
    
    <div class="row row-xs" style="margin-top:20px;">
        <div class="col-lg-6">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fas fa-chart-bar"></i> My General Statistic
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <div class="row">
                        <div class="col">
                            <table class="table table-borderless">
                                <tr>
                                    <th scope="row">Smoothest landing</th>
                                    <td>{{ number_format($stats['min_landing_rate']) }} ft/min</td>
                                </tr>
                                <tr>
                                    <th scope="row">Hardest landing</th>
                                    <td>{{ number_format($stats['max_landing_rate']) }} ft/min</td>
                                </tr>
                                <tr>
                                    <th scope="row">Average landing</th>
                                    <td>{{ number_format($stats['avg_landing_rate']) }} ft/min</td>
                                </tr>
                                <tr>
                                    <th scope="row">Flights last month</th>
                                    <td>{{ number_format($stats['last_mth_flight']) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Flights this month</th>
                                    <td>{{ number_format($stats['this_mth_flight']) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col">
                            <table class="table table-borderless">
                                <tr>
                                    <th scope="row">Average flight time</th>
                                    <td>{{ Utils::minutesToTimeString($stats['avg_flight_time']) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Average flight distance</th>
                                    <td>{{ number_format($stats['avg_distance']) }} NM</td>
                                </tr>
                            </table>
                        </div>											
                    </div>
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
        <div class="col-lg-6 mg-t-20 mg-lg-t-0">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    <i class="fa fa-map-marker"></i> My Destination Statistic
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <div class="row">
                        <div class="col">
                            <h6>Top 5 Departure</h6>
                            @if ($stats['top_departure'] == null || count($stats['top_departure']) == 0)
                                <p>No flights flown</p>
                            @else
                                <table class="table table-striped mg-b-0">
                                    <thead>
                                        <tr>
                                            <th>Airport</th>
                                            <th>No of Flight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stats['top_departure'] as $s)
                                            <tr>
                                                <td><a href="{{route('frontend.airports.show', [
                                                    'id' => $s->icao
                                                    ])}}">{{ $s->iata }} / {{ $s->id }}</a>
                                                </td>
                                                <td>{{ number_format($s->count) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        <div class="col">
                            <h6>Top 5 Destination</h6>
                            @if ($stats['top_destination'] == null || count($stats['top_destination']) == 0)
                                <p>No flights flown</p>
                            @else
                                <table class="table table-striped mg-b-0">
                                    <thead>
                                        <tr>
                                            <th>Airport</th>
                                            <th>No of Flight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stats['top_destination'] as $s)
                                            <tr>
                                                <td><a href="{{route('frontend.airports.show', [
                                                    'id' => $s->icao
                                                    ])}}">{{ $s->iata }} / {{ $s->id }}</a>
                                                </td>
                                                <td>{{ number_format($s->count) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div><!-- card-body -->
            </div><!-- card -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        phpvms.map.render_voyage_map({
            center: ['{{ $center[0] }}', '{{ $center[1] }}'],
            zoom: '{{ $zoom }}',
            routes: {!! json_encode($map_features) !!},
        });
    </script>
    <script>
        $(document).ready(function () {
            $("button.save_flight").click(function (e) {
                e.preventDefault();
        
                const btn = $(this);
                const class_name = btn.attr('x-saved-class'); // classname to use is set on the element
        
                let params = {
                    url: '{{ url('/api/user/bids') }}',
                    data: {
                        'flight_id': btn.attr('x-id')
                    }
                };
        
                if (btn.hasClass(class_name)) {
                    params.method = 'DELETE';
                } else {
                    params.method = 'POST';
                }
        
                axios(params).then(response => {
                    console.log('save bid response', response);
        
                    if(params.method === 'DELETE') {
                        console.log('successfully removed flight');
                        btn.removeClass(class_name);
                        alert('Your booking has been cancelled');
                    } else {
                        console.log('successfully saved flight');
                        btn.addClass(class_name);
                        alert('Your booking has been added');
                        window.location.replace('{{ url('/dashboard') }}');
                    }
                })
                .catch(error => {
                    console.error('Error saving bid status', params, error);
                });
            });
        });
    </script>
@endsection