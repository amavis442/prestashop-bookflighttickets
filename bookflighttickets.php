<?php

if (!defined('_PS_VERSION_'))
    exit;

class Bookflighttickets extends Module
{

    private $_html = '';
    private $_postErrors = array();
    public $details;
    public $owner;
    public $address;
    public $extra_mail_vars;

    public function __construct()
    {
        $this->name = 'bookflighttickets';
        $this->tab = 'other';
        $this->version = '0.7';
        $this->author = 'Patrickswebsite.nl';
        $this->is_needed = 0;

        parent::__construct();

        $this->displayName = $this->l('Booking flight tickets');
        $this->description = $this->l('Booking flight tickets with date input for departure and arrival.');
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');
    }

    public function install()
    {
        if (!parent::install() ||
                !$this->registerHook('header') ||
                !$this->registerHook('displayLeftColumn') ||
                !$this->_createTab()
        ) {
            $this->_errors[] = 'Error creating hooks/tabs';
            return false;
        }

        include_once(_PS_MODULE_DIR_ . '/' . $this->name . '/bookflighttickets_install.php');

        $booking_install = new BookFlightTicketsInstall();
        if (!$booking_install->createTables()) {
            $this->_errors[] = 'Error creating tables';
            $this->_errors[] = $booking_install->getErrorMsg();
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
        ) {
            $this->_errors[] = 'Error uninstalling tabs';
            return false;
        }
        
        if (Configuration::hasKey('PS_BOOKFLIGHTTICKETS_CAT_ID'))
                Configuration::deleteByName('PS_BOOKFLIGHTTICKETS_CAT_ID');
                
        //include_once(_PS_MODULE_DIR_ . '/' . $this->name . '/bookflighttickets_install.php');
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

        return true;
    }

    /**
     * 
     * @param string $tabClass
     * @param array $tabName
     * @param int $idTabParent
     * @return boolean
     */
    private function installModuleTab($tabClass, Array $tabName, $idTabParent)
    {
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

    /**
     * 
     * @param string $tabClass
     * @return boolean
     */
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

    /* Frontend stuff */

    public function hookHeader($params)
    {
        $this->context->controller->addJqueryPlugin('datepicker');
        $this->context->controller->addJqueryPlugin('autocomplete');
        $this->context->controller->addCSS(($this->_path) . 'css/bookflighttickets.css', 'screen');
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->common($params);
        return $this->display(__FILE__, 'bookflighttickets_form.tpl');
    }

}
