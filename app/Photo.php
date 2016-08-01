<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sabre\Xml\Deserializer;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;

class Photo extends Model implements XmlDeserializable
{
    const SAMPLE_DATA_DEFAULT_NS = 'http://rets.org/xsd/Syndication/2012-03';

    public $timestamps = false;
    public $guarded = ['listing_id'];

    public function listing() {
        return $this->belongsTo(Listings::class);
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
        $photo = new self();

        $keyValues = Deserializer\keyValue($reader, self::SAMPLE_DATA_DEFAULT_NS);

        if (isset($keyValues['MediaModificationTimestamp'])) {
            $keyValues['MediaModificationTimestamp'] = trim($keyValues['MediaModificationTimestamp']);
        }

        // Fix attribute casing
        if (isset($keyValues['MediaURL'])) {
            $keyValues['MediaUrl'] = $keyValues['MediaURL'];
            unset($keyValues['MediaURL']);
        }

        foreach(array_keys($keyValues) as $key) {
            $snakeKey=snake_case($key);
            $photo->$snakeKey = $keyValues[$key];
        }



        return $photo;
    }
}
