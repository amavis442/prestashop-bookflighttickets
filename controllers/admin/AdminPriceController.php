<?php

/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 * Date: 12/17/13.
 */
/**
 * To bad we can not autoload our record models (AR)
 */
require_once (dirname(__file__) . '/../../classes/Price.php');

class AdminPriceController extends ModuleAdminController
{

    public function __construct()
    {
        $this->name = 'Price';
        /**
         * The name of the database table so we can extract data.
         */
        $this->table = 'booking_price';
        /**
         * AR Class
         */
        $this->className = 'Price';
        $this->identifier = 'id_price';
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

        /**
         * Welke velden wil je laten zien in het hoofdscherm.
         */
        $this->fields_list = array(
            'id_price' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25
            ),
            'id_schedule' => array(
                'title' => $this->l('Schedule'),
                'width' => 'auto',
            ),
            'price' => array(
                'title' => $this->l('Price'),
                'width' => 'auto',
            ),
            'valid_until' => array(
                'title' => $this->l('Valid_until'),
                'width' => 'auto',
            ),
            'modified' => array(
                'title' => $this->l('Modified'),
                'width' => 'auto',
            ),
            'created' => array(
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

        $sql = 'select id_schedule,location FROM ' . _DB_PREFIX_ . 'booking_schedule';
        $schedules = Db::getInstance()->executeS($sql);


        //$optionsarray = array(array('id_location'=>1,'location'=>'Mijn test'));

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Prices'),
                'image' => '../img/admin/cog.gif'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Schedule:'),
                    'name' => 'id_schedule',
                    'options' => array(
                        'query' => $schedules,
                        'id' => 'id_schedule',
                        'name' => 'schedule'
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Price:'),
                    'name' => 'price',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Valid until:'),
                    'name' => 'vali_until',
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
