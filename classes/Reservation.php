<?php

class Reservation extends ObjectModel
{

    public $id_reservation;
    public $id_cart;
    public $id_schedule;
    public $code;
    public $price;
    public $adults;
    public $children;
    public $special;
    public $status;
    public $origin_id;
    public $destination_id;
    public $session_id;
    public $date_upd;
    public $date_add;
    public static $definition = array(
        'table' => 'bookflighttickets_reservation',
        'primary' => 'id_reservation',
        'multilang' => false,
        'fields' => array(
            'id_reservation' => array(
                'type' => self::TYPE_INT,
            ),
            'id_cart' => array(
                'type' => self::TYPE_INT,
            ),
            'id_schedule' => array(
                'type' => self::TYPE_INT,
            ),
            'code' => array(
                'type' => self::TYPE_STRING,
                'size' => 20,
            ),
            'price' => array(
                'type' => self::TYPE_STRING,
                'size' => 5,
            ),
            'adults' => array(
                'type' => self::TYPE_INT,
            ),
            'children' => array(
                'type' => self::TYPE_INT,
            ),
            'special' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),
            'status' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
            ),
            'origin_id' => array(
                'type' => self::TYPE_INT,
            ),
            'destination_id' => array(
                'type' => self::TYPE_INT,
            ),
            'session_id' => array(
                'type' => self::TYPE_STRING,
                'size' => 255,
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
