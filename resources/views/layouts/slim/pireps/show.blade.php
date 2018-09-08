@extends('app')
@section('title', 'PIREP '.$pirep->airline->code.$pirep->ident)

@section('content')
    <div class="row row-sm mg-t-20">
       <div class="col-6">
           <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    PIREP Details
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                    <div class="row">
                        <div class="col-12">
                            <p style="text-align:center;"><img src="{{ $pirep->airline->logo }}" style="width: 120px;" /></p>
                            <p>
                                <h2 style="margin-bottom: 5px; text-align:center;">{{$pirep->airline->code}}{{ $pirep->ident }}</h2>
                                <p style="text-align:center;">Arrived {{$pirep->created_at->diffForHumans()}}</p>
                            </p>
                            
                            <table class="table">
                                <tr>
                                    <th scope="row">PIC</th>
                                    <td>{{ $pirep->user->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Departure Airport</th>
                                    <td>
                                        {{ $pirep->dpt_airport->name }} (<a href="{{route('frontend.airports.show', [
                                                                            'id' => $pirep->dpt_airport->icao
                                                                            ])}}">{{$pirep->dpt_airport->icao}}</a>)
                                        @if($pirep->block_off_time)
                                            <br />
                                            {{ Carbon\Carbon::parse($pirep->block_off_time)->format('d F y - H:i:s') }} Z
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Arrival Airport</th>
                                    <td>
                                        {{ $pirep->arr_airport->name }} (<a href="{{route('frontend.airports.show', [
                                                                            'id' => $pirep->arr_airport->icao
                                                                            ])}}">{{$pirep->arr_airport->icao}}</a>)
                                        @if($pirep->block_on_time)
                                            <br />
                                            {{ Carbon\Carbon::parse($pirep->block_on_time)->format('d F y - H:i:s') }} Z
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Aircraft</th>
                                    <td>{{ $pirep->aircraft->icao }} ({{ $pirep->aircraft->registration }})</td>
                                </tr>
                                <tr>
                                    <th scope="row">Flight Time</th>
                                    <td>{{ Utils::minutesToTimeString($pirep->flight_time) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Touch Down Rate</th>
                                    <td>{{ number_format($pirep->landing_rate) }} ft/min</td>
                                </tr>
                                <tr>
                                    <th scope="row">Date Submitted</th>
                                    <td>{{ Carbon\Carbon::parse($pirep->created_at)->format('d F y') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>
                                        @php
                                        if($pirep->state === PirepState::PENDING)
                                            $badge = 'warning';
                                        elseif ($pirep->state === PirepState::ACCEPTED)
                                            $badge = 'success';
                                        elseif ($pirep->state === PirepState::REJECTED)
                                            $badge = 'danger';
                                        else
                                            $badge = 'info';
                                        @endphp
                                        <div class="badge badge-{{$badge}}">
                                            {{ PirepState::label($pirep->state) }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Flight Type</th>
                                    <td>{{ \App\Models\Enums\FlightType::label($pirep->flight_type) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Route</th>
                                    <td>{{ $pirep->route }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">ZFW</th>
                                    <td>{{ number_format($pirep->zfw) }} Kg</td>
                                </tr>
                                <tr>
                                    <th scope="row">Block Fuel</th>
                                    <td>{{ number_format($pirep->block_fuel) }} Kg</td>
                                </tr>
                                <tr>
                                    <th scope="row">Fuel Used</th>
                                    <td>{{ number_format($pirep->fuel_used['kg']) }} Kg</td>
                                </tr>
                                <tr>
                                    <th scope="row">Notes</th>
                                    <td>{{ $pirep->notes }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
               </div>
            </div>
       </div>
       
       <div class="col-6">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    Route Map
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0">
                   @include('pireps.map')
               </div>
            </div>
       </div>
    </div>
    
    @if(count($pirep->acars_logs) > 0)
    <div class="row row-sm mg-t-20">
        <div class="col-12">
            <div class="card bd-0">
                <div class="card-header tx-medium bd-0 tx-white bg-danger">
                    Flight Log
                </div><!-- card-header -->
                <div class="card-body bd bd-t-0" style="height:400px; overflow:auto;">
                    <table class="table table-hover table-condensed" id="users-table">
                        <tbody>
                        @foreach($pirep->acars_logs as $log)
                            <tr>
                                <td nowrap="true">{{ Carbon\Carbon::parse($log->created_at)->format('d M y H:i:s') }} Z</td>
                                <td>{{ $log->log }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

