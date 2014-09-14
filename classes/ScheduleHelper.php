<?php
require_once (dirname(__file__) . '/Inventory.php');
require_once (dirname(__file__) . '/ScheduleProduct.php');

class ScheduleHelper
{
    private $schedule;
    private $routes = array();
    private $date;
    private $rsplink;
    
    private $table;

    /**
     * 
     * @param DateTime $date
     * @param array $routes
     */
    public function __construct(DateTime $date, Array $routes)
    {
        $this->table = _DB_PREFIX_.'bookflighttickets_schedule';
        
        $this->routes = $routes;
        $this->schedule = $schedule;
        $this->date = $date;
        //$this->rsplink = $rsplink;
    }

    /**
     * 
     * @param DateTime $date
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * 
     * @param array $routes
     */
    public function setRoutes(Array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * 
     * @param string $where
     * @return array
     */
    public function findSchedule($where)
    {
        $sql = 'select * from '.$this->table.' WHERE '.$where;
        $records = Db::getInstance()->executeS($sql);
        return $records;
    }
    
    /**
     * 
     * @param int $id_route
     * @return array
     */
    public function findLocations($id_route)
    {
        $sql = 'SELECT id_location,location,country,code FROM (
                (
                    SELECT id_location,location,country,l.code FROM '._DB_PREFIX_.'bookflighttickets_location l
                    INNER JOIN '._DB_PREFIX_.'bookflighttickets_route ON (id_location_1 = id_location)
                    WHERE id_route = '.$id_route.'
                )    
            UNION ALL
                (
                    SELECT id_location,location,country,l.code FROM '._DB_PREFIX_.'bookflighttickets_location l
                    INNER JOIN '._DB_PREFIX_.'bookflighttickets_route ON (id_location_2 = id_location)
                    WHERE id_route = '.$id_route.'
                ) 
            ) as t';
        return Db::getInstance()->executeS($sql);
    }
    
    /**
     *  Hier worden de vluchten bepaald die in aanmerking komen 
     *
     * @param type $spread
     * @return boolean
     */
    public function getSchedule($spread=2)
    {
        $schedule_data = array();

        $n = 0;
        $r = 0;
        foreach ($this->routes as $k=>$route) {
            foreach ($route as $subroute_id) {
                $sql = sprintf('id_route=%1$d AND
								(departure>=NOW() AND departure >= DATE_SUB("%2$s", INTERVAL %3$d DAY) AND DATE(departure) <= DATE_ADD("%2$s", INTERVAL %3$d DAY))',
                    $subroute_id,
                    $this->date->format('Y-m-d 00:00:00'),
                    $spread
                );
                
                $records = $this->findSchedule($sql);


                if ($records != null && count($records) > 0 ) {
                    /* Check if there are still seats left
                     * Here rules should be applied like seats = seats*1.1 for the no shows etc.
                    * TODO: Rules for this schedule
                    */
                    foreach ($records as $k=>$record) {
                        $v = $record;
                        /* De rest er maar bijzoeken */
                        $v['locations'] = $this->findLocations($v['id_route']);
                    
                        //$seats = $v->inventory->seats;
                        //$num_reservations = $this->rsplink->numRecordsForSchedule($v->id);
                        //if ($seats > $num_reservations) {
                            $schedule_data[$r][$n++] = $v;
                        // } else {
                        //    unset($schedule_data[$r]);
                            //break;
                        //}
                    }
                }
            }
            $r++;
        }
        //var_dump($schedule_data);
        //die();
        
        if ($schedule_data) {
            return $schedule_data;
        } else {
            return false;
        }
    }
    
    /**
     * 
     * @param string $departure
     * @param string $traveltime
     * @return string
     */
    public static function getArrival($departure,$traveltime)
    {
        $travel_h = substr($traveltime,0,2);
        $travel_m = substr($traveltime,3,2);
        
        $arrival = date('Y-m-d H:i:s',strtotime($departure) + $travel_h*60*60 + $travel_m*60);
        return $arrival;
    }
    
    /**
     * 
     * @param array $to
     * @param array $back
     * @param type $num_persons
     * @return string
     */
    public static function makeCombinations(Array $to, Array $back,$num_persons = 1)
    {
        $schedules = array();
        $n = 0;
        foreach ($to as $k=>$toF) {
            foreach ($toF as $k=>$tFlight) {
                foreach ($back as $backF) {
                    foreach ($backF as $bFlight) {
                        unset($id_schedule);
                        unset($id_product);
                        /* to */
                        $schedules[$n]['to'] = $tFlight;
                        $id_schedule = $tFlight['id_schedule'];
                        $id_product = ScheduleProduct::findProductIdByScheduleId($id_schedule);
                        if ($id_product) {
                            $product = new Product($id_product);
                            $schedules[$n]['to']['id_product'] = $id_product;
                            $price = $product->getPrice(false);
                            $schedules[$n]['to']['price'] = ($price ? $price : 0);
                        }
                        $schedules[$n]['to']['arrival'] = ScheduleHelper::getArrival($tFlight['departure'], $tFlight['traveltime']);
                        $schedules[$n]['to']['inventory'] = Inventory::getDesignation($tFlight['id_inventory']);
                        
                        
                        unset($id_schedule);
                        unset($id_product);
                        /* Back */
                        $schedules[$n]['back'] = $bFlight;
                        $id_schedule = $bFlight['id_schedule'];
                        $id_product = ScheduleProduct::findProductIdByScheduleId($id_schedule);
                        if ($id_product) {
                            $product = new Product($id_product);
                            $schedules[$n]['back']['id_product'] = $id_product;
                            $schedules[$n]['back']['price'] = $product->getPrice(false);
                        }
                        $schedules[$n]['back']['arrival'] = ScheduleHelper::getArrival($bFlight['departure'], $bFlight['traveltime']);
                        $schedules[$n]['back']['inventory'] = Inventory::getDesignation($bFlight['id_inventory']);
                        
                        $schedules[$n]['totalprice'] = $num_persons * ($schedules[$n]['back']['price'] + $schedules[$n]['to']['price']);
                        $schedules[$n]['type'] = 'directe vlucht';
                        
                        $n++;
                    }
                }
            }
        }
        return $schedules;
    }
}