<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Flight</th>
            <th>Pilot</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Aircraft</th>
            <th>Submitted</th>
            <th>Aircraft Status</th>
        </tr>
    </thead>
    <tbody>

    @foreach($pireps as $pirep)
        <tr>
            <td>
                {{ $pirep->airline->code }}{{ $pirep->ident }}
            </td>
            <td>{{ $pirep->user->name }}</td>
            <td>
                {{ $pirep->dpt_airport->name }}
                    (<a href="{{route('frontend.airports.show', [
                    'id' => $pirep->dpt_airport->icao
                    ])}}">{{$pirep->dpt_airport->icao}}</a>)
            </td>
            <td>
                {{ $pirep->arr_airport->name }}
                    (<a href="{{route('frontend.airports.show', [
                    'id' => $pirep->arr_airport->icao
                    ])}}">{{$pirep->arr_airport->icao}}</a>)
            </td>
            <td>{{ $pirep->aircraft->icao }} ({{ $pirep->aircraft->registration }})</td>
            <td>
                {{ $pirep->submitted_at->diffForHumans() }}
            </td>
            <td>
                @if ($pirep->landing_rate < -500)
                    <div class="badge badge-danger">Structural Damage</div>
                @else
                    <div class="badge badge-success">Verified</div>
                @endif
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
