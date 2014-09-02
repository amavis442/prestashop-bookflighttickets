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
        'table' => 'bookflighttickets_inventory',
        'primary' => 'id_booking_inventory',
        'multilang' => false,
        'fields' => array(
            'id_booking_inventory' => array(
                'type' => self::TYPE_INT,
            ),
            'designation' => array(
                'type' => self::TYPE_STRING,
                'size' => 100,
            ),
            'seats' => array(
                'type' => self::TYPE_INT,
            ),
            'modified' => array(
                'type' => self::TYPE_DATE
            ),
            'created' => array(
                'type' => self::TYPE_DATE
            ),
        ),
    );

    public function add($autodate = true, $null_values = false)
    {
        $this->created = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');

        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->modified = date('Y-m-d H:i:s');
        return parent::update($null_values);
    }

}
