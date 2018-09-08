<table class="table table-striped mg-b-0">
    <thead>
        <th>Name</th>
        <th>Crew ID</th>
        <th>Nationality</th>
        <th>UAE Citizen Permit</th>
        <th>Rating</th>
        <th>Airline</th>
        <th>Location</th>
        <th>Flights</th>
        <th>Hours</th>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>
                    <a href="{{ route('frontend.profile.show.public', ['id' => $user->id]) }}">
                        {{ $user->name }}
                    </a>
                </td>
                <td>{{ $user->pilotId }}</td>
                <td>
                    @if(filled($user->country))
                        {{ App\Support\Countries::getSelectList()[$user->country] }} <span class="flag-icon flag-icon-{{ $user->country }}"></span>
                    @endif
                </td>
                <td>{{ $user->getCitizenshipStatus() }}</td>
                <td><img src="{{ $user->rank->image_url }}" title="{{ $user->rank->name }}" style="height:20px;" /></td>
                <td>{{ $user->airline->icao }}</td>
                <td>
                    @if($user->current_airport)
                        {{ $user->curr_airport_id }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $user->flights }}</td>
                <td>{{ \App\Facades\Utils::minutesToTimeString($user->flight_time) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>