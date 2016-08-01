<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Sabre\Xml\Deserializer;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;

class Listing extends Model implements XmlDeserializable
{
    const SAMPLE_DATA_DEFAULT_NS = 'http://rets.org/xsd/Syndication/2012-03';

    public $timestamps = false;
    public $guarded = [];

    public function address() {
        return $this->hasOne(Address::class);
    }

    public function photos() {
        return $this->hasMany(Photo::class);
    }

    /**
     * The deserialize method is called during xml parsing.
     *
     * This method is called statically, this is because in theory this method
     * may be used as a type of constructor, or factory method.
     *
     * Often you want to return an instance of the current class, but you are
     * free to return other data as well.
     *
     * You are responsible for advancing the reader to the next element. Not
     * doing anything will result in a never-ending loop.
     *
     * If you just want to skip parsing for this element altogether, you can
     * just call $reader->next();
     *
     * $reader->parseInnerTree() will parse the entire sub-tree, and advance to
     * the next element.
     *
     * @param Reader $reader
     * @return mixed
     */
    static function xmlDeserialize(Reader $reader)
    {
        $listing = new self();

        $keyValues = Deserializer\keyValue($reader, self::SAMPLE_DATA_DEFAULT_NS);

        // Map ListingStatus from XML API to proper boolean representation in the database
        if (isset($keyValues['ListingStatus'])) {
            $keyValues['ListingIsActive'] = (strcasecmp('active', $keyValues['ListingStatus']) === 0);
            unset($keyValues['ListingStatus']);
        }

        if (isset($keyValues['ListingURL'])) {
            $keyValues['ListingUrl'] = $keyValues['ListingURL'];
            unset($keyValues['ListingURL']);
        }

        foreach(array_keys($keyValues) as $key) {
            $snakeKey = snake_case($key);
            $listing->$snakeKey = $keyValues[$key];
        }

        return $listing;
    }
}
