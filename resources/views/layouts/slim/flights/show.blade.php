@extends('app')
@section('title', 'Flight '.$flight->ident)

@section('content')
<div class="row row-sm mg-t-20">
    <div class="col-8">
        <div class="card bd-0">
            <div class="card-header tx-medium bd-0 tx-white bg-danger">
                Schedule Details
            </div><!-- card-header -->
            <div class="card-body bd bd-t-0">
                <h2>{{ $flight->ident }}</h2>
                
                <table class="table table-striped">
                    <tr>
                        <td>Aircraft</td>
                        <td>
                            <?php
                                foreach ($flight->subfleets as $sf) {
                                    $new_arr[] = $sf->name;
                                }
                            ?>
                            {{ implode(', ', $new_arr) }}
                            <?php 
                                $new_arr = null;
                            ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Departure</td>
                        <td>
                            {{ $flight->dpt_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->dpt_airport->icao
                            ])}}">{{$flight->dpt_airport->icao}}</a>)
                            @ {{ Carbon\Carbon::parse($flight->dpt_time)->format('H:i') }} Z
                        </td>
                    </tr>

                    <tr>
                        <td>Arrival</td>
                        <td>
                            {{ $flight->arr_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->arr_airport->icao
                            ])}}">{{$flight->arr_airport->icao}}</a>)
                            @ {{ Carbon\Carbon::parse($flight->arr_time)->format('H:i') }} Z
                        </td>
                    </tr>
                    @if($flight->alt_airport_id)
                    <tr>
                        <td>Alternate Airport</td>
                        <td>
                            {{ $flight->alt_airport->full_name }}
                        </td>
                    </tr>
                    @endif

                    @if($flight->route)
                    <tr>
                        <td>Route</td>
                        <td>{{ $flight->route }}</td>
                    </tr>
                    @endif

                    @if(filled($flight->notes))
                        <tr>
                            <td>Notes</td>
                            <td>{{ $flight->notes }}</td>
                        </tr>
                    @endif
                </table>
                
                <br />
                
                @include('flights.map')
            </div><!-- card-body -->
        </div><!-- card -->
    </div>
    
    <div class="col-4">
        <div class="card bd-0">
            <div class="card-header tx-medium bd-0 tx-white bg-danger">
                {{$flight->dpt_airport_id}} METAR
            </div><!-- card-header -->
            <div class="card-body bd bd-t-0">
                {{ Widget::Weather([
                    'icao' => $flight->dpt_airport_id,
                ]) }}
            </div>
        </div>
        
        <br />
        
        <div class="card bd-0">
            <div class="card-header tx-medium bd-0 tx-white bg-danger">
                {{$flight->arr_airport_id}} METAR
            </div><!-- card-header -->
            <div class="card-body bd bd-t-0">
                {{ Widget::Weather([
                    'icao' => $flight->arr_airport_id,
                ]) }}
            </div>
        </div>
    </div>
</div>
@endsection
