<?php
/**
 * User: patrick
 * Date: 12/18/13.
 */
 
 class BookingInventory extends ObjectModel
 {
    public $id_booking_inventory;
 	public $designation;
 	public $seats;
 	public $modified;
	public $created;

	public static $definition = array(
	    'table' => 'booking_inventory',
	    'primary' => 'id_booking_inventory',
	    'multilang' => false,

	    'fields' => array(
	            'id_booking_inventory' => array(
	                    'type' => self::TYPE_INT,
	                    ),
	            'designation' => array(
	                    'type' => self::TYPE_STRING,
		                'size' =>10,
	                    ),
	            'seats' =>  array(
	                    'type' => self::TYPE_INT,
	                    ),
	            'modified' =>  array(
	                    'type' => self::TYPE_DATE
	                    ),
	            'created' =>  array(
	                    'type' => self::TYPE_DATE
	                    ),
			),
	    );
 }