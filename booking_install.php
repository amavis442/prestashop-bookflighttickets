<?php

if (!defined('_PS_VERSION_'))
    exit;

class BookingInstall
{

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
                !$this->createPrice() ||
                !$this->createReservation() ||
                !$this->createReservationDetails() ||
                !$this->createRsplink() ||
                !$this->alterProduct() ||
                !$this->createCategory() ||
                !$this->createProductBooking()
        ) {
            return false;
        }
        return true;
    }

    private function createMain()
    {
        /* Set database */
        if (!Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking` (
			`id_booking` INT AUTO_INCREMENT,
			`id_order` int(10) unsigned NOT NULL,
			`id_transaction` varchar(255) NOT NULL,
			`id_invoice` varchar(255) DEFAULT NULL,
			`id_cart` INT unsigned not null,
			`id_product` INT unsigned not null,
			`arrival_date` date NOT NULL,
			`departure_date` date NOT NULL,
			`booking_date` date NOT NULL,
			PRIMARY KEY (`id_booking`),
			INDEX(id_order, id_product)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    private function createInventory()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_inventory` (
		  `id_inventory` int(11) NOT NULL AUTO_INCREMENT,
		  `designation` varchar(10) NOT NULL,
		  `seats` int(11) NOT NULL,
		  `modified` datetime NOT NULL,
		  `created` datetime NOT NULL,
		  PRIMARY KEY (`id_inventory`),
		  KEY (`designation`)) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    private function createLocation()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_location` (
		  `id_location` int(11) NOT NULL AUTO_INCREMENT,
		  `location` varchar(255) NOT NULL,
		  `country` varchar(255) NOT NULL,
		  `code` varchar(6) NOT NULL,
		  `modified` datetime NOT NULL,
		  `created` datetime NOT NULL,
		  PRIMARY KEY (`id_location`),
		  KEY (`location`,`country`)) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
                )) {
            return false;
        }
        return true;
    }

    private function createRoute()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_route` (
			  `id_route` int(11) NOT NULL AUTO_INCREMENT,
			  `id_location_1` int(11) NOT NULL,
			  `id_location_2` int(11) NOT NULL,
			  `code` varchar(6) NOT NULL,
			  `order` int(1) NOT NULL,
			  `modified` datetime NOT NULL,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_route`),
			  KEY (`id_location_1`),
			  KEY (`id_location_2`)
			) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    private function createSchedule()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_schedule` (
			  `id_schedule` int(11) NOT NULL AUTO_INCREMENT,
			  `id_route` int(11) NOT NULL,
			  `id_inventory` int(11) NOT NULL,
			  `traveltime` time NOT NULL,
			  `departure` datetime NOT NULL,
			  `modified` datetime NOT NULL,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_schedule`),
			  KEY (`id_inventory`,`id_route`)
			) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    private function createPrice()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_price` (
			  `id_price` int(11) NOT NULL AUTO_INCREMENT,
			  `id_schedule` int(11) NOT NULL,
			  `valid_until` int(11) NOT NULL,
			  `price` decimal(6,2) NOT NULL,
			  `modified` datetime NOT NULL,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_price`),
			  KEY (`id_price`,`id_schedule`,`valid_until`)
			) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    private function createReservation()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_reservation` (
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
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_reservation`),
			  KEY (`id_booking`,`id_schedule`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    private function createRsplink()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_rsplink` (
			  `id_rsplink` int(11) NOT NULL AUTO_INCREMENT,
			  `id_reservation` int(11) NOT NULL,
			  `id_reservationdetails` int(11) NOT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_rsplink`),
			  KEY (`id_reservation`,`id_reservationdetails`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    private function createReservationDetails()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_reservationdetails` (
			  `id_reservationdetails` int(11) NOT NULL AUTO_INCREMENT,
		      `id_cart` int(11),
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
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_reservationdetails`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COMMENT=\'PNR\'')) {
            return false;
        }
        return true;
    }

    private function createTickets()
    {
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_tickets` (
		  `id_tickets` int(11) NOT NULL AUTO_INCREMENT,
		  `id_cart` int(10) unsigned NOT NULL,
		  `modified` int(11) NOT NULL,
		  `created` int(11) NOT NULL,
		  PRIMARY KEY (`id_tickets`),
		  KEY (`id_rsplink`)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    private function createProductBooking()
    {
        /* Set database */
        if (!Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'booking_productbooking` (
			`id_productbooking` INT AUTO_INCREMENT,
			`id_order` int(10) unsigned NOT NULL,
                        `id_cart` int(10) unsigned NOT NULL,
                        `id_product` int(10) unsigned NOT NULL,
                        `checkin_date` date NOT NULL,
                        `checkout_date` date NOT NULL,
                        `token` varchar(255) not null,
			`date_add` datetime NOT NULL,
                        `date_upd` datetime NOT NULL,
			PRIMARY KEY (`id_productbooking`),
			INDEX(id_order, id_product,id_cart)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }
    
    private function alterProduct()
    {
        if (!Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'product` 
	        ADD id_schedule INT NOT NULL')) {
            return false;
        }
        return true;
    }

    private function createCategory()
    {
        $context = Context::getContext();
        $checkCat = Category::searchByName((int) Configuration::get('PS_LANG_DEFAULT'), (int) Configuration::get('PS_LANG_DEFAULT'));

        $category = new Category();
        if (!(is_array($checkCat) && count($checkCat) > 0)) {
            /* Category does not exist so add it */
            $category->name = array((int) Configuration::get('PS_LANG_DEFAULT') => 'Vliegtickets');
            $category->id_parent = Configuration::get('PS_HOME_CATEGORY');
            $category->link_rewrite = array((int) Configuration::get('PS_LANG_DEFAULT') => 'cool-url');
            $category->add();
            
            //$category->id;
            Configuration::updateValue('PS_BOOKING_CAT_ID', (int)$category->id);
            
        }
    }

    /**
     * Set configuration table
     */
    public function updateConfiguration($paypal_version)
    {
        Configuration::updateValue('PS_BOOKING_CAT_ID', 0);
    }

    /**
     * Delete PayPal configuration
     */
    public function deleteConfiguration()
    {
        Configuration::deleteByName('BOOKING_CAT');
    }

}
