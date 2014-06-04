<?php
/**
 * @author Patrick Teunissen <patrick@patrickswebsite.nl>
 * Date: 12/17/13.
 */

/**
 * To bad we can not autoload our record models (AR)
 */
require_once(dirname(__file__) . '/../../classes/Inventory.php');

class AdminInventoryController extends ModuleAdminController
{
	public function __construct()
	{
		/**
		 * The name of the database table so we can extract data.
		 */
		$this->table = 'booking_inventory';
		/**
		 * AR Class
		 */
		$this->className = 'Inventory';
		$this->identifier = 'id_inventory';

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
			'id_inventory' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'designation' => array(
				'title' => $this->l('Designation'),
				'width' => 'auto',
			),
			'seats' => array(
				'title' => $this->l('Seats'),
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
		if (!($obj = $this->loadObject(true))) {
			return;
		}
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Inventory'),
				'image' => '../img/admin/cog.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Designation:'),
					'name' => 'designation',
					'size' => 40,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Seats:'),
					'name' => 'seats',
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

}