<?php

//namespace booking\models;

/**
 * Description of ProductBooking
 *
 * @author patrick
 */
class ProductBooking extends ObjectModel
{

    //put your code here
    public $id_productbooking;
    public $id_order;
    public $id_cart;
    public $id_product;
    public $checkin_date;
    public $checkout_date;
    public $token;
    public $date_add;
    public $date_upd;
    
    /**
     *
     * @var type 
     */
    public static $definition = array(
        'table' => 'booking_productbooking',
        'primary' => 'id_productbooking',
        'multilang' => false,
        'fields' => array(
            'id_productbooking' => array(
                'type' => self::TYPE_INT,
            ),
            'id_order' => array(
                'type' => self::TYPE_INT,
            ),
            'id_cart' => array(
                'type' => self::TYPE_INT,
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
            ),
            'checkin_date' => array(
                'type' => self::TYPE_DATE
            ),
            'checkout_date' => array(
                'type' => self::TYPE_DATE
            ),
            'token' => array(
                'type' => self::TYPE_STRING
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE
            ),
        ),
    );

}
