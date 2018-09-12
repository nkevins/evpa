<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="airline" class="form-control-label">Airline</label>
            {{ Form::select('airline', $airlines, null, ['class' => 'form-control', 'id' => 'airline']) }}
        </div>
    </div>
</div>
<div class="row mg-t-10">
    <div class="col-md-12" id="mapWrapper">
        <div id="map" style="width: {{ $config['width'] }}; height: {{ $config['height'] }}"></div>
    </div>
</div>

@section('scripts')
<script>
phpvms.map.render_schedule_map({
    center: ['{{ $center[0] }}', '{{ $center[1] }}'],
    zoom: '{{ $zoom }}',
    selected_airport: '{{ $config["selected_airport"] }}',
});
</script>
@endsection
