<table class="table table-striped mg-b-0">
    <thead>
        <th>Name</th>
        <th>Crew ID</th>
        <th>Airline</th>
        <th>Previous Rating</th>
        <th>Promoted Rating</th>
        <th>Promoted Date</th>
        <th>Captaincy Program</th>
    </thead>
    <tbody>
        @foreach($promotions as $promotion)
            <tr>
                <td>
                    <a href="{{ route('frontend.profile.show.public', ['id' => $promotion->user->id]) }}">
                        {{ $promotion->user->name }}
                    </a>
                </td>
                <td>{{ $promotion->user->getCrewID() }}</td>
                <td>{{ $promotion->user->airline->icao }}</td>
                <td>
                    <img src="{{ $promotion->oldRank->image_url }}" title="{{ $promotion->oldRank->name }}" style="height:20px;" />
                </td>
                <td>
                    <img src="{{ $promotion->newRank->image_url }}" title="{{ $promotion->newRank->name }}" style="height:20px;" />
                </td>
                <td>{{ Carbon\Carbon::parse($promotion->created_at)->format('d M y') }}</td>
                <td>
                    @if ($promotion->user->flight_time >= 50 * 60)
                        Completed
                    @elseif ($promotion->user->flight_time >= 30 * 60)
                        Started
                    @else
                        Not Qualified
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>