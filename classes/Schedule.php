<?php
/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 *
 * Deze class is slechts voor 1 enkele record. Voor een list wordt
 * deze niet gebruikt :(.
 */

//require_once (dirname(__file__) . '/CDbCriteria.php');
//require_once (dirname(__file__) . '/BaseModel.php');
require_once (dirname(__file__) . '/ScheduleHelper.php');

class Schedule extends ObjectModel
{
	public $id_schedule;
	public $id_inventory;
	public $id_route;
	public $traveltime;
	public $departure;
	public $modified;
	public $created;
	public $price;

	public static $definition = array(
		'table' => 'booking_schedule',
		'primary' => 'id_schedule',
		'multilang' => false,

		'fields' => array(
			'id_schedule' => array(
				'type' => self::TYPE_INT,
			),
			'id_route' => array(
				'type' => self::TYPE_INT,
			),
			'id_inventory' => array(
				'type' => self::TYPE_INT,
			),
			'traveltime' => array(
				'type' => self::TYPE_STRING,
			),
			'departure' => array(
				'type' => self::TYPE_STRING,
				'size' => 19,
			),
			'modified' => array(
				'type' => self::TYPE_DATE
			),
			'created' => array(
				'type' => self::TYPE_DATE
			),
		),
	);
    
	public function __construct($id_schedule=null)
	{
	    
	    if ($id_schedule) {
	        $this->id_schedule = $id_schedule;
	    }
	    parent::__construct($id_schedule);
	}
	
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
	
	public static function getData($id_schedule)
	{
	   $sql = sprintf('SELECT s.*,i.designation,i.seats,r.id_location_1,r.id_location_2 FROM %s s  LEFT JOIN %s r ON (s.id_route = r.id_route)
	                   LEFT JOIN %s i ON (s.id_inventory = i.id_inventory) WHERE s.id_schedule = %d',
	                   _DB_PREFIX_.'booking_schedule',
	                   _DB_PREFIX_.'booking_route',
			           _DB_PREFIX_.'booking_inventory',
			             $id_schedule);
	  $row = Db::getInstance()->getRow($sql);
	  $id_location_1 = $row['id_location_1'];
	  $id_location_2 = $row['id_location_2'];
	  $sql = 'SELECT location FROM '._DB_PREFIX_.'booking_location WHERE id_location = '.$id_location_1;
	  $row['origin'] = Db::getInstance()->getValue($sql);
	  $sql = 'SELECT location FROM '._DB_PREFIX_.'booking_location WHERE id_location = '.$id_location_2;
	  $row['destination'] = Db::getInstance()->getValue($sql);
	   
	  $row['arrival'] = ScheduleHelper::getArrival($row['departure'], $row['traveltime']); 
	  $row['type'] = 'directe vlucht';
	  return $row;
	}

}