<?php
if (!defined('_PS_VERSION_'))
    exit;

include(dirname(__FILE__) . '/../../classes/RouteHelper.php');
include(dirname(__FILE__) . '/../../classes/ScheduleHelper.php');

class bookflightticketsSearchflightModuleFrontController extends ModuleFrontController
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
    }

    public function init()
    {
        parent::init();

        $this->instant_search = Tools::getValue('instantSearch');
        $this->ajax_search = Tools::getValue('ajaxSearch');

        if ($this->instant_search || $this->ajax_search) {
            /* Alleen de content */
            $this->display_header = false;
            $this->display_footer = false;
        }
        $this->max_rows = Tools::getValue('maxRows');
        if ($this->max_rows > 20) {
            $this->max_rows = 10;
        }

        if (!($this->instant_search || $this->ajax_search)) {
            $this->context->cookie->flightfrom = Tools::getValue('flightfrom');
            $this->context->cookie->flightto = Tools::getValue('flightto');
            $this->context->cookie->flighttype = Tools::getValue('flighttype');
            $this->context->cookie->flightdeparturedate = Tools::getValue('flightdeparturedate');
            $this->context->cookie->flightreturndate = Tools::getValue('flightreturndate');
            $this->context->cookie->flightspread = Tools::getValue('flightspread');
            $this->context->cookie->flightnumadults = Tools::getValue('flightnumadults');
            $this->context->cookie->flightnumchildren = Tools::getValue('flightnumchildren');
            $this->context->cookie->flightnumbaby = Tools::getValue('flightnumbaby');
            $this->context->cookie->fl_l1 = (int) Tools::getValue('fl_l1');
            $this->context->cookie->fl_l2 = (int) Tools::getValue('fl_l2');
            $this->context->cookie->write();

            $this->context->smarty->assign('flightdata', array(
                'flightfrom' => Tools::getValue('flightfrom'),
                'flightto' => Tools::getValue('flightto'),
                'flighttype' => Tools::getValue('flighttype'),
                'flightdeparturedate' => Tools::getValue('flightdeparturedate'),
                'flightreturndate' => Tools::getValue('flightreturndate'),
                'flightspread' => Tools::getValue('flightspread'),
                'flightnumadults' => Tools::getValue('flightnumadults'),
                'flightnumchildren' => Tools::getValue('flightnumchildren'),
                'flightnumbaby' => Tools::getValue('flightnumbaby'),
                'fl_l1' => (int) Tools::getValue('fl_l1'),
                'fl_l2' => (int) Tools::getValue('fl_l2')
                    )
            );

            $this->flightfrom = Tools::getValue('flightfrom');
            $this->flightto = Tools::getValue('flightto');
            $this->flighttype = Tools::getValue('flighttype');
            $this->flightdeparturedate = Tools::getValue('flightdeparturedate');
            $this->flightreturndate = Tools::getValue('flightreturndate');
            $this->flightspread = Tools::getValue('flightspread');
            $this->flightnumadults = Tools::getValue('flightnumadults');
            $this->flightnumchildren = Tools::getValue('flightnumchildren');
            $this->flightnumbaby = Tools::getValue('flightnumbaby');

            // European style date
            if (preg_match('/(\d{2})(\.|\/|\-)(\d{2})(\.|\/|\-)(\d{4})/', $this->flightdeparturedate, $match)) {
                $this->mysql_departuredate = $match[5] . '-' . $match[3] . '-' . $match[1];
                $this->mysql_returndate = preg_replace('/(\d{2})(\.|\/|\-)(\d{2})(\.|\/|\-)(\d{4})/', '$5-$3-$1', $this->flightreturndate);
            }
            
            // American style date
            if (preg_match('/(\d{4})(\.|\/|\-)(\d{2})(\.|\/|\-)(\d{4})/', $this->flightdeparturedate, $match)) {
                $this->mysql_departuredate = $match[1] . '-' . $match[3] . '-' . $match[5];
                $this->mysql_returndate = preg_replace('/(\d{4})(\.|\/|\-)(\d{2})(\.|\/|\-)(\d{2})/', '$1-$3-$5', $this->flightreturndate);
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    /**
     * 
     * @param string $date
     * @return boolean
     */
    public function isDate($date)
    {
        //preg_match('/^(([0-9])|([1-2][0-9])|3([01]))-((0?[0-9])|([1][1-2]))-20(([1-2])([0-9]))$', $date,$match);
        if (preg_match('/^(0?[0-9]|[012][0-9]|3[01])-(0?[0-9]|1[012])-(20[1-9][0-9])/', $date) ||
                preg_match('/^(20[1-9][0-9])-(0?[0-9]|1[0-2])-([0-9]?|[012][0-9]|3[01])/', $date)) {
            return true;
        }
        return false;
    }

    public function initContent()
    {
        parent::initContent();

        $query = Tools::replaceAccentedChars(urldecode(Tools::getValue('q')));
        if ($this->ajax_search) {
            $sql = sprintf('SELECT id_location as id,location,country,code FROM %1$s WHERE (location LIKE \'%2$s%%\' or Country LIKE \'%2$s%%\' OR Code Like \'%2$s%%\') LIMIT %3$d', _DB_PREFIX_ . 'bookflighttickets_location', $query, $this->max_rows);
            $result = Db::getInstance()->executeS($sql);
            $searchResults = $result;
            die(Tools::jsonEncode($searchResults));
        }


        /* Check if we have data */
        if (
                ((int) Tools::getValue('flightnumadults') < 1 && (int) Tools::getValue('flightnumchildren') < 1) ||
                !((int) Tools::getValue('flighttype') > 0) ||
                ( (int) Tools::getValue('flighttype') == 1 && (
                !(Tools::getValue('flightfrom') && Tools::getValue('flightto')) ||
                !($this->isDate(Tools::getValue('flightdeparturedate')) && $this->isDate(Tools::getValue('flightreturndate')) ))
                ) ||
                ((int) Tools::getValue('flighttype') == 2 && (
                !(Tools::getValue('flightfrom') && Tools::getValue('flightto')) ||
                !($this->isDate(Tools::getValue('flightdeparturedate')) )
                ))
        ) {

            $this->errors[] = Tools::displayError('U heeft het formulier onvolledig ingevuld of leeggelaten.');

            if (!(int) Tools::getValue('flighttype') > 0) {
                $this->errors[] = Tools::displayError('U heeft geen retour of enkele reis aangegeven.');
            }

            if (!Tools::getValue('flightto')) {
                $this->errors[] = Tools::displayError('U heeft geen bestemming opgegeven.');
            }

            if (!Tools::getValue('flightfrom')) {
                $this->errors[] = Tools::displayError('U heeft geen vertrekplaats opgegeven.');
            }

            if (!$this->isDate(Tools::getValue('flightdeparturedate'))) {
                $this->errors[] = Tools::displayError('U heeft een onjuiste of geen vertrekdatum opgegeven.');
            }
            if ((int) Tools::getValue('flighttype') == 1 && !$this->isDate(Tools::getValue('flightreturndate'))) {
                $this->errors[] = Tools::displayError('U heeft een onjuiste of geen retourdatum opgegeven.');
            }

            $this->context->smarty->assign('errors', $this->errors);
            $this->setTemplate('error.tpl');
            return;
        }

        $total_persons = (int) $this->flightnumadults + (int) $this->flightnumchildren;


        $this->context->smarty->assign(array('heen' => $this->mysql_departuredate,
            'terug' => $this->mysql_returndate,
            'origin' => $this->flightfrom,
            'destination' => $this->flightto));

        $routeHelper = new RouteHelper();
        $routeHelper->setOrigin((int) Tools::getValue('fl_l1'));
        $routeHelper->setDestination((int) Tools::getValue('fl_l2'));
        $routes = $routeHelper->getRoutes();
        $foundSchedule = false;

        if (is_array($routes) && count($routes) > 0) {
            $dtDeparture = new DateTime($this->mysql_departuredate . 'T00:00:00');
            $dtReturn = new DateTime($this->mysql_returndate . 'T00:00:00');
            $scheduleHelper_departure = new ScheduleHelper($dtDeparture, $routes);
            $schedules_depart = $scheduleHelper_departure->getSchedule(3);

            if ($this->flighttype == 1) {
                $routeHelper->setOrigin((int) Tools::getValue('fl_l2'));
                $routeHelper->setDestination((int) Tools::getValue('fl_l1'));
                $routes = $routeHelper->getRoutes();

                if ($routes && is_array($routes)) {
                    $scheduleHelper_return = new ScheduleHelper($dtReturn, $routes);
                    $schedules_return = $scheduleHelper_return->getSchedule(3);
                }
            }


            /* Retour */
            if ($this->flighttype == 1 && $schedules_depart && count($schedules_depart) && $schedules_return && count($schedules_return)) {
                $schedules = ScheduleHelper::makeCombinations($schedules_depart, $schedules_return, $total_persons);
                if ($schedules && count($schedules) > 0)
                    $foundSchedule = true;

                $this->context->smarty->assign('schedules', $schedules);
                $this->setTemplate('retour.tpl');
            } else {
                /* Enkel */
                if ($schedules_depart && count($schedules_depart)) {
                    foreach ($schedules_depart as $k => $item) {
                        foreach ($item as $i => $schedule) {
                            $id_schedule = $schedule['id_schedule'];
                            $id_product = ScheduleProduct::findProductIdByScheduleId($id_schedule);
                            if ($id_product) {
                                $product = new Product($id_product);
                                $schedules_depart[$k][$i]['id_product'] = $id_product;
                                $schedules_depart[$k][$i]['price'] = $product->getPrice(false);
                            }
                            unset($product);
                            $schedules_depart[$k][$i]['inventory'] = Inventory::getDesignation($schedule['id_inventory']);
                            $schedules_depart[$k][$i]['arrival'] = ScheduleHelper::getArrival($schedule['departure'], $schedule['traveltime']);
                            $schedules_depart[$k][$i]['totalprice'] = $schedules_depart[$k][$i]['price'] * ($this->flightnumadults + $this->flightnumchildren);
                        }
                    }
                    $this->context->smarty->assign('departures', $schedules_depart);
                    $foundSchedule = true;
                }
                $this->setTemplate('single.tpl');
            }

            $this->context->smarty->assign('foundSchedule', $foundSchedule);
            $this->context->smarty->assign('bookflighttickets_tpl_dir', dirname(__FILE__) . '/../../views/templates/front');
            $this->context->smarty->assign('flighttype', $this->flighttype);
        } else {
            $this->setTemplate('content.tpl');
        }
        $this->context->smarty->assign('token', Tools::GetToken());
        $this->context->smarty->assign('foundSchedule', $foundSchedule);
    }

}
