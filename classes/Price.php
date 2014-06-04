<?php
class Price extends ObjectModel
{
    public $id_price;
    public $id_schedule;
    public $valid_until;
    public $price;
    public $modified;
    public $created;
    
    
    public static $definition = array(
        'table' => 'booking_price',
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
            'modified' => array(
                'type' => self::TYPE_DATE
            ),
            'created' => array(
                'type' => self::TYPE_DATE
            ),
        ),
    );
   
}