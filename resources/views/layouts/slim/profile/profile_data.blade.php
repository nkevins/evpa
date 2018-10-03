<div class="profile-photo-container">
    @if (Auth::user()->avatar != '')
        <img src="{{ Auth::user()->avatar->url }}" alt="" />
    @else
        <img src="{{ public_asset('/assets/slim/img/default_avatar.jpg') }}" alt="" />
    @endif
</div>
<table class="table table-striped table-borderless table-sm">
    <tr>
        <th scope="row">Name</th>
        <td>{{ $user->name }}</td>
    </tr>
    @if(Auth::check() && $user->id === Auth::user()->id)
    <tr>
        <th scope="row">Email</th>
        <td>{{ $user->email }}</td>
    </tr>
    @endif
    <tr>
        <th scope="row">Nationality</th>
        <td>
            @if(filled($user->country))
                {{ App\Support\Countries::getSelectList()[$user->country] }} <span class="flag-icon flag-icon-{{ $user->country }}"></span>
            @endif
        </td>
    </tr>
    <tr>
        <th scope="row">Current Airline</th>
        <td>{{ $user->airline->name }}</td>
    </tr>
    <tr>
        <th scope="row">Current Rating</th>
        <td><img src="{{ $user->rank->image_url }}" title="{{ $user->rank->name }}" style="height:20px;" /></td>
    </tr>
    <tr>
        <th scope="row">Home Airport</th>
        <td>{{ $user->home_airport->name }}</td>
    </tr>
    <tr>
        <th scope="row">Contract</th>
        <td>{{ Carbon\Carbon::parse($user->created_at)->format('d M y') }} - Open Ended</td>
    </tr>
    <tr>
        <th scope="row">ICAO ATPL</th>
        <td>
            @if ($user->state == UserState::ACTIVE)
                <span class="badge badge-success">Valid</span>
            @else
                <span class="badge badge-danger">Frozen</span>
            @endif
        </td>
    </tr>
    <tr>
        <th scope="row">Current Position</th>
        <td>
            @if($user->current_airport)
            <a href="{{route('frontend.airports.show', [
                                'id' => $user->curr_airport_id
                                ])}}">{{ $user->curr_airport_id }}</a>
            @else
            <a href="{{route('frontend.airports.show', [
                                'id' => $user->home_airport_id
                                ])}}">{{ $user->home_airport_id }}</a>
            @endif
        </td>
    </tr>
    <tr>
        <th scope="row">Flight Hour</th>
        <td>{{ \App\Facades\Utils::minutesToTimeString($user->flight_time, false)}}</td>
    </tr>
    <tr>
        <th scope="row">Total Flight</th>
        <td>{{ $user->flights }}</td>
    </tr>
    <tr>
        <th scope="row">UAE Citizen Permit</th>
        <td>{{ $user->getCitizenshipStatus() }}</td>
    </tr>
    <tr>
        <th scope="row">Monthly Salary</th>
        <td>AED {{ number_format($user->rank->acars_base_pay_rate, 2) }}</td>
    </tr>
</table>

@if(Auth::check() && $user->id === Auth::user()->id)
<div class="clearfix" style="height: 50px;"></div>
<div class="row">
    <div class="col-sm-12">
        <div class="text-right">
            <a href="{{ route('frontend.profile.regen_apikey') }}" class="btn btn-warning"
               onclick="return confirm({{ __('Are you sure? This will reset your API key.') }})">@lang('profile.newapikey')</a>
            &nbsp;
            <a href="{{ route('frontend.profile.edit', ['id' => $user->id]) }}"
               class="btn btn-primary">@lang('common.edit')</a>
        </div>

        <h3 class="description">@lang('profile.yourprofile')</h3>
        <table class="table table-borderless table-sm">
            <tr>
                <th scope="row">@lang('common.email')</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th scope="row">@lang('profile.apikey')&nbsp;&nbsp;<span class="description">(@lang('profile.dontshare'))</span></th>
                <td>{{ $user->api_key }}</td>
            </tr>
            <tr>
                <th scope="row">@lang('common.timezone')</th>
                <td>{{ $user->timezone }}</td>
            </tr>
            <tr>
                <th scope="row">@lang('profile.opt-in')</td>
                <td>{{ $user->opt_in ? __('common.yes') : __('common.no') }}</td>
            </tr>
        </table>
    </div>
</div>
@endif