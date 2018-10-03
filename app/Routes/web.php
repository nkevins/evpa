<?php

/**
 * User doesn't need to be logged in for these
 */
Route::group([
    'namespace' => 'Frontend', 'prefix' => '', 'as' => 'frontend.',
], function () {
    Route::get('/', 'HomeController@indexEpva')->name('home');
});

/*
 * These are only visible to a logged in user
 */
Route::group([
    'namespace'  => 'Frontend', 'prefix' => '', 'as' => 'frontend.',
    'middleware' => ['role:admin|user'],
], function () {
    Route::get('dashboard', 'DashboardController@indexEpva');

    Route::get('r/{id}', 'PirepController@show')->name('pirep.show.public');
    Route::get('p/{id}', 'ProfileController@show')->name('profile.show.public');

    Route::get('users', 'UserController@indexEvpa')->name('users.index');
    Route::get('pilots', 'UserController@index')->name('pilots.index');

    Route::get('livemap', 'AcarsController@index')->name('livemap.index');

    Route::get('airports/{id}', 'AirportController@showEvpa')->name('airports.show');

    // Download a file
    Route::get('downloads', 'DownloadController@index')->name('downloads.index');
    Route::get('downloads/{id}', 'DownloadController@show')->name('downloads.download');

    Route::get('flights/bids', 'FlightController@bidsEvpa')->name('flights.bids');
    Route::get('flights/search', 'FlightController@searchEvpa')->name('flights.search');
    Route::get('flights/routes', 'FlightController@showFlightRoutes');
    Route::resource('flights', 'FlightController');
    Route::get('flights', 'FlightController@indexEvpa')->name('flights.index');

    Route::get('pireps/fares', 'PirepController@fares');
    Route::resource('pireps', 'PirepController');
    Route::get('pireps', 'PirepController@indexEvpa')->name('pireps.index');
    Route::post('pireps/{id}/submit', 'PirepController@submit')->name('pireps.submit');

    Route::get('profile/regen_apikey', 'ProfileController@regen_apikey')
        ->name('profile.regen_apikey');
    Route::resource('profile', 'ProfileController');
    Route::get('profile', 'ProfileController@indexEvpa')->name('profile.index');

    // Customized
    Route::get('promotions', 'UserController@promotions');
    Route::get('last_landings', 'PirepController@lastLandings');
    Route::get('statistics', 'StatisticController@index');
});

Auth::routes(['verify' => true]);
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

require app_path('Routes/admin.php');
