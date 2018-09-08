@extends('app')
@section('title', $airport->full_name)

@section('content')
<div class="row row-sm mg-t-20">
    <div class="col-md-12">
        <div class="card bd-0">
            <div class="card-header tx-medium bd-0 tx-white bg-danger">
                Airport Details
            </div><!-- card-header -->
            <div class="card-body bd bd-t-0">
                <div class="row">
                    <div class="col-12">
                        <h2>{{ $airport->full_name }}</h2>
                    </div>
                </div>
            
                <div class="row">
                    {{-- Show the weather widget in one column --}}
                    <div class="col-5">
                        {{ Widget::Weather([
                            'icao' => $airport->icao,
                          ]) }}
                    </div>
                
                    {{-- Show the airspace map in the other column --}}
                    <div class="col-7">
                        {{ Widget::AirspaceMap([
                            'width' => '100%',
                            'height' => '400px',
                            'lat' => $airport->lat,
                            'lon' => $airport->lon,
                          ]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-sm mg-t-20">
    {{-- There are files uploaded and a user is logged in--}}
    @if(count($airport->files) > 0 && Auth::check())
        <div class="col-12">
            <h3>Downloads</h3>
            @include('downloads.table', ['files' => $airport->files])
        </div>
    @endif
</div>

<div class="row row-sm mg-t-20">
    <div class="col-6">
        <div class="card bd-0">
            <div class="card-header tx-medium bd-0 tx-white bg-danger">
                Inbound Flights
            </div><!-- card-header -->
            <div class="card-body bd bd-t-0">
                @if(!$inbound_flights)
                    <div class="jumbotron text-center">
                        no flights found
                    </div>
                @else
                    <table class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th class="text-left">Ident</th>
                            <th class="text-left">From</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                        </tr>
                        </thead>
                        @foreach($inbound_flights as $flight)
                        <tr>
                            <td class="text-left">
                                <a href="{{ route('frontend.flights.show', [$flight->id]) }}">
                                    {{ $flight->ident }}
                                </a>
                            </td>
                            <td class="text-left">{{ $flight->dpt_airport->name }}
                                (<a href="{{route('frontend.airports.show',
                                 ['id'=>$flight->dpt_airport->icao])}}">{{$flight->dpt_airport->icao}}</a>)
                            </td>
                            <td>{{ Carbon\Carbon::parse($flight->dpt_time)->format('H:i') }} Z</td>
                            <td>{{ Carbon\Carbon::parse($flight->arr_time)->format('H:i') }} Z</td>
                        </tr>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="card bd-0">
            <div class="card-header tx-medium bd-0 tx-white bg-danger">
                Outbound Flights
            </div><!-- card-header -->
            <div class="card-body bd bd-t-0">
                @if(!$outbound_flights)
                    <div class="jumbotron text-center">
                        no flights found
                    </div>
                @else
                    <table class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th class="text-left">Ident</th>
                            <th class="text-left">To</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                        </tr>
                        </thead>
                        @foreach($outbound_flights as $flight)
                            <tr>
                                <td class="text-left">
                                    <a href="{{ route('frontend.flights.show', [$flight->id]) }}">
                                        {{ $flight->ident }}
                                    </a>
                                </td>
                                <td class="text-left">{{ $flight->arr_airport->name }}
                                    (<a href="{{route('frontend.airports.show',
                                 ['id'=>$flight->arr_airport->icao])}}">{{$flight->arr_airport->icao}}</a>)
                                </td>
                                <td>{{ Carbon\Carbon::parse($flight->dpt_time)->format('H:i') }} Z</td>
                                <td>{{ Carbon\Carbon::parse($flight->arr_time)->format('H:i') }} Z</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
