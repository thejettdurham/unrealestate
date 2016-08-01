<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;

use App\Http\Requests;

class ListingController extends Controller
{
    public static function UpsertListing(Listing $listing) : Listing {

        // listing_is_active field is user-mutable, so it should not be used to compare against the DB during upsert
        if (isset($listing->listing_is_active)) {
            $isActive = $listing->listing_is_active;
            unset($listing->listing_is_active);
        }

        $returnedListing = Listing::firstOrNew($listing->getAttributes());

        if (isset($isActive)) {
            $returnedListing->listing_is_active = $isActive;
        }

        $returnedListing->save();

        return $returnedListing;
    }

    public function GetAllListings() {
        return Listing::with('address', 'photos')->get();
    }

    public function GetFullListingAtId($id) {
        $listing = Listing::with('address', 'photos')->find($id);

        if (is_null($listing)) {
            return response("", 404);
        }

        return $listing;
    }

    public function ToggleActivationOnListingAtId($id) {
        $listing = Listing::find($id);

        if (is_null($listing)) {
            return response("", 404);
        }

        if (isset($listing->listing_is_active)) {
            $listing->listing_is_active = !($listing->listing_is_active);
        }

        $listing->save();

        return $this->GetFullListingAtId($listing->id);
    }
}
