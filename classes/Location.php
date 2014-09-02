<?php

/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 *
 * Deze class is slechts voor 1 enkele record. Voor een list wordt
 * deze niet gebruikt :(.
 */
class Location extends ObjectModel
{

    public $id_location;
    public $location;
    public $country;
    public $code;
    public $date_upd;
    public $date_add;
    public static $definition = array(
        'table' => 'bookflighttickets_location',
        'primary' => 'id_location',
        'multilang' => false,
        'fields' => array(
            'id_location' => array(
                'type' => self::TYPE_INT,
            ),
            'location' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),
            'country' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),
            'code' => array(
                'type' => self::TYPE_STRING,
                'size' => 6,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE
            ),
        ),
    );

}
