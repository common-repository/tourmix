<?php

/**
 * This file contains all the objects which will be used by the Tourmix API
 *
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

class TourmixObject {
    /**
     * Get an array of only the properties that have been set.
     *
     * @return array
     */
    public function getSetProperties() {
        $setProperties = [];

        foreach ($this as $property => $value) {
            if (isset($value)) {
                $setProperties[$property] = $value;
            }
        }

        return $setProperties;
    }
}

class TourmixRecipientObject extends TourmixObject {
    public $name;
    public $phone;
    public $email;
}

class TourmixLocationObject extends TourmixObject {
    public $zip;
    public $city;
    public $street;
    public $number;
    public $other;
}

class TourmixParcelObject extends TourmixObject {
    public $recipient;          // TourmixRecipientObject
    public $start_location;     // TourmixLocationObject
    public $end_location;       // TourmixLocationObject
    public $weight;
    public $size;
    public $cod;
    public $invoice_number;
    public $timewindow;
    public $outer_id;
    public $outer_id_type;
}

class TourmixParcelsObject extends TourmixObject {
    public $parcels = [];

    public function parseJSON() {
        $json = json_encode($this);
        return preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', $json);
    }

    public function getSetProperties() {
        $json = $this->parseJSON();
        return json_decode($json);
    }
}