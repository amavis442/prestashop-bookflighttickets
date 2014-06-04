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
	public $modified;
	public $created;


	public static $definition = array(
		'table' => 'booking_inventory',
		'primary' => 'id_inventory',
		'multilang' => false,

		'fields' => array(
			'id_inventory' => array(
				'type' => self::TYPE_INT,
			),
			'designation' => array(
				'type' => self::TYPE_STRING,
				'size' =>100,
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

	public static function getDesignation($id_inventory)
	{
	    $sql = sprintf('SELECT designation,seats FROM %s WHERE id_inventory = %d',_DB_PREFIX_.'booking_inventory',$id_inventory);
	    return Db::getInstance()->getRow($sql);
	}
}