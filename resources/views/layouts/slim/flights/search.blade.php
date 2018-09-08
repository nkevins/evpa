<div class="card bd-0">
    <div class="card-header tx-medium bd-0 tx-white bg-danger">
        Search Schedule
    </div><!-- card-header -->
    <div class="card-body bd bd-t-0">
        {{ Form::open([
                'route' => 'frontend.flights.search',
                'method' => 'GET'
        ]) }}
        <div class="row mg-b-25">
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-control-label">Departure Airport</label>
                    {{ Form::select('dep_icao', $dep_apts, null , ['class' => 'form-control select2']) }}
                </div>
            </div><!-- col-12 -->
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-control-label">Arrival Airport</label>
                    {{ Form::select('arr_icao', $arr_apts, null , ['class' => 'form-control select2']) }}
                </div>
            </div><!-- col-12 -->
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-control-label">Aircraft</label>
                    {{ Form::select('subfleets', $subfleets, null , ['class' => 'form-control select2']) }}
                </div>
            </div><!-- col-12 -->
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-control-label">Flight Number</label>
                    {{ Form::text('flight_number', null, ['class' => 'form-control']) }}
                </div>
            </div><!-- col-12 -->
            <div class="col-lg-12">
                {{ Form::submit('Search', ['class' => 'btn btn-primary']) }}&nbsp;
                <a href="{{ route('frontend.flights.index') }}" class="btn btn-secondary">Clear</a>
            </div><!-- col-12 -->    
        </div>
        {{ Form::close() }}
    </div><!-- card-body -->
</div><!-- card -->