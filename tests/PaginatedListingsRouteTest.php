<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaginatedListingsRouteTest extends TestCase
{
    /**
     * These aren't very good unit tests as they depend heavily on database state, but they will suffice in ensuring the
     * application works as intended without spending a lot of time setting up mocks.
     */

    public function testGetPaginatedListingsWithNoParametersWorksAsExpected() {
        /**
         * Given I am an API consumer
         * If I visit /paginated_listings (passing no query params)
         * I get an array containing a single listing in JSON format
         * And status code is 206
         */
        $response = $this->get('/paginated_listings');

        $responseJson = $response->decodeResponseJson();
        assertEquals(count($responseJson), 1);
        $response->seeStatusCode(206);
    }

    public function testGetPaginatedListingsWithValidPaginationWorksAsExpected() {
        /**
         * Given I am an API consumer
         * If I visit /paginated_listings?page=1&results_per_page=3
         * I get an array containing 3 listings in JSON format
         * And status code is 206
         */
        $response = $this->get('/paginated_listings?page=1&results_per_page=3');

        $responseJson = $response->decodeResponseJson();
        assertEquals(count($responseJson), 3);
        $response->seeStatusCode(206);
    }

    public function testGetPaginatedListingsWithValidPaginationAndSortingWorks() {
        /**
         * Given I am an API consumer
         * If I visit /paginated_listings?page=1&results_per_page=5&sort=list_price.asc
         * I get an array containing 5 listings in JSON format
         * And they are sorted by list_price ascending, then listing_date descending
         * And status code is 206
         */
        $response = $this->get('/paginated_listings?page=1&results_per_page=5&sort=list_price.asc');

        $responseJson = $response->decodeResponseJson();
        assertEquals(count($responseJson), 5);
        assertEquals($responseJson[0]['list_price'], 125000);
        $response->seeStatusCode(206);
    }

    public function testGetPaginatedListingsWithValidPaginationAndPhotosWorks() {
        /**
         * Given I am an API consumer
         * If I visit /paginated_listings?page=1&results_per_page=3&photos_only=true
         * I get an array containing 3 listing in JSON format
         * And status code is 206
         */
        $response = $this->get('/paginated_listings?page=1&results_per_page=3&photos_only=true');

        $responseJson = $response->decodeResponseJson();
        assertEquals(count($responseJson), 1);
        $response->seeStatusCode(206);
    }
}
