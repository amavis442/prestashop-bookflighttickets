<?php
/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 * Date: 12/17/13.
 */

/**
 * To bad we can not autoload our record models (AR)
 */
require_once (dirname(__file__) . '/../../classes/Schedule.php');


class AdminScheduleController extends ModuleAdminController
{
	public function __construct()
    {
	    $this->name = 'Schedule';
	    /**
	     * The name of the database table so we can extract data.
	     */
	    //$this->table = _DB_PREFIX_.'booking_schedule';
	    /**
	     * AR Class
	     */
	  	$this->className = 'Schedule';
	    $this->identifier = 'id_schedule';
	    $this->table = 'booking_schedule';
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

	    $this->_select = ' id_route as route_desc, ROUND(p.price,2) price';
	    //$this->_select = '(SELECT location  FROM '._DB_PREFIX_.'booking_location WHERE id_location_1=id_location) as location_1,
	    //(SELECT location FROM '._DB_PREFIX_.'booking_location WHERE id_location_2=id_location)  as location_2';

	    $this->_join = 'LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_schedule = a.id_schedule)';

	    /**
	     * Welke velden wil je laten zien in het hoofdscherm.
	     */
	    $this->fields_list = array(
	    		'id_schedule' => array(
	    				'title' => $this->l('ID'),
	    				'align' => 'center',
	    				'width' => 25
	    				),
		        'id_route'=> array(
		        	    'title' => $this->l('Route'),
		        	    'width' => 'auto',
		        ),
			    'route_desc'=> array(
						'title' => $this->l('Route desc'),
				        'width' => 'auto',
		        ),
	    		'traveltime' => array(
	    				'title' => $this->l('Traveltime'),
	    				'width' => 'auto',
	    				),
	    		'departure' => array(
	    				'title' => $this->l('Departure'),
	    				'width' => 'auto',
	    				),
	    		'price' => array(
	    		     'title' => $this->l('Price'),
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

	/* Om de query te kunnen bekijken
	public function display()
		{
			die($this->_listsql);
		}
	*/
	
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
		$helper->simple_header = false;
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
	    return $helper->generateList($this->_list, $this->fields_list);
	}


	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('duplicate');
		$this->addRowAction('delete');

		if (!($this->fields_list && is_array($this->fields_list))) {
		  return false;
		}
        /* Voert een sql uit (dDb::getInstance()->executeS(sql)) en geeft de uitvoer aan $this->_list */
		$this->getList($this->context->language->id);
		
		
        $helper = new HelperList();

		// Empty list is ok
		if (!is_array($this->_list))
		{
			$this->displayWarning($this->l('Bad SQL query', 'Helper').'<br />'.htmlspecialchars($this->_list_error));
			return false;
		}
		
		foreach ($this->_list as $k=>$listitem) {
			$sql = sprintf("SELECT
					(SELECT location FROM ps_booking_location WHERE id_location_1=id_location) as location_1,
					(SELECT location FROM ps_booking_location WHERE id_location_2=id_location) as location_2 FROM `"._DB_PREFIX_."booking_route` a
					WHERE id_route = %d",$listitem['id_route']);
			$row = Db::getInstance()->getRow($sql);
			$this->_list[$k]['route_desc'] = $row['location_1']. ' - '. $row['location_2'];
		}



		$this->setHelperDisplay($helper);
		$helper->tpl_vars = $this->tpl_list_vars;
		$helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

		// For compatibility reasons, we have to check standard actions in class attributes
		foreach ($this->actions_available as $action)
		{
			if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action)
				$this->actions[] = $action;
		}
		$helper->is_cms = $this->is_cms;
		$list = $helper->generateList($this->_list, $this->fields_list);

		return $list;
	}

	/**
	 * Om een record te bewerken.
	 *
	 * @return mixed
	 */
	public function renderForm()
	{
	    /* Check if object is loaded. In our case BookingInventory */
		if (!($obj = $this->loadObject(true))) {
		    return;
		}
		
		$sql = 'select id_inventory,CONCAT(designation," - ",seats) as descr FROM '._DB_PREFIX_.'booking_inventory';
		$inventory = Db::getInstance()->executeS($sql);

		$sql = 'select id_route,id_location_1,id_location_2 FROM '._DB_PREFIX_.'booking_route';
		
		$routes = Db::getInstance()->executeS($sql);
		$finalRoutes = array();
		foreach ($routes as $route) {
			$sql = sprintf('SELECT location FROM '._DB_PREFIX_.'booking_location WHERE id_location = %d',$route['id_location_1']);
			$from = Db::getInstance()->getValue($sql);
			$sql = sprintf('SELECT location FROM '._DB_PREFIX_.'booking_location WHERE id_location = %d',$route['id_location_2']);
			$to = Db::getInstance()->getValue($sql);
			$route['combined'] = $from . ' - '.$to;
			$finalRoutes[] = $route;
		}
		
		$id_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$sql = 'SELECT id_product FROM '._DB_PREFIX_.'product WHERE id_schedule = ' .Tools::getValue('id_schedule');
		$id_product = Db::getInstance()->getValue($sql);
		$price = '00.00';
		if ($id_product) {
		    $product = new Product($id_product);
		  $price = $product->price;
		}
		$obj->price = round($price,2);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Schedule'),
				'image' => '../img/admin/cog.gif'
				),
			'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Inventory:'),
						'name' => 'id_inventory',
						'options' => array(
							'query' => $inventory,
							'id' => 'id_inventory',
							'name' => 'descr'
						),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Route:'),
						'name' => 'id_route',
						'options' => array(
							'query' => $finalRoutes,
							'id' => 'id_route',
							'name' => 'combined'
						),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Traveltime:'),
						'name' => 'traveltime',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Departure:'),
						'name' => 'departure',
					),
			       array(
			          'type' => 'text',
			         'label' => $this->l('Price:'),
			         'name' => 'price',
			          ),
				),
				'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'button'
				)
		);
        
		return parent::renderForm();

	}
	
	public function postProcess()
	{
	    parent::postProcess();
	    /* We maken er ook een product van zodat het order systeem werkt. */
	       /*
	        * 1. Check of record moet worden toegevoegd of aangepast.
	        * 2. Pas dit dan ook toe
	        */
	    if (Tools::isSubmit('submitAddbooking_schedule')) {
	       $id_category = (int)(Configuration::get('PS_BOOKING_CAT_ID'));
	       $id_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));
	       $sql = 'SELECT id_product FROM '._DB_PREFIX_.'product WHERE id_schedule = ' .Tools::getValue('id_schedule');
	       $id_product = Db::getInstance()->getValue($sql);
	       $product = null;
	       
	       /* Wat kenmerken ophalen */
	       $id_route = Tools::getValue('id_route');
	       $id_inventory = Tools::getValue('id_inventory');
	       $sql = 'select designation, seats FROM '._DB_PREFIX_.'booking_inventory WHERE id_inventory = '.$id_inventory ;
	       $inventory = Db::getInstance()->getRow($sql);
	       $quantity = $inventory['seats'];
	       
	       $departure = Tools::getValue('departure');
	       $sql = sprintf("SELECT
					(SELECT location FROM ps_booking_location WHERE id_location_1=id_location) as origin,
					(SELECT location FROM ps_booking_location WHERE id_location_2=id_location) as destination FROM `"._DB_PREFIX_."booking_route` a
					WHERE id_route = %d",$id_route);
	       $row = Db::getInstance()->getRow($sql);
	       $kenmerk = $inventory['designation']. ':: ' . $row['origin'] . ' - '. $row['destination'] . ' (' . $departure.')';
	       $price = Tools::getValue('price');
	       
	       if ($id_product) {
	           $product = new Product($id_product,false,$id_lang);
	           $product->name[$id_lang] = $kenmerk;
	           $product->price = $price;
	           $product->save();
	           /* Omdat het allemaal zo moeilijk moet in prestashop, gaan we even de boel zelf goedzetten */
	           $sql = sprintf('UPDATE %s SET name = \'%s\' WHERE id_product = %d AND id_lang = %d AND id_shop = %d',_DB_PREFIX_.'product_lang',addslashes($kenmerk),$id_product,$id_lang,1);
	           Db::getInstance()->execute($sql);
	           
	       } else {
                $product = new Product();
	            $product->description = array($id_lang => '');
	            $product->description_short = array($id_lang => '');
	            $product->link_rewrite = array($id_lang => 'dummy-dummy');
	            $product->name = array($id_lang => '');
	            $product->id_category = array(0);
	            $product->id_color = array(0);
	            $product->quantity = $quantity;
	            $product->price = $price;
	            $product->id_shop_default = 1;
	            $product->id_manufacturer = 0;
	            $product->id_supplier = 0;
	            $product->id_category_default = $id_category;
	            $product->reference = 'Vliegticket';
	            $product->active = 1;
	            $product->is_virtual = 1;
	            $product->visibility = 'none';
	            $product->condition = 'new';
	            $product->id_schedule = Tools::getValue('id_schedule');
	            $product->name[$id_lang] = $kenmerk;
	            $product->add();
	            //quantity	
	            //minimal_quantity	
	            //price	
	            //wholesale_price	
	            //unity	
	            //unit_price_ratio	
	            //out_of_stock	
	            //	text_fields	active	redirect_type	id_product_redirected	available_for_order	available_date	condition	show_price	indexed	visibility	cache_is_pack	cache_has_attachments	is_virtual	cache_default_attribute	date_add	date_upd	advanced_stock_management	id_schedule
	             
	       }
	       
	    }
	}
}