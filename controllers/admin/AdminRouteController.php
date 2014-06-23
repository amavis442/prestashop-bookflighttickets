<?php

/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 * Date: 12/17/13.
 */
/**
 * To bad we can not autoload our record models (AR)
 */
require_once (dirname(__file__) . '/../../classes/Route.php');
require_once (dirname(__file__) . '/../../classes/Location.php');


class AdminRouteController extends ModuleAdminController
{

    public function __construct()
    {
        $this->name = 'Route';
        /**
         * The name of the database table so we can extract data.
         */
        $this->table = Route::$definition['table'];
        /**
         * AR Class
         */
        $this->className = 'Route';
        $this->identifier = 'id_route';
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
        //$this->_orderBy = 'id_location';



        $this->_select = '(SELECT location  FROM ' . _DB_PREFIX_ . Location::$definition['table'].' WHERE id_location_1=id_location) as location_1,
	                       (SELECT location FROM ' . _DB_PREFIX_ . Location::$definition['table'].' WHERE id_location_2=id_location)  as location_2';

        //$this->_join = _DB_PREFIX_.'booking_location ON (id_location_1 = id_location OR id_location_2 = id_location)';

        /**
         * Welke velden wil je laten zien in het hoofdscherm.
         */
        $this->fields_list = array(
            'id_route' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25
            ),
            'location_1' => array(
                'title' => $this->l('Source'),
                'width' => 'auto',
            ),
            'location_2' => array(
                'title' => $this->l('Destination'),
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
      public function display()
      {
      var_dump($this->_listsql);
      }
     */

    public function renderList()
    {

        $this->addRowAction('edit');
        //$this->addRowAction('duplicate');
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

        $sql = 'select id_location,location FROM ' . _DB_PREFIX_ . Location::$definition['table'];
        $locations = Db::getInstance()->executeS($sql);



        //$optionsarray = array(array('id_location'=>1,'location'=>'Mijn test'));

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Routes'),
                'image' => '../img/admin/cog.gif'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Source:'),
                    'name' => 'id_location_1',
                    'options' => array(
                        'query' => $locations,
                        'id' => 'id_location',
                        'name' => 'location'
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Destination:'),
                    'name' => 'id_location_2',
                    'options' => array(
                        'query' => $locations,
                        'id' => 'id_location',
                        'name' => 'location'
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Retour:'),
                    'name' => 'retour'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        return parent::renderForm();
    }

}
