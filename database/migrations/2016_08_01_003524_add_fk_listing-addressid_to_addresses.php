<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkListingAddressidToAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function ($table) {
            $table->foreign('listing_id')->references('id')->on('listings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function ($table) {
            $table->dropForeign('addresses_listing_id_foreign');
        });
    }
}
