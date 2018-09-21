<table class="table table-striped table-borderless table-sm">
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
    <tr>
        <th scope="row">Average flight time</th>
        <td>{{ Utils::minutesToTimeString($stats['avg_flight_time']) }}</td>
    </tr>
    <tr>
        <th scope="row">Average flight distance</th>
        <td>{{ number_format($stats['avg_distance']) }} NM</td>
    </tr>
</table>
