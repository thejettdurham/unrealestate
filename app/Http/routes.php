<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/listings', 'ListingController@GetAllListings');
Route::get('/listings/{id}', 'ListingController@GetFullListingAtId');
Route::put('/listings/{id}/toggle_activation', "ListingController@ToggleActivationOnListingAtId");