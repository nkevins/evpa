<div id="map" style="width: {{ $config['width'] }}; height: {{ $config['height'] }}"></div>

@section('scripts')
<script>
phpvms.map.render_voyage_map({
    center: ['{{ $center[0] }}', '{{ $center[1] }}'],
    zoom: '{{ $zoom }}',
    routes: {!! json_encode($map_features) !!},
});
</script>
@endsection
