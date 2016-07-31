<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchListings extends Command
{
    const SAMPLE_DATA_SOURCE = 'app/sample_listing_data.xml';

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
        echo "Fetching!" . PHP_EOL;

        /**
         * It's unclear whether the fetched listings are new listings or should be added to the application's existing
         * listings. Therefore, to simplify this implementation, I am deleting all the existing listings in the app's
         * database, and replacing them entirely with the fetched listings.
         */

        $this->DeleteSavedListings();

        $listingsXml = $this->FetchListingsAsXml();
        $listings = $this->ParseListingsFromXml($listingsXml);

        $this->AddNewListings($listings);
    }

    /**
     * Deletes all saved listings
     *
     * @return void
     */
    private function DeleteSavedListings()
    {

    }

    /**
     * Fetches Listing Data from the data source as an XML string
     *
     * @return string
     */
    private function FetchListingsAsXml() : string
    {
        // TODO: Fetch data from the proper API
        return file_get_contents(self::SAMPLE_DATA_SOURCE);
    }

    private function ParseListingsFromXml($listingsXml)
    {

    }

}
