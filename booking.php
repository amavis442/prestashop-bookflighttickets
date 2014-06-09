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
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_'))
    exit;

include_once(_PS_MODULE_DIR_ . '/booking/booking_orders.php');

class Booking extends Module
{

    private $_html = '';
    private $_postErrors = array();
    public $details;
    public $owner;
    public $address;
    public $extra_mail_vars;

    public function __construct()
    {
        $this->name = 'booking';
        $this->tab = 'other';
        $this->version = '0.4';
        $this->author = 'Patrickswebsite.nl';
        $this->is_needed = 0;

        parent::__construct();

        $this->displayName = $this->l('Booking');
        $this->description = $this->l('Booking with date input for departure and arrival.');
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');
    }

    public function install()
    {
        if (!parent::install() ||
                !$this->registerHook('header') ||
                !$this->registerHook('displayBook') ||
                !$this->registerHook('displayLeftColumn') ||
                !$this->registerHook('displayAdminProductsExtra') ||
                !$this->_createTab()
        )
            return false;

        include_once(_PS_MODULE_DIR_ . '/' . $this->name . '/booking_install.php');

        $booking_install = new BookingInstall();
        if (!$booking_install->createTables()) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
                !$this->uninstallModuleTab('Adminlocation') ||
                !$this->uninstallModuleTab('AdminRoute') ||
                !$this->uninstallModuleTab('AdminInventory') ||
                !$this->uninstallModuleTab('AdminSchedule') ||
                !$this->uninstallModuleTab('AdminPrice') ||
                !$this->uninstallModuleTab('AdminMainNewTab')
        )
            return false;

        include_once(_PS_MODULE_DIR_ . '/' . $this->name . '/booking_install.php');
        //$booking_install = new BookingInstall();
        //$booking_install->createTables();

