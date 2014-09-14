<?php

if (!defined('_PS_VERSION_'))
    exit;

class BookFlightTicketsInstall
{

    private $errorMsg = '';
    private $db_prefix = '';

    public function __construct()
    {
        $this->db_prefix = _DB_PREFIX_ . 'bookflighttickets';
    }

    /**
     * Create PayPal tables
     */
    public function createTables()
    {
        if (!$this->createMain() ||
                !$this->createLocation() ||
                !$this->createInventory() ||
                !$this->createTickets() ||
                !$this->createRoute() ||
                !$this->createSchedule() ||
                !$this->createScheduleProduct() ||
                !$this->createPrice() ||
                !$this->createReservation() ||
                !$this->createReservationDetails() ||
                !$this->createRsplink() ||
                !$this->updateConfiguration()
        ) {
            return false;
        }
        return true;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    private function createMain()
    {
        /* Set database */
        if (!Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '` (
			`id_bookflighttickets` INT AUTO_INCREMENT,
			`id_transaction` varchar(255) NOT NULL,
			`id_invoice` varchar(255) DEFAULT NULL,
			`id_cart` INT unsigned not null,
			`id_product` INT unsigned not null,
			`arrival_date` date NOT NULL,
			`departure_date` date NOT NULL,
			`booking_date` date NOT NULL,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
			PRIMARY KEY (`id_bookflighttickets`),
			INDEX(id_cart, id_product)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createInventory()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_inventory` (
		    `id_inventory` int(11) NOT NULL AUTO_INCREMENT,
		    `designation` varchar(30) NOT NULL,
		    `seats` int(11) NOT NULL,
                    `date_add` datetime NOT NULL,
                    `date_upd` datetime NOT NULL,
		    PRIMARY KEY (`id_inventory`)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_inventory(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createLocation()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_location` (
		    `id_location` int(11) NOT NULL AUTO_INCREMENT,
		    `location` varchar(255) NOT NULL,
		    `country` varchar(255) NOT NULL,
		    `code` varchar(6) NOT NULL,
		    `date_add` datetime NOT NULL,
                    `date_upd` datetime NOT NULL,
		    PRIMARY KEY (`id_location`)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
                )) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_location(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createRoute()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_route` (
			  `id_route` int(11) NOT NULL AUTO_INCREMENT,
			  `id_location_1` int(11) NOT NULL,
			  `id_location_2` int(11) NOT NULL,
			  `code` varchar(6) NOT NULL,
			  `order` int(1) NOT NULL,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_route`)
			) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_route(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createSchedule()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_schedule` (
			  `id_schedule` int(11) NOT NULL AUTO_INCREMENT,
			  `id_route` int(11) NOT NULL,
			  `id_inventory` int(11) NOT NULL,
			  `traveltime` time NOT NULL,
			  `departure` datetime NOT NULL,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_schedule`)
			) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_schedule(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createScheduleProduct()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_schedule_product` (
                            `id_scheduleproduct` int(11) NOT NULL AUTO_INCREMENT,
                            `id_schedule` int(11),
                            `id_product` int(11) NOT NULL,			 
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
                            PRIMARY KEY (`id_scheduleproduct`),
                            INDEX(`id_schedule`, `id_product`)
			) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_schedule_product(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createPrice()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_price` (
			  `id_price` int(11) NOT NULL AUTO_INCREMENT,
			  `id_schedule` int(11) NOT NULL,
			  `valid_until` int(11) NOT NULL,
			  `price` decimal(6,2) NOT NULL,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_price`)
			  
			) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_price(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createReservation()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_reservation` (
			  `id_reservation` int(11) NOT NULL AUTO_INCREMENT,
			  `id_cart` int(11) NOT NULL,
			  `id_schedule` int(11) NOT NULL,
			  `code` varchar(20) NOT NULL,
			  `price` decimal(5,2) NOT NULL,
			  `adults` tinyint(2) NOT NULL,
			  `children` tinyint(2) NOT NULL,
			  `special` varchar(255) NOT NULL,
			  `status` enum(\'nieuw\',\'geannuleerd\',\'afgerond\') NOT NULL,
			  `origin_id` int(11) NOT NULL,
			  `destination_id` int(11) NOT NULL,
			  `session_id` varchar(255) NOT NULL,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_reservation`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_reservation(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createRsplink()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_rsplink` (
			  `id_rsplink` int(11) NOT NULL AUTO_INCREMENT,
			  `id_reservation` int(11) NOT NULL,
			  `id_reservationdetails` int(11) NOT NULL,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_rsplink`)
			  ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_rsplink(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createReservationDetails()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_reservationdetails` (
			`id_reservationdetails` int(11) NOT NULL AUTO_INCREMENT,
                        `id_cart` int(11),
                        `id_product` int(11),
                        `id_schedule` int(11),
			`gender` enum(\'Male\',\'Female\') NOT NULL,
			`firstname` varchar(255) NOT NULL,
			`surname` varchar(255) NOT NULL,
			`birthdate` date NOT NULL,
			`email` varchar(255) NOT NULL,
			`phone` varchar(20) NOT NULL,
			`adress` varchar(200) NOT NULL,
			`postalcode` varchar(20) NOT NULL,
			`place` varchar(255) NOT NULL,
			`country` varchar(255) NOT NULL,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
			PRIMARY KEY (`id_reservationdetails`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COMMENT=\'PNR\'')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_reservationdetails(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    private function createTickets()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . $this->db_prefix . '_tickets` (
		  `id_tickets` int(11) NOT NULL AUTO_INCREMENT,
		  `id_cart` int(10) unsigned NOT NULL,
                        `date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
		  PRIMARY KEY (`id_tickets`)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            $this->errorMsg = 'Creating  ' . $this->db_prefix . '_tickets(' . __LINE__ . ') :: ' . Db::getInstance()->getMsgError();
            return false;
        }
        return true;
    }

    /**
     * Create category if not exists and use it for the flighttickets
     * 
     * @return boolean
     */
    private function updateConfiguration()
    {
        $context = Context::getContext();
        $checkCat = Category::searchByName((int) Configuration::get('PS_LANG_DEFAULT'), (int) Configuration::get('PS_BOOKFLICHTTICKETS_CAT_ID'));

        $category = new Category();
        if (!(is_array($checkCat) && count($checkCat) > 0)) {
            /* Category does not exist so add it */
            $category->name = array((int) Configuration::get('PS_LANG_DEFAULT') => 'Vliegtickets');
            $category->id_parent = Configuration::get('PS_HOME_CATEGORY');
            $category->link_rewrite = array((int) Configuration::get('PS_LANG_DEFAULT') => 'cool-url');
            $category->description = array((int) Configuration::get('PS_LANG_DEFAULT') => 'Deze categorie wordt gebruikt voor het boeken van vliegtickets in module: bookflighttickets. Let op!!!!: Niet verwijderen.'); 
            $category->add();

            //$category->id;
            Configuration::updateValue('PS_BOOKFLICHTTICKETS_CAT_ID', (int) $category->id);
        }
        return true;
    }

    /**
     * Delete Bookflight configuration
     */
    public function deleteConfiguration()
    {
        Configuration::deleteByName('PS_BOOKFLICHTTICKETS_CAT_ID');
        return true;
    }

}
