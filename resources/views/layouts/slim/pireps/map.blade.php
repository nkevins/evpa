<div class="row">
    <div class="col-12">
        <div class="box-body">
            <div id="map" style="width: 100%; height: 550px"></div>
        </div>
    </div>
</div>

@section('scripts')
<script type="text/javascript">
phpvms.map.render_route_map({
    route_points: {!! json_encode($map_features['planned_rte_points'])  !!},
    planned_route_line: {!! json_encode($map_features['planned_rte_line']) !!},
    actual_route_line: {!! json_encode($map_features['actual_route_line']) !!},
    actual_route_points: {!! json_encode($map_features['actual_route_points']) !!},
});
</script>
@endsection
