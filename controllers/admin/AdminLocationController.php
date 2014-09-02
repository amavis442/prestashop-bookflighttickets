<?php

/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 * Date: 12/17/13.
 */
/**
 * To bad we can not autoload our record models (AR)
 */
require_once (dirname(__file__) . '/../../classes/Location.php');

class AdminLocationController extends ModuleAdminController
{

    public function __construct()
    {
        /**
         * The name of the database table so we can extract data.
         */
        $this->table = Location::$definition['table'];
        /**
         * AR Class
         */
        $this->className = 'Location';
        $this->identifier = 'id_location';
        /**
         * Context so we can access cart,cookie and other stuff.
         */
        $this->context = Context::getContext();
        /**
         * No language
         */
        $this->lang = false;
        /**
         * We need a database to store data
         */
        $this->requiredDatabase = true;

        /**
         * Remove multiple records at one go, with confirmation box.
         */
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
        $this->_orderBy = 'id_location';


        /*
          $this->_select = ''; // Reformat values
          $this->_join = ''; // Join with other tables
          $this->_orderBy = 'id_order';
          $this->_orderWay = 'DESC';
         */

        //$this->module
        /*
          if (!$this->module->active)
          Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
         */
        /**
         * Welke velden wil je laten zien in het hoofdscherm.
         */
        $this->fields_list = array(
            'id_location' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25
            ),
            'location' => array(
                'title' => $this->l('Location'),
                'width' => 'auto',
            ),
            'country' => array(
                'title' => $this->l('Country'),
                'width' => 'auto',
            ),
            'code' => array(
                'title' => $this->l('Code'),
                'width' => 'auto',
            ),
            'date_upd' => array(
                'title' => $this->l('Modified'),
                'width' => 'auto',
            ),
            'date_add' => array(
                'title' => $this->l('Created'),
                'width' => 'auto',
            ),
        );

        parent::__construct();
    }

    /*
      private function initList()
      {
      $this->fields_list = array(
      'id_category' => array(
      'title' => $this->l('Id'),
      'width' => 140,
      'type' => 'text',
      ),
      'name' => array(
      'title' => $this->l('Name'),
      'width' => 140,
      'type' => 'text',
      ),
      );
      $helper = new HelperList();

      $helper->shopLinkType = '';

      $helper->simple_header = true;

      // Actions to be displayed in the "Actions" column
      $helper->actions = array('edit', 'delete', 'view');

      $helper->identifier = 'id_category';
      $helper->show_toolbar = true;
      $helper->title = 'HelperList';
      $helper->table = $this->name.'_categories';

      $helper->token = Tools::getAdminTokenLite('AdminModules');
      $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
      return $helper;
      }
     */

    public function renderList()
    {
        /* Which options should we offer on the main screen */
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    /**
     * Om een record te bewerken.
     *
     * @return mixed
     */
    public function renderForm()
    {
        /* Check if object is loaded. In our case BookingInventory */
        if (!($obj = $this->loadObject(true)))
            return;



        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Locations'),
                'image' => '../img/admin/cog.gif'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Location:'),
                    'name' => 'location',
                    'size' => 40,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Country:'),
                    'name' => 'country',
                    'size' => 40,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Code:'),
                    'name' => 'code',
                    'size' => 6,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        return parent::renderForm();
    }

    /**
     * initContent
     */
    /* 	public function initContent()
      {

      parent::initContent();
      $this->setTemplate('pickinglist.tpl');


      $sql = "SELECT product_name, COUNT(id_order_detail) quantity FROM
      ps_orders a
      INNER JOIN ps_order_detail b ON a.id_order = b.id_order
      GROUP BY product_name
      ORDER BY product_name ASC";

      $products = array();
      if ($results = Db::getInstance()->ExecuteS($sql)) {
      foreach ($results as $row) {
      $products[] = array('product' => $row['product_name'], 'quantity' => $row['quantity']);
      }
      }
      $this->context->smarty->assign('products',$products);
      } */
}