        return true;
    }

    private function _createTab()
    {
        $parent_tab = new Tab();
        $parent_tab->class_name = 'AdminMainNewTab';
        $parent_tab->id_parent = 0;
        $parent_tab->module = $this->name;
        $parent_tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = 'Vluchten';
        $parent_tab->add();


        $this->installModuleTab('AdminLocation', array((int) (Configuration::get('PS_LANG_DEFAULT')) => 'Location'), $parent_tab->id);
        $this->installModuleTab('AdminInventory', array((int) (Configuration::get('PS_LANG_DEFAULT')) => 'Inventory'), $parent_tab->id);
        $this->installModuleTab('AdminRoute', array((int) (Configuration::get('PS_LANG_DEFAULT')) => $this->l('Route')), $parent_tab->id);
        $this->installModuleTab('AdminSchedule', array((int) (Configuration::get('PS_LANG_DEFAULT')) => $this->l('Schedule')), $parent_tab->id);
        $this->installModuleTab('AdminPrice', array((int) (Configuration::get('PS_LANG_DEFAULT')) => $this->l('Price')), $parent_tab->id);

        /*
          $this->installModuleTab('AdminVluchten', array((int)(Configuration::get('PS_LANG_DEFAULT'))=>$this->l('Vluchten')), $parent_tab->id);
          $this->installModuleTab('AdminSchedule', array((int)(Configuration::get('PS_LANG_DEFAULT'))=>$this->l('Schedule')), $parent_tab->id);
         */
        return true;
    }

    private function installModuleTab($tabClass, $tabName, $idTabParent)
    {
        // $idTab = Tab::getIdFromClassName($idTabParent);
        $idTab = $idTabParent;
        if (!$id_tab = Tab::getIdFromClassName($tabClass)) {
            $pass = true;
            @copy(_PS_MODULE_DIR_ . $this->name . '/logo.gif', _PS_IMG_DIR_ . 't/' . $tabClass . '.gif');
            $tab = new Tab();
            $tab->name = $tabName;
            $tab->class_name = $tabClass;
            $tab->module = $this->name;
            $tab->id_parent = $idTab;
            $pass = $tab->save();
            return $pass;
        } else {
            return false;
        }
    }

    private function uninstallModuleTab($tabClass)
    {
        if ($idTab = Tab::getIdFromClassName($tabClass)) {
            $pass = true;
            @unlink(_PS_IMG_DIR_ . 't/' . $tabClass . '.gif');
            $tab = new Tab($idTab);
            $pass = $tab->delete();
            return $pass;
        } else {
            return false;
        }
    }

    private function common($params)
    {
        $this->context->smarty->assign(array('self' => dirname(__FILE__)));
    }

    /* Admin stuff */

    public function hookDisplayAdminProductsExtra()
    {
        $this->context->smarty->assign('name', $this->l('Gifts'));
        $this->context->smarty->assign('Hint', $this->l('The customer will be allowed to pick a product from this category for free.'));
        $this->context->smarty->assign('credits', $this->l('Powered by BlazingArts'));

        return $this->display(__FILE__, 'tab-body.tpl');
    }

    /* Frontend stuff */

    public function hookHeader($params)
    {
        //$this->context->controller->addJquery('1.10.1');
        //$this->context->controller->addJqueryUI('1.9.1');

        $this->context->controller->addJqueryPlugin('datepicker');
        $this->context->controller->addJqueryPlugin('autocomplete');
        //$this->context->controller->addJS(($this->_path).'js/booking.js');
        $this->context->controller->addCSS(($this->_path) . 'css/booking.css', 'screen');
    }

    public function hookDisplayBook($params)
    {
        if (!$this->active)
            return;


        /* Ophalen van reeds ingevulde data */
        $product = $params['product'];
        $category = $product['category'];
        if (strtolower($category) == 'snappers') {
            $this->context->smarty->assign('is_snapper', true);
        }
        if (strtolower($category) == 'arrangementen') {
            $this->context->smarty->assign('is_arrangement', true);
        }

        $id_product = $product['id_product'];
        $sql = sprintf('SELECT id_schedule FROM %s WHERE id_product = %d', _DB_PREFIX_ . 'product', $id_product);
        $id_schedule = Db::getInstance()->getValue($sql);
        if ($id_schedule > 0) {
            $this->context->smarty->assign('is_flight', true);
            $this->context->smarty->assign('cannotModify', 1);
        }
        $id_product = $params['product']['id_product'];
        $id_cart = $this->context->cart->id;

        $data = $this->getDates($id_cart, $id_product);
        $this->context->smarty->assign('arrival_date', preg_replace('/(\d{4})-(\d{2})-(\d{2})/', '$3-$2-$1', $data['arrival_date']));
        $this->context->smarty->assign('departure_date', preg_replace('/(\d{4})-(\d{2})-(\d{2})/', '$3-$2-$1', $data['departure_date']));

        $this->context->smarty->assign('product', $params['product']);
        $this->common($params);

        return $this->display(__FILE__, 'booking_datepicker.tpl');
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->common($params);
        return $this->display(__FILE__, 'book_form.tpl');
    }

    public function hookAjaxCall($params)
    {
        $from = preg_replace('/(\d{2})-(\d{2})-(\d{4})/', '$3-$2-$1', $_POST['from']);
        if (isset($_POST['nodepart']) && $_POST['nodepart']) {
            $to = '1970-01-01';
        } else {
            $to = preg_replace('/(\d{2})-(\d{2})-(\d{4})/', '$3-$2-$1', $_POST['to']);
        }
        $id_product = (int) $_POST['id_product'];

        $res = $this->saveOrder($id_product, $from, $to);

        $diff = (strtotime($to) - strtotime($from)) / 60 / 60 / 24;
        $this->common($params);
        return json_encode(array('result' => 'ok', 'diff' => $diff, 'sql' => $res));
    }

    private function saveOrder($id_product, $arrival_date, $departure_date)
    {
        $context = Context::getContext();
        /* Save stuff in database */
        $product = $context->cart->getProducts(false, $id_product);
        $cart_id = $context->cart->id;

        $sql = sprintf('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'booking WHERE id_product = %d AND id_cart=%d', $id_product, $cart_id);
        $aantal = Db::getInstance()->getValue($sql);
        if ($aantal > 0) {
            $sql = sprintf('UPDATE %s SET arrival_date =\'%s\' , departure_date = \'%s\' WHERE id_cart=%d AND id_product=%d', _DB_PREFIX_ . 'booking', $arrival_date, $departure_date, $cart_id, $id_product);
        } else {
            $sql = sprintf('INSERT INTO %s SET id_cart=%d, id_product= %d, arrival_date = \'%s\', departure_date = \'%s\', booking_date=NOW()', _DB_PREFIX_ . 'booking', $cart_id, $id_product, $arrival_date, $departure_date);
        }
        /* Eerst checken of er niet eerst een combinatie (id_order,id_product) */
        Db::getInstance()->Execute($sql);
        return $sql . ' ' . $this->static_token;
    }

    private function getDates($id_cart, $id_product)
    {
        $sql = sprintf('SELECT * FROM %s WHERE id_cart = %d AND id_product = %d', _DB_PREFIX_ . 'booking', $id_cart, $id_product);
        $data = Db::getInstance()->getRow($sql);
        return $data;
    }

}
