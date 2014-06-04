<?php
/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 *
 * Deze class is slechts voor 1 enkele record. Voor een list wordt
 * deze niet gebruikt :(.
 */

//require_once (dirname(__file__) . '/CDbCriteria.php');
//require_once (dirname(__file__) . '/BaseModel.php');


class Route extends ObjectModel
{
	public $id_route;
	public $id_location_1;
	public $id_location_2;
	public $code;
	public $order;
	public $modified;
	public $created;
	
	public static $definition = array(
		'table' => 'booking_route',
		'primary' => 'id_route',
		'multilang' => false,

		'fields' => array(
			'id_route' => array(
				'type' => self::TYPE_INT,
			),
			'id_location_1' => array(
				'type' => self::TYPE_INT,
			),
			'id_location_2' => array(
				'type' => self::TYPE_INT,
			),
			'code' => array(
				'type' => self::TYPE_STRING,
				'size' => 6,
			),
			'order' => array(
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