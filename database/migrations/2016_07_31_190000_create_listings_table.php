<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('address_id')->unsigned();
            $table->bigInteger('list_price')->unsigned();
            $table->string('listing_url');
            $table->integer('bedrooms')->unsigned();
            $table->integer('bathrooms')->unsigned();
            $table->string('property_type');
            $table->string('listing_key');
            $table->string('listing_category');
            $table->boolean('listing_is_active');
            $table->boolean('disclose_address')->nullable();
            $table->text('listing_description');
            $table->string('mls_id');
            $table->string('mls_name');
            $table->integer('mls_number')->unsigned();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('listings');
    }
}
