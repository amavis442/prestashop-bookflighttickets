<?php
/*
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2013 PrestaShop SA
 *  @version  Release: $Revision: 14390 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

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
				!$this->alterProduct()
		){
			return false;
		}
		return true;
	}


	private function createMain()
	{
		/* Set database */
		if (!Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking` (
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
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')) {
			return false;
		}
		return true;
	}

	private function createInventory()
	{
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_inventory` (
		  `id_inventory` int(11) NOT NULL AUTO_INCREMENT,
		  `designation` varchar(10) NOT NULL,
		  `seats` int(11) NOT NULL,
		  `modified` datetime NOT NULL,
		  `created` datetime NOT NULL,
		  PRIMARY KEY (`id_inventory`),
		  KEY (`designation`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')) {
					return false;
		}
		return true;
	}

	private function createLocation()
	{
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_location` (
		  `id_location` int(11) NOT NULL AUTO_INCREMENT,
		  `location` varchar(255) NOT NULL,
		  `country` varchar(255) NOT NULL,
		  `code` varchar(6) NOT NULL,
		  `modified` datetime NOT NULL,
		  `created` datetime NOT NULL,
		  PRIMARY KEY (`id_location`),
		  KEY (`location`,`country`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'
		)) {
			return false;
		}
		return true;
	}

	private function createRoute()
	{
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_route` (
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
			) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8')) {
			return false;
		}
		return true;
	}


	private function createSchedule()
	{
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_schedule` (
			  `id_schedule` int(11) NOT NULL AUTO_INCREMENT,
			  `id_route` int(11) NOT NULL,
			  `id_inventory` int(11) NOT NULL,
			  `traveltime` time NOT NULL,
			  `departure` datetime NOT NULL,
			  `modified` datetime NOT NULL,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_schedule`),
			  KEY (`id_inventory`,`id_route`)
			) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8')) {
			return false;
		}
		return true;
	}
	
	private function createPrice()
	{
	    if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_price` (
			  `id_price` int(11) NOT NULL AUTO_INCREMENT,
			  `id_schedule` int(11) NOT NULL,
			  `valid_until` int(11) NOT NULL,
			  `price` decimal(6,2) NOT NULL,
			  `modified` datetime NOT NULL,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_price`),
			  KEY (`id_price`,`id_schedule`,`valid_until`)
			) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8')) {
				return false;
	    }
	    return true;
	}

	private function createReservation()
	{
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_reservation` (
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
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')){
			return false;
		}
		return true;
	}

	private function createRsplink()
	{
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_rsplink` (
			  `id_rsplink` int(11) NOT NULL AUTO_INCREMENT,
			  `id_reservation` int(11) NOT NULL,
			  `id_reservationdetails` int(11) NOT NULL,
			  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id_rsplink`),
			  KEY (`id_reservation`,`id_reservationdetails`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')){
			return false;
		}
		return true;
	}


	private function createReservationDetails()
	{
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_reservationdetails` (
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
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 COMMENT=\'PNR\'')){
			return false;
		}
		return true;
	}

	private function createTickets()
	{
		if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'booking_tickets` (
		  `id_tickets` int(11) NOT NULL AUTO_INCREMENT,
		  `id_cart` int(10) unsigned NOT NULL,
		  `modified` int(11) NOT NULL,
		  `created` int(11) NOT NULL,
		  PRIMARY KEY (`id_tickets`),
		  KEY (`id_rsplink`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')){
			return false;
		}
		return true;
	}

	private function alterProduct()
	{
	    if (!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product` 
	        ADD id_schedule INT NOT NULL')){
			return false;
	    }
	    return true;
	}
	
	
	/**
	 * Set configuration table
	 */
	public function updateConfiguration($paypal_version)
	{
		Configuration::updateValue('PAYPAL_SANDBOX', 0);
		Configuration::updateValue('PAYPAL_HEADER', '');
		Configuration::updateValue('PAYPAL_BUSINESS', 0);
		Configuration::updateValue('PAYPAL_BUSINESS_ACCOUNT', 'paypal@prestashop.com');
		Configuration::updateValue('PAYPAL_API_USER', '');
		Configuration::updateValue('PAYPAL_API_PASSWORD', '');
		Configuration::updateValue('PAYPAL_API_SIGNATURE', '');
		Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT', 0);
		Configuration::updateValue('PAYPAL_CAPTURE', 0);
		Configuration::updateValue('PAYPAL_PAYMENT_METHOD', WPS);
		Configuration::updateValue('PAYPAL_NEW', 1);
		Configuration::updateValue('PAYPAL_DEBUG_MODE', 0);
		Configuration::updateValue('PAYPAL_SHIPPING_COST', 20.00);
		Configuration::updateValue('PAYPAL_VERSION', $paypal_version);
		Configuration::updateValue('PAYPAL_COUNTRY_DEFAULT', (int)Configuration::get('PS_COUNTRY_DEFAULT'));

		// PayPal v3 configuration
		Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT', 1);
	}
	
	/**
	 * Delete PayPal configuration
	 */
	public function deleteConfiguration()
	{
		Configuration::deleteByName('PAYPAL_SANDBOX');
		Configuration::deleteByName('PAYPAL_HEADER');
		Configuration::deleteByName('PAYPAL_BUSINESS');
		Configuration::deleteByName('PAYPAL_API_USER');
		Configuration::deleteByName('PAYPAL_API_PASSWORD');
		Configuration::deleteByName('PAYPAL_API_SIGNATURE');
		Configuration::deleteByName('PAYPAL_BUSINESS_ACCOUNT');
		Configuration::deleteByName('PAYPAL_EXPRESS_CHECKOUT');
		Configuration::deleteByName('PAYPAL_PAYMENT_METHOD');
		Configuration::deleteByName('PAYPAL_TEMPLATE');
		Configuration::deleteByName('PAYPAL_CAPTURE');
		Configuration::deleteByName('PAYPAL_DEBUG_MODE');
		Configuration::deleteByName('PAYPAL_COUNTRY_DEFAULT');

		// PayPal v3 configuration
		Configuration::deleteByName('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT');
	}
	
	/**
	 * Create a new order state
	 */
	public function createOrderState()
	{
		if (!Configuration::get('PAYPAL_OS_AUTHORIZATION'))
		{
			$orderState = new OrderState();
			$orderState->name = array();

			foreach (Language::getLanguages() as $language)
			{
				if (strtolower($language['iso_code']) == 'fr')
					$orderState->name[$language['id_lang']] = 'Autorisation acceptée par PayPal';
				else
					$orderState->name[$language['id_lang']] = 'Authorization accepted from PayPal';
			}

			$orderState->send_email = false;
			$orderState->color = '#DDEEFF';
			$orderState->hidden = false;
			$orderState->delivery = false;
			$orderState->logable = true;
			$orderState->invoice = true;

			if ($orderState->add())
			{
				$source = dirname(__FILE__).'/../../img/os/'.Configuration::get('PS_OS_PAYPAL').'.gif';
				$destination = dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif';
				copy($source, $destination);
			}
			Configuration::updateValue('PAYPAL_OS_AUTHORIZATION', (int)$orderState->id);
		}
	}
}
