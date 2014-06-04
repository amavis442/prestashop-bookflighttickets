<?php
/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 *
 * Deze class is slechts voor 1 enkele record. Voor een list wordt
 * deze niet gebruikt :(.
 */

//require_once(dirname(__file__) . '/CDbCriteria.php');
//require_once(dirname(__file__) . '/BaseModel.php');

class Location extends ObjectModel
{
	public $id_location;
	public $location;
	public $country;
	public $code;
	public $modified;
	public $created;

	public static $definition = array(
		'table' => 'booking_location',
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