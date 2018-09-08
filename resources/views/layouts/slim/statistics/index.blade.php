@extends('app')
@section('title', 'Statistic Center')

@section('css')
<style>
    .statistic li {
        margin: 0 0 7px 0;
    }
    .statistic {
        list-style: none;
        margin-left: -30px;
    }
</style>
@endsection

@section('content')
    <div class="slim-pageheader">
        <ol class="breadcrumb slim-breadcrumb">
        </ol>
        <h6 class="slim-pagetitle">Statistic Center</h6>
    </div><!-- slim-pageheader -->
    
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    THE EMIRATES VIRTUAL PILOT ASSOCIATION
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <ul class="statistic">
                        <li>The Emirates Virtual Pilot Association counts <strong>{{ $company_statistic['total_user'] }}</strong> pilots, of which <strong>{{ $company_statistic['active_percentage'] }}%</strong> active. 
                        The timetable of Emirates Virtual Pilot Association counts <strong>{{ $company_statistic['total_flights'] }}</strong> flights.</li>
                        <li>
                            <?php
                            $flight_usage_text = '';
                            foreach ($company_statistic['aircraft_usage'] as $ac) {
                                $flight_usage_text .= '<strong>'.$ac->count.'</strong> flights are flown with the <strong>'.$ac->name.'</strong>, ';
                            }
                            ?>
                            {!! $flight_usage_text !!}
                        </li>
                        @if ($company_statistic['max_distance'])
                        <li>The longest flight (distance) is flight <strong>{{ $company_statistic['max_distance']->getIdentAttribute() }}</strong> 
                            from <strong>{{ $company_statistic['max_distance']->dpt_airport_id }}</strong> 
                            to <strong>{{ $company_statistic['max_distance']->arr_airport_id }}</strong> with a distance of <strong>{{ number_format($company_statistic['max_distance']->distance['nmi']) }}</strong> NM</li>
                        @endif
                        @if ($company_statistic['min_distance'])
                        <li>The shortest flight (distance) is flight <strong>{{ $company_statistic['min_distance']->getIdentAttribute() }}</strong> 
                            from <strong>{{ $company_statistic['min_distance']->dpt_airport_id }}</strong> 
                            to <strong>{{ $company_statistic['min_distance']->arr_airport_id }}</strong> with a distance of <strong>{{ number_format($company_statistic['min_distance']->distance['nmi']) }}</strong> NM</li>
                        @endif
                        @if ($company_statistic['max_duration'])
                        <li>The longest flight (duration) is flight <strong>{{ $company_statistic['max_duration']->getIdentAttribute() }}</strong> 
                            from <strong>{{ $company_statistic['max_duration']->dpt_airport_id }}</strong> 
                            to <strong>{{ $company_statistic['max_duration']->arr_airport_id }}</strong> with a distance of 
                            <strong>{{ (new \App\Support\Units\Time($company_statistic['max_duration']->flight_time)) }}</strong></li>
                        @endif
                        @if ($company_statistic['min_duration'])
                        <li>The shortest flight (duration) is flight <strong>{{ $company_statistic['min_duration']->getIdentAttribute() }}</strong> 
                            from <strong>{{ $company_statistic['min_duration']->dpt_airport_id }}</strong> 
                            to <strong>{{ $company_statistic['min_duration']->arr_airport_id }}</strong> with a distance of 
                            <strong>{{ (new \App\Support\Units\Time($company_statistic['min_duration']->flight_time)) }}</strong></li>
                        @endif
                        <li>The website is made by the <strong>Jetline-virtual developer</strong></li>
                        <li>The last time the statistics were updated was <strong>{{ $company_statistic['last_updated_date']->format('d F y H:i:s') }} Z ({{ $company_statistic['last_updated_date']->diffForHumans() }})</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row row-sm row-sm mg-t-20">
        <div class="col-lg-12">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    THE PILOTS
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <ul class="statistic">
                        @if ($pilot_statistic['max_no_flights'])
                        <li>The pilot with the most number of flights is <strong>{{ $pilot_statistic['max_no_flights']->name }}</strong> 
                            with <strong>{{ number_format($pilot_statistic['max_no_flights']->flights) }}</strong> flights.</li>  
                        @endif
                        @if ($pilot_statistic['min_no_flights'])
                        <li>The pilot with the least number of flights is <strong>{{ $pilot_statistic['min_no_flights']->name }}</strong> 
                            with <strong>{{ number_format($pilot_statistic['min_no_flights']->flights) }}</strong> flights.</li>
                        @endif
                        @if ($pilot_statistic['max_flight_time'])
                        <li>The pilot with the most number of hours is <strong>{{ $pilot_statistic['max_flight_time']->name }}</strong> 
                            with <strong>{{ (new \App\Support\Units\Time($pilot_statistic['max_flight_time']->flight_time)) }}</strong> hours.</li>
                        @endif
                        @if ($pilot_statistic['min_flight_time'])
                        <li>The pilot with the least number of hours is <strong>{{ $pilot_statistic['min_flight_time']->name }}</strong> 
                            with <strong>{{ (new \App\Support\Units\Time($pilot_statistic['min_flight_time']->flight_time)) }}</strong> hours.</li>
                        @endif
                        @if ($pilot_statistic['max_distance'])
                        <li>The pilot with the most flown distance is <strong>{{ $pilot_statistic['max_distance']->name }}</strong> 
                            with <strong>{{ number_format($pilot_statistic['max_distance']->distance) }}</strong> NM  ({{ round($pilot_statistic['max_distance']->distance / 24900, 2) }} times around the world).</li>
                        @endif
                        @if ($pilot_statistic['min_distance'])
                        <li>The pilot with the least flown distance is <strong>{{ $pilot_statistic['min_distance']->name }}</strong> 
                            with <strong>{{ number_format($pilot_statistic['min_distance']->distance) }}</strong> NM ({{ round($pilot_statistic['min_distance']->distance / 24900, 2) }} times around the world).</li>
                        @endif
                        @if ($pilot_statistic['last_pirep'])
                        <li>The last flight has been flown by <strong>{{ $pilot_statistic['last_pirep']->user->name }}</strong> at <strong>{{ $pilot_statistic['last_pirep']->created_at->format('d F y H:m:i') }} Z</strong>.</li>
                        @endif
                        <li>Together all pilots are good for <strong>{{ (new \App\Support\Units\Time($pilot_statistic['all_hours'])) }}</strong> and <strong>{{ round($pilot_statistic['all_distance'] / 24900, 2) }}</strong>
                            times around the world (<strong>{{ number_format($pilot_statistic['all_distance']) }}</strong> NM).</li>
                        <li>On average a flight lasted for <strong>{{ (new \App\Support\Units\Time($pilot_statistic['avg_hours'])) }}</strong> 
                            and the flight had a length of <strong>{{ round($pilot_statistic['avg_distance'], 2) }}</strong> NM.</li>
                        @if ($pilot_statistic['min_td'])
                        <li>The smoothest landing ever has been made by <strong>{{ $pilot_statistic['min_td']->user->name }}</strong> 
                            and was <strong>{{ number_format($pilot_statistic['min_td']->landing_rate) }} ft/m</strong>.</li>
                        @endif
                        @if ($pilot_statistic['max_td'])
                        <li>The roughest landing ever has been made by <strong>{{ $pilot_statistic['max_td']->user->name }}</strong> 
                            and was <strong>{{ number_format($pilot_statistic['max_td']->landing_rate) }} ft/m</strong>.</li>
                        @endif
                        @if ($pilot_statistic['min_last_td'])
                        <li>The smoothest landing of the last 30 days has been made by <strong>{{ $pilot_statistic['min_last_td']->user->name }}</strong> 
                            and was <strong>{{ number_format($pilot_statistic['min_last_td']->landing_rate) }} ft/m</strong>.</li>
                        @endif
                        @if ($pilot_statistic['max_last_td'])
                        <li>The roughest landing of the last 30 days has been made by <strong>{{ $pilot_statistic['max_last_td']->user->name }}</strong> 
                            and was <strong>{{ number_format($pilot_statistic['max_last_td']->landing_rate) }} ft/m</strong>.</li>
                        @endif
                        <li>On average a landing is <strong>{{ round($pilot_statistic['avg_td'], 2) }} ft/m</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row row-sm row-sm mg-t-20">
        <div class="col-lg-12">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    THE ACTIVTY
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Year - Month</th>
                                        <th>Flights</th>
                                        <th>Total Distance</th>
                                        <th>Average</th>
                                        <th>Total Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activity_stats['activity'] as $a)
                                    <tr>
                                        <td>{{ $a->year }} - {{ $a->month }}</td>
                                        <td>
                                            {{ number_format($a->count) }}
                                            @if ($a->count_percentage)
                                                @if ($a->count_percentage > 0)
                                                    (<span style="color:green;">+{{ $a->count_percentage }}%</span>)
                                                @else
                                                    (<span style="color:red;">{{ $a->count_percentage }}%</span>)
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ number_format($a->ttl_distance) }} NM
                                            @if ($a->ttl_distance_percentage)
                                                @if ($a->ttl_distance_percentage > 0)
                                                    (<span style="color:green;">+{{ $a->ttl_distance_percentage }}%</span>)
                                                @else
                                                    (<span style="color:red;">{{ $a->ttl_distance_percentage }}%</span>)
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ round($a->avg_distance, 2) }} NM
                                            @if ($a->avg_distance_percentage)
                                                @if ($a->avg_distance_percentage > 0)
                                                    (<span style="color:green;">+{{ $a->avg_distance_percentage }}%</span>)
                                                @else
                                                    (<span style="color:red;">{{ $a->avg_distance_percentage }}%</span>)
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ (new \App\Support\Units\Time($a->ttl_time)) }}
                                            @if ($a->ttl_time_percentage)
                                                @if ($a->ttl_time_percentage > 0)
                                                    (<span style="color:green;">+{{ $a->ttl_time_percentage }}%</span>)
                                                @else
                                                    (<span style="color:red;">{{ $a->ttl_time_percentage }}%</span>)
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mg-t-20">
                        <div class="col-md-12">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Year - Month</th>
                                        <th>Landings</th>
                                        <th>Smoothest Landing</th>
                                        <th>Roughest Landing</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activity_stats['td'] as $a)
                                    <tr>
                                        <td>{{ $a->year }} - {{ $a->month }}</td>
                                        <td>
                                            {{ number_format($a->count) }}
                                            @if ($a->count_percentage)
                                                @if ($a->count_percentage > 0)
                                                    (<span style="color:green;">+{{ $a->count_percentage }}%</span>)
                                                @else
                                                    (<span style="color:red;">{{ $a->count_percentage }}%</span>)
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ number_format($a->max_td) }} ft/m ({{ $a->max_td_name }})</td>
                                        <td>{{ number_format($a->min_td) }} ft/m ({{ $a->min_td_name }})</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row row-sm row-sm mg-t-20">
        <div class="col-lg-12">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    THE AIRPORT
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Top 10 Departure Airports</strong></p>
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Airport</th>
                                        <th>Numbers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($airport_stats['top_departure'] as $a)
                                    <tr>
                                        <td>{{ $a->name }} ({{ $a->dpt_airport_id }})</td>
                                        <td>{{ number_format($a->count) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Top 10 Destination Airports</strong></p>
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Airport</th>
                                        <th>Numbers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($airport_stats['top_destination'] as $a)
                                    <tr>
                                        <td>{{ $a->name }} ({{ $a->arr_airport_id }})</td>
                                        <td>{{ number_format($a->count) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mg-t-10">
                        <div class="col-md-12">
                            <p><strong>Top 10 Routes</strong></p>
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Numbers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($airport_stats['top_route'] as $r)
                                    <tr>
                                        <td>{{ $r->dpt_name }} ({{ $r->dpt_icao }})</td>
                                        <td>{{ $r->arr_name }} ({{ $r->arr_icao }})</td>
                                        <td>{{ number_format($r->count) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection