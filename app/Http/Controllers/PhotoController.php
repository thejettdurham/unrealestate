<?php

namespace App\Http\Controllers;

use App\Listing;
use App\Photo;
use Illuminate\Http\Request;

use App\Http\Requests;

class PhotoController extends Controller
{
    public static function UpsertPhotoForListing(Photo $photo, Listing $listing)
    {
        return $listing->photos()->firstOrCreate($photo->getAttributes());
    }
}
