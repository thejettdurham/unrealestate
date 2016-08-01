<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;

use App\Http\Requests;

class ListingController extends Controller
{
    public static function UpsertListing(Listing $listing) : Listing {
        return Listing::firstOrCreate($listing->getAttributes());
    }
}
