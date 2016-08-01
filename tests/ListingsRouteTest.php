<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ListingsRouteTest extends TestCase
{
    /**
     * These aren't very good unit tests as they depend heavily on database state, but they will suffice in ensuring the
     * application works as intended without spending a lot of time setting up mocks.
     */

    public function testListingDataInAllListingsOutput() {
        /**
         * Given I am an API consumer
         * If I visit the listings endpoint
         * I get back a JSON object containing listing data
         */
        $this->get('/listings')->seeJson([
           "list_price" => 154900,
            "listing_url" => "http://listings.listhub.net/pages/BCMLSIA/12777/?channel=passfail",
            "bedrooms" => 3,
            "bathrooms" => 3,
            "property_type" => "Residential"
        ]);
    }

    public function testAddressesInAllListingsOutput()
    {
        /**
         * Given I am an API consumer
         * If I visit the listings endpoint
         * The returned JSON object contains nested address objects for each listing
         */
        $this->get('/listings')->seeJson([
            "full_street_address" => '2251 58 Street'
        ]);
    }

    public function testPhotosInAllListingsOutput() {
        /**
         * Given I am an API consumer
         * If I visit the listings endpoint
         * The returned JSON object contains nested photo objects for each listing
         */
        $this->get('/listings')->seeJson([
            "media_url" => "http://photos.listhub.net/BCMLSIA/12777/3?lm=20160106T175645"
        ]);
    }

    public function testGetSingleListingsWorksCorrectly() {
        /**
         * Given I am an API consumer
         * If I visit the listings endpoint passing a valid id
         * I get a single Listing object back
         */
        $this->get('listings/1')->seeJson([
            "list_price" => 154900
        ]);
    }

    public function testGetSingleListingsFailsWithInvalidId() {
        /**
         * Given I am an API consumer
         * If I visit the listings endpoint passing an invalid id
         * I get a 404 status code
         */
        $this->get('/listings/100')->seeStatusCode(404);
    }

    public function testToggleActivationWorksCorrectly() {
        /**
         * Given I am an API consumer
         * If I visit /listings/:id/toggle_activation
         * Where :id is a valid id
         * I get a 201 status code and the object back as JSON
         * Where the returned object has toggled the state of listing_is_active
         */
        $initialState = $this->get('/listings/1')->decodeResponseJson()['listing_is_active'];

        $response = $this->put('/listings/1/toggle_activation');
        $response->seeStatusCode(200);
        $response->seeJson([
            "listing_is_active" => !$initialState
        ]);
    }

    public function testToggleActivationFailsWithInvalidId() {
        /**
         * Given I am an API consumer
         * If I visit /listings/:id/toggle_activation
         * Where :id is an invalid id
         * I get a 404 status code
         */
        $response = $this->put('/listings/100/toggle_activation')->seeStatusCode(404);
    }
}
