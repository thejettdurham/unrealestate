<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class PaginatedListingController extends Controller
{
    const DEFAULT_PAGE=1;
    const DEFAULT_RESULTS_PER_PAGE=1;
    const SORTING_ASCENDING="asc";
    const SORTING_DESCENDING="desc";
    // No input data has a listing_date field...
    const ALLOWED_SORT_FIELDS= [ "list_price"];

    /**
     * Transforms a sorting expression into an ordered associative array specifying sort order
     *
     * @param string $key
     * @return array
     */
    private static function processSortingExpression(string $expression)
    {
        $parsedSortings = array();
        $sortings = explode(",", $expression);
        foreach($sortings as $sorting) {
            if (strpos($sorting, '.') !== false) {
                $splitSorting = explode('.', $sorting);

                if (count($splitSorting) > 2) {
                    return response()->json(["message" => "Bad sorting expression"])->setStatusCode(400);
                }

                $sortField = $splitSorting[0];
                if (!in_array($sortField, self::ALLOWED_SORT_FIELDS)) {
                    return response()->json(["message" => "Invalid sort field '" . $sortField . "' specified"])->setStatusCode(400);
                }

                $sortDirection = $splitSorting[1];
                if ($sortDirection !== self::SORTING_ASCENDING and $sortDirection !== self::SORTING_DESCENDING) {
                    return response()->json(["message" => "Bad sorting direction '" . $sortDirection . "' specified"])->setStatusCode(400);
                }

                $parsedSortings[$sortField] = $sortDirection;
            } else {
                if (!in_array($sorting, self::ALLOWED_SORT_FIELDS)) {
                    return response()->json(["message" => "Invalid sort field '" . $sorting . "' specified"])->setStatusCode(400);
                }

                $parsedSortings[$sorting] = self::SORTING_ASCENDING;
            }
        }

        return $parsedSortings;
    }

    private static function processIndexQueryParams(array $rawQueryParams) {
        $processedQueryParams = array();

        // Process and validate each query parameter
        foreach (array_keys($rawQueryParams) as $key) {
            switch($key) {
                case "page":
                case "results_per_page":
                    $processedQueryParams[$key] = (int) $rawQueryParams[$key];
                    if ($processedQueryParams[$key] <= 0) {
                        return response()->json([ "message" => "Invalid value '" . $rawQueryParams[$key] . "' for '" . $key . "'"])->setStatusCode(400);
                    }
                    break;

                case "sort":
                    $processedSortings = self::processSortingExpression($rawQueryParams[$key]);
                    if (!is_array($processedSortings)) {
                        return $processedSortings;
                    }
                    $processedQueryParams[$key] = $processedSortings;
                    break;

                case "photos_only":
                    if ($rawQueryParams[$key] !== 'true' and  $rawQueryParams[$key] !== 'false') {
                        return response()->json([ "message" => "Invalid value '" . $rawQueryParams[$key] . "' for '" . $key . "'"])->setStatusCode(400);
                    }
                    $processedQueryParams[$key] = (boolean) $rawQueryParams[$key];
                    break;

                default:
                    return response()->json([ "message" => "Unexpected query parameter(s)"])->setStatusCode(400);
                    break;
            }
        }

        return $processedQueryParams;
    }

    public function index(Request $request) {
        $processedQueryParams = self::processIndexQueryParams(Input::all());

        // This will either be a proper array or a response object that needs to be bubbled up
        if (!is_array($processedQueryParams)) {
            return $processedQueryParams;
        }

        $listingsQuery = Listing::with("address", "photos");
        if (isset($processedQueryParams["sort"])) {
            foreach($processedQueryParams["sort"] as $sortField=>$sortDirection) {
                $listingsQuery->orderby($sortField, $sortDirection);
            }
        }

        $page = $processedQueryParams["page"] ?? self::DEFAULT_PAGE;
        $results_per_page = $processedQueryParams["results_per_page"] >> self::DEFAULT_RESULTS_PER_PAGE;

        $listings = $listingsQuery->skip($results_per_page * ($page - 1))->take($results_per_page)->get();

        if (isset($processedQueryParams["photos_only"])) {
            //
        }

        return $listings;
    }
}
