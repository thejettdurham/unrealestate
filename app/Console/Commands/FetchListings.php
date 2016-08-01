<?php

namespace App\Console\Commands;

use App\Address;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PhotoController;
use App\Listing;
use App\Photo;
use Illuminate\Console\Command;
use Sabre\Xml\Reader;
use Sabre\Xml\Service;
use Sabre\Xml\Deserializer;



class FetchListings extends Command
{
    const SAMPLE_DATA_SOURCE = 'app/sample_listing_data.xml';
    const SAMPLE_DATA_DEFAULT_NS = 'http://rets.org/xsd/Syndication/2012-03';


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listings:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "Fetching new listings..." . PHP_EOL;

        $listingsXml = $this->FetchListingsAsXml();
        $listings = $this->ParseListingsFromXml($listingsXml);

        $this->UpsertListings($listings);
        //TODO: Remove listings in app's database but not in $listings(?)

        echo "Fetching new listings successful";
        exit(0);

    }

    /**
     * Fetches Listing Data from the data source as an XML string
     *
     * @return string
     */
    private function FetchListingsAsXml() : string
    {
        // TODO: Fetch data from the proper API
        return file_get_contents(storage_path(self::SAMPLE_DATA_SOURCE));
    }

    /**
     * Parses out an array of Listing objects from the given source XML string
     *
     * @param string $listingsXml
     * @return array of Listing
     */
    private function ParseListingsFromXml(string $listingsXml) : array
    {
        // Used sabre/xml to parse the XML (http://sabre.io/xml)
        $service = new Service();
        $service->elementMap = [
            '{' . self::SAMPLE_DATA_DEFAULT_NS . '}Listing' => Listing::class,
            '{' . self::SAMPLE_DATA_DEFAULT_NS . '}Listings' => function(Reader $reader) {
                return Deserializer\repeatingElements($reader, '{' . self::SAMPLE_DATA_DEFAULT_NS . '}Listing');
            },
            '{' . self::SAMPLE_DATA_DEFAULT_NS . '}Photo' => Photo::class,
            '{' . self::SAMPLE_DATA_DEFAULT_NS . '}Photos' => function(Reader $reader) {
                return Deserializer\repeatingElements($reader, '{' . self::SAMPLE_DATA_DEFAULT_NS . '}Photo');
            },
            '{' . self::SAMPLE_DATA_DEFAULT_NS . '}Address' => Address::class
        ];

        return $service->parse($listingsXml);
    }

    /**
     * Upserts a given array of Listings and all nested models into the app's database.
     * Only changed records are modified.
     *
     * @param array $listings
     */
    private function UpsertListings(array $listings)
    {
        foreach($listings as $listing) {

            /**
             * Break Address and Photos into their own variables such that Listing can be smoothly inserted into database.
             * The Address and Photos will be inserted on their own through the Listing's relation methods
             */
            if (isset($listing->address)) {
                $address = $listing->address;
                unset($listing->address);
            }

            if (isset($listing->photos)) {
                $photos = $listing->photos;
                unset($listing->photos);
            }

            $listing = ListingController::UpsertListing($listing);

            if (isset($address)) {
                AddressController::UpsertAddressForListing($address, $listing);
            }

            if (isset($photos)) {
                foreach($photos as $photo)
                PhotoController::UpsertPhotoForListing($photo, $listing);
            }
        }
    }

}
