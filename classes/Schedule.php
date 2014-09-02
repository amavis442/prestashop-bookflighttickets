<?php

/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 *
 * Deze class is slechts voor 1 enkele record. Voor een list wordt
 * deze niet gebruikt :(.
 */
require_once (dirname(__file__) . '/ScheduleHelper.php');
require_once (dirname(__file__) . '/Location.php');
require_once (dirname(__file__) . '/Route.php');
require_once (dirname(__file__) . '/Inventory.php');

class Schedule extends ObjectModel
{

    public $id_schedule;
    public $id_route;
    public $id_inventory;
    public $traveltime;
    public $departure;
    public $modified;
    public $date_upd;
    public $date_add;
    public static $definition = array(
        'table' => 'bookflighttickets_schedule',
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
     * @param int $id_schedule
     */
    public function __construct($id_schedule = null)
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

    /**
     * 
     * @param int $id_schedule
     * @return string
     */
    public static function getData($id_schedule)
    {
        $sql = sprintf('SELECT s.*,i.designation,i.seats,r.id_location_1,
                                r.id_location_2 FROM %s s  LEFT JOIN %s r ON (s.id_route = r.id_route)
	                   LEFT JOIN %s i ON (s.id_inventory = i.id_inventory) WHERE s.id_schedule = %d', _DB_PREFIX_ . self::$definition['table'], _DB_PREFIX_ . Route::$definition['table'], _DB_PREFIX_ . Inventory::$definition['table'], $id_schedule);

        $row = Db::getInstance()->getRow($sql);
        $id_location_1 = $row['id_location_1'];
        $id_location_2 = $row['id_location_2'];
        $sql = 'SELECT location FROM ' . _DB_PREFIX_ . Location::$definition['table'] . ' WHERE id_location = ' . $id_location_1;
        $row['origin'] = Db::getInstance()->getValue($sql);
        $sql = 'SELECT location FROM ' . _DB_PREFIX_ . Location::$definition['table'] . ' WHERE id_location = ' . $id_location_2;
        $row['destination'] = Db::getInstance()->getValue($sql);

        $row['arrival'] = ScheduleHelper::getArrival($row['departure'], $row['traveltime']);
        $row['type'] = 'directe vlucht';
        return $row;
    }

}
