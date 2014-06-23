<?php
class Price extends ObjectModel
{
    public $id_price;
    public $id_schedule;
    public $valid_until;
    public $price;
    public $date_upd;
    public $date_add;
    
    
    public static $definition = array(
        'table' => 'bookflighttickets_price',
        'primary' => 'id_price',
        'multilang' => false,
    
        'fields' => array(
            'id_price' => array(
                'type' => self::TYPE_INT,
            ),
            'id_schedule' => array(
                'type' => self::TYPE_INT,
            ),
            'price' => array(
                'type' => self::TYPE_STRING,
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