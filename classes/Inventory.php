<?php

/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 *
 * Deze class is slechts voor 1 enkele record. Voor een list wordt
 * deze niet gebruikt :(.
 */
class Inventory extends ObjectModel
{

    public $id_inventory;
    public $designation;
    public $seats;
    public $date_upd;
    public $date_add;
    public static $definition = array(
        'table' => 'bookflighttickets_inventory',
        'primary' => 'id_inventory',
        'multilang' => false,
        'fields' => array(
            'id_inventory' => array(
                'type' => self::TYPE_INT,
            ),
            'designation' => array(
                'type' => self::TYPE_STRING,
                'size' => 100,
            ),
            'seats' => array(
                'type' => self::TYPE_INT,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE
            ),
        ),
    );

    /**
     * 
     * @param int $id_inventory
     * @return array
     */
    public static function getDesignation($id_inventory)
    {
        $sql = sprintf('SELECT designation,seats FROM %s WHERE id_inventory = %d', _DB_PREFIX_ . self::$definition['table'], $id_inventory);
        return Db::getInstance()->getRow($sql);
    }

}
