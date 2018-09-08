<div class="card bd-0">
    <div class="card-header tx-medium bd-0 tx-white bg-danger">
        My Schedule
    </div><!-- card-header -->
    <div class="card-body bd bd-t-0">

        <table class="table table-condensed table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>Flight</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>A/C</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
        
            @foreach($flights as $flight)
                <tr>
                    <td>
                        <img src="{{ $flight->airline->logo }}" style="width:80px;" />
                    </td>
                    <td>
                        {{ $flight->ident }}
                    </td>
                    <td>
                        {{ $flight->dpt_airport->name }}
                        (<a href="{{route('frontend.airports.show', [
                                    'id' => $flight->dpt_airport->icao
                                    ])}}">{{$flight->dpt_airport->icao}}</a>)
                        <br />
                        <small>{{ Carbon\Carbon::parse($flight->dpt_time)->format('H:i') }} Z</small>
                    </td>
                    <td>
                        {{ $flight->arr_airport->name }}
                        (<a href="{{route('frontend.airports.show', [
                                    'id' => $flight->arr_airport->icao
                                    ])}}">{{$flight->arr_airport->icao}}</a>)
                        <br />
                        <small>{{ Carbon\Carbon::parse($flight->arr_time)->format('H:i') }} Z</small>
                    </td>
                    <td>
                        <?php
                            foreach ($flight->subfleets as $sf) {
                                $new_arr[] = $sf->type;
                            }
                        ?>
                        {{ implode(', ', $new_arr) }}
                        <?php 
                            $new_arr = null;
                        ?>
                    </td>
                    <td>
                        <a href="{{ route('frontend.flights.show', [$flight->id]) }}" class="btn btn-primary btn-sm">Briefing</a>
                        
                        <button class="btn btn-sm btn-secondary
                               save_flight"
                        x-id="{{ $flight->id }}"
                        x-saved-class="btn-info"
                        type="button"
                        title="Cancel"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        
            </tbody>
        </table>
        
        <br />
        {{ $flights->links('pagination.default') }}
    </div>
</div>
