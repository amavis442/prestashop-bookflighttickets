<?php
/**
 * @since 1.5.0
 */
if (!defined('_PS_VERSION_'))
        exit;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../classes/MyGender.php');

class bookingResumeModuleFrontController extends ModuleFrontController
{
	public $ajax_search;
	public $instant_search;
	public $max_rows = 10;

	public $flighttype;
	public $fl_l1;
	public $fl_l2;
	
	public $flightfrom;
	public $flightto;
	public $flightdeparturedate;
	public $mysql_departuredate;
	public $flightreturndate;
	public $mysql_returndate;
	public $flightspread;
	public $flightnumadults;
	public $flightnumchildren;
	public $flightnumbaby;
	
	
	public function __construct()
	{
		parent::__construct();


		$this->context = Context::getContext();
	}

	public function init()
	{
		parent::init();
		
		$this->context->cookie->flightfrom = Tools::getValue('flightfrom');
		$this->context->cookie->flightto = Tools::getValue('flightto');
		$this->context->cookie->flighttype = Tools::getValue('flighttype');
		$this->context->cookie->flightdeparturedate = Tools::getValue('flightdeparturedate');
		$this->context->cookie->flightreturndate = Tools::getValue('flightreturndate');
		$this->context->cookie->flightspread = Tools::getValue('flightspread');
		$this->context->cookie->flightnumadults = Tools::getValue('flightnumadults');
		$this->context->cookie->flightnumchildren = Tools::getValue('flightnumchildren');
		$this->context->cookie->flightnumbaby = Tools::getValue('flightnumbaby');
		$this->context->cookie->fl_l1 = (int)Tools::getValue('fl_l1');
		$this->context->cookie->fl_l2 = (int)Tools::getValue('fl_l2');
		
		
		$this->instant_search = Tools::getValue('instantSearch');
		$this->ajax_search = Tools::getValue('ajaxSearch');
		
		if ($this->instant_search || $this->ajax_search)
		{
			/* Alleen de content */
			$this->display_header = false;
			$this->display_footer = false;
		}
		$this->max_rows = Tools::getValue('maxRows');
		if ($this->max_rows > 20) {
			$this->max_rows = 10;
		}
	}
	
	public function setMedia()
	{
	    parent::setMedia();
	
        }
	
	public function initContent()
	{
		parent::initContent();

		$query = Tools::replaceAccentedChars(urldecode(Tools::getValue('q')));
		if ($this->ajax_search) {
			$sql = sprintf('SELECT id_location as id,location,country,code FROM %1$s WHERE (location LIKE \'%2$s%%\' or Country LIKE \'%2$s%%\' OR Code Like \'%2$s%%\') LIMIT %3$d', _DB_PREFIX_.'booking_location', $query,$this->max_rows);
			$result = Db::getInstance()->executeS($sql);
			$searchResults = $result;
			die(Tools::jsonEncode($searchResults));
		}

		
		$this->setTemplate('content.tpl');
		
		$this->flightfrom = Tools::getValue('flightfrom');
		$this->flightto = Tools::getValue('flightto');
		$this->flighttype = Tools::getValue('flighttype');
		$this->flightdeparturedate = Tools::getValue('flightdeparturedate');
		$this->flightreturndate = Tools::getValue('flightreturndate');
		$this->flightspread = Tools::getValue('flightspread');
		$this->flightnumadults = Tools::getValue('flightnumadults');
		$this->flightnumchildren = Tools::getValue('flightnumchildren');
		$this->flightnumbaby = Tools::getValue('flightnumbaby');
		
		
		
		if (preg_match('/(\d{2})(\.|\/|\-)(\d{2})(\.|\/|\-)(\d{4})/',$this->flightdeparturedate,$match)){
		    $this->mysql_departuredate = $match[5].'-'.$match[3].'-'.$match[1];
		    $this->mysql_returndate = preg_replace('/(\d{2})(\.|\/|\-)(\d{2})(\.|\/|\-)(\d{4})/','$5-$3-$1',$this->flightreturndate);
		}
		if (preg_match('/(\d{4})(\.|\/|\-)(\d{2})(\.|\/|\-)(\d{4})/',$this->flightdeparturedate,$match)){
		    $this->mysql_departuredate = $match[1].'-'.$match[3].'-'.$match[5];
		    $this->mysql_returndate = preg_replace('/(\d{4})(\.|\/|\-)(\d{2})(\.|\/|\-)(\d{2})/','$1-$3-$5',$this->flightreturndate);
		}
		
		$this->context->smarty->assign(array('heen'=>$this->mysql_departuredate,
		                                      'terug'=>$this->mysql_returndate,
		                                  'origin'=>$this->flightfrom,
		                                  'destination'=>$this->flightto));
		
		$routeHelper = new RouteHelper();
		$routeHelper->setOrigin((int)Tools::getValue('fl_l1'));
		$routeHelper->setDestination((int)Tools::getValue('fl_l2'));
		$routes = $routeHelper->getRoutes();
		
		$foundSchedule = false;
		if (count($routes) > 0) {
		    $dtDeparture = new DateTime($this->mysql_departuredate.'T00:00:00');
		    $dtReturn = new DateTime($this->mysql_returndate.'T00:00:00');
		    $scheduleHelper_departure = new ScheduleHelper($dtDeparture, $routes);
		    $schedules_depart = $scheduleHelper_departure->getSchedule(3);
		    
		    if ($this->flighttype == 1) {
		      $routeHelper->setOrigin((int)Tools::getValue('fl_l2'));
		      $routeHelper->setDestination((int)Tools::getValue('fl_l1'));
		      $routes = $routeHelper->getRoutes();
		        
		      $scheduleHelper_return = new ScheduleHelper($dtReturn, $routes);
		      $schedules_return = $scheduleHelper_return->getSchedule(3);
		      
		      if ($schedules_return && count($schedules_return)) {
		          foreach ($schedules_return as $k => $item) {
		              foreach ($item as $i=>$schedule) {
		                  $id_schedule = $schedule['id_schedule'];
		                  /* Welk product ghoort daarbij */
		                  $id_product = Product::findProductIdByScheduleId($id_schedule);
		                  if ($id_product) {
		                      $schedules_return[$k][$i]['id_product'] = $id_product;
		                      $schedules_return[$k][$i]['price'] = Product::getPrice(false,$id_product);
		                  }
		              }
		          }
		          $this->context->smarty->assign('returns',$schedules_return);
		          $foundSchedule = true;
		      }
		    } else {
		        $this->context->smarty->assign('returns',null);
		    }
		    
		    if ($schedules_depart && count($schedules_depart)) {
		        foreach ($schedules_depart as $k=>$item) {
		            foreach ($item as $i=>$schedule) {
		                  $id_schedule = $schedule['id_schedule'];
		                  $id_product = Product::findProductIdByScheduleId($id_schedule);
		                  $product = new Product($id_product);
		                  if ($id_product) {
		                      $schedules_depart[$k][$i]['id_product'] = $id_product;
		                      $schedules_depart[$k][$i]['price'] = $product->getPrice(false);
		                  }
		                  unset($product);
		              }
		        }
		        
		        $this->context->smarty->assign('departures',$schedules_depart);
		        
		        $foundSchedule = true;
		    } else {
		        $this->context->smarty->assign('departures','null');
		    }
		    
		    $this->context->smarty->assign('booking_tpl_dir',dirname(__FILE__).'/../../views/templates/front');
		    $this->context->smarty->assign('flighttype',$this->flighttype);
		}
		$this->context->smarty->assign('foundSchedule',$foundSchedule);
	}
}
