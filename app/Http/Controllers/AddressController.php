<?php

namespace App\Http\Controllers;

use App\Address;
use App\Listing;
use Illuminate\Http\Request;

use App\Http\Requests;

class AddressController extends Controller
{
    //
    public static function UpsertAddressForListing(Address $address, Listing $listing)
    {
        return $listing->address()->firstOrCreate($address->getAttributes());
    }
}
