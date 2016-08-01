<?php

namespace App\Console\Commands;

use App\Address;
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

        try {
            /**
             * It's unclear whether the fetched listings are new listings or should be added to the application's existing
             * listings. Therefore, to simplify this implementation, I am deleting all the existing listings in the app's
             * database, and replacing them entirely with the fetched listings.
             */
            $this->DeleteSavedListings();

            $listingsXml = $this->FetchListingsAsXml();
            $listings = $this->ParseListingsFromXml($listingsXml);

            $this->AddNewListings($listings);

            echo "Fetching new listings successful";
            exit(0);

        } catch (\Throwable $t) {
            echo "Encountered fatal error when fetching new listings" . PHP_EOL;
            print_r($t);
            exit(1);
        }
    }

    /**
     * Deletes all saved listings
     *
     * @return void
     */
    private function DeleteSavedListings(){

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

    // todo: make private
    public static function ParseListingsFromXml($listingsXml) : array
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

}
