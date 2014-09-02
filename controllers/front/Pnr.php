<?php

/**
 * @since 1.5.0
 */
if (!defined('_PS_VERSION_'))
    exit;

include(dirname(__FILE__) . '/../../classes/RouteHelper.php');
include(dirname(__FILE__) . '/../../classes/ScheduleHelper.php');
require_once (dirname(__file__) . '/../../classes/Location.php');
require_once (dirname(__file__) . '/../../classes/Schedule.php');
include(dirname(__FILE__) . '/../../classes/MyGender.php');

class bookflightticketsPnrModuleFrontController extends ModuleFrontController
{

    public $ajax_search;
    public $instant_search;
    public $checkform;
    public $max_rows = 10;
    public $flighttype;
    public $fl_l1;
    public $fl_l2;
    public $flightfrom;
    public $flightto;
    public $flightdeparturedate;
    public $mysql_departuredate;
    public $flightreturndate;
    public $mysql_returndate;
    public $flightspread;
    public $flightnumadults;
    public $flightnumchildren;
    public $flightnumbaby;
    public $id_product_1;
    public $id_product_2;

    public function __construct()
    {
        parent::__construct();
        $this->className = 'Schedule';
    }

    public function init()
    {
        parent::init();
        if (Tools::getValue('token') != Tools::getToken()) {
            Tools::redirect('index.php');
            die('Invalid token');
        }
        $this->context->smarty->assign('token', Tools::getToken());



        $this->instant_search = Tools::getValue('instantSearch');
        $this->ajax_search = Tools::getValue('ajaxSearch');
        $this->checkform = Tools::getValue('checkform');

        if (!Tools::isSubmit('submitPnr') && !$this->checkform) {
            list($id_schedule, $id_product) = explode('_', Tools::getValue('booking_dep'));
            $this->context->cookie->id_product_1 = $id_product;
            list($id_schedule, $id_product) = explode('_', Tools::getValue('booking_ret'));
            $this->context->cookie->id_product_2 = $id_product;
        }

        if ($this->instant_search || $this->ajax_search) {
            /* Alleen de content */
            $this->display_header = false;
            $this->display_footer = false;
        }
        $this->max_rows = Tools::getValue('maxRows');
        if ($this->max_rows > 20) {
            $this->max_rows = 10;
        }

        $this->context->smarty->assign('flightdata', array(
            'flightfrom' => $this->context->cookie->flightfrom,
            'flightto' => $this->context->cookie->flightto,
            'flighttype' => $this->context->cookie->flighttype,
            'flightdeparturedate' => $this->context->cookie->flightdeparturedate,
            'flightreturndate' => $this->context->cookie->flightreturndate,
            'flightspread' => $this->context->cookie->flightspread,
            'flightnumadults' => $this->context->cookie->flightnumadults,
            'flightnumchildren' => $this->context->cookie->flightnumchildren,
            'flightnumbaby' => $this->context->cookie->flightnumbaby,
            'fl_l1' => $this->context->cookie->fl_l1,
            'fl_l2' => $this->context->cookie->fl_l2
                )
        );
    }

    /**
     * 
     * @param array $passenger
     * @return string|array
     */
    public function validatePassenger(Array $passenger)
    {
        $validate = array('email' => 'isEmail', 'firstname' => 'isString', 'lastname' => 'isString', 'days' => 'isBirthDate', 'address1' => '', 'postalcode' => '', 'city' => '', 'id_number' => '', 'phone' => '');

        $bday = $passenger['years'] . '-' . $passenger['months'] . '-' . $passenger['days'];

        $valid = array();
        foreach ($passenger as $key => $value) {
            if (empty($value)) {
                $valid[$key]['valid'] = false;
                $valid[$key]['msg'] = 'Geen waarde ingevuld';
            } else {
                switch ($key) {
                    case 'email':
                        $valid[$key]['valid'] = Validate::isEmail($value);
                        $valid[$key]['msg'] = 'Geen geldig email adres ingevuld.';
                        break;
                    case 'days':
                        $valid[$key]['valid'] = Validate::isBirthDate($bday);
                        $valid[$key]['msg'] = 'Geen geldige geboortedatum ingevuld.';
                        break;
                    case 'phone':
                        $valid[$key]['valid'] = Validate::isPhoneNumber($value);
                        $valid[$key]['msg'] = 'Geen geldig telefoonnummer ingevuld.';
                        break;
                    case 'postalcode':
                        $valid[$key]['valid'] = Validate::isPostCode($value);
                        $valid[$key]['msg'] = 'Geen geldige postcode ingevuld.';
                        break;
                    case 'city':
                        $valid[$key]['valid'] = Validate::isCityName($value);
                        $valid[$key]['msg'] = 'Geen geldige woonplaats ingevuld.';
                        break;
                    default:
                        $valid[$key]['valid'] = true;
                        $valid[$key]['msg'] = '';
                        break;
                }
            }
        }

        return $valid;
    }

    public function initContent()
    {
        parent::initContent();
        $id_lang = (int) (Configuration::get('PS_LANG_DEFAULT'));

        if ($this->ajax_search) {
            $query = Tools::replaceAccentedChars(urldecode(Tools::getValue('q')));
            $sql = sprintf('SELECT id_location as id,location,country,code FROM %1$s WHERE (location LIKE \'%2$s%%\' or Country LIKE \'%2$s%%\' OR Code Like \'%2$s%%\') LIMIT %3$d', _DB_PREFIX_ . Location::$definition['table'], $query, $this->max_rows);
            $result = Db::getInstance()->executeS($sql);
            $searchResults = $result;
            die(Tools::jsonEncode($searchResults));
        }

        if ($this->checkform) {
            $errors = array();
            foreach ($_POST['Passenger'] as $k => $item) {
                $error = $this->validatePassenger($item);
                $errors[$k] = $error;
            }

            $result = array('result' => $errors);
            die(Tools::jsonEncode($result));
        }



        $genders = Gender::getGenders();
        $this->context->smarty->assign(
                array(
                    'num_adults' => $this->context->cookie->flightnumadults,
                    'num_children' => $this->context->cookie->flightnumchildren,
                    'num_baby' => $this->context->cookie->flightnumbaby,
                    'genders' => $genders
                )
        );

        $p1 = null;
        $s1 = null;
        $p2 = null;
        $s2 = null;
        if ($this->context->cookie->id_product_1) {
            $id_product_1 = (int) $this->context->cookie->id_product_1;
            $p1 = new Product($id_product_1, false, $id_lang);
            if ($p1) {
                $s1 = Schedule::getData((int) $p1->id_schedule);
            }
        }
        if ($this->context->cookie->id_product_2) {
            $id_product_2 = (int) $this->context->cookie->id_product_2;
            $p2 = new Product($id_product_2, false, $id_lang);
            if ($p2) {
                $s2 = Schedule::getData((int) $p2->id_schedule);
            }
        }

        $total = ((int) $this->context->cookie->flightnumadults + (int) $this->context->cookie->flightnumchildren) * ($p1->price + $p2->price);
        $this->context->smarty->assign('total', $total);
        $this->context->smarty->assign('p1', $p1);
        $this->context->smarty->assign('s1', $s1);
        $this->context->smarty->assign('p2', $p2);
        $this->context->smarty->assign('s2', $s2);



        if (!Tools::isSubmit('submitPnr')) {

            $this->context->smarty->assign(
                    array(
                        'days' => Tools::dateDays(),
                        'years' => Tools::dateYears(),
                        'months' => Tools::dateMonths()
                    )
            );
            if ($this->context->customer->isLogged()) {
                $this->context->smarty->assign('guestInformations', $this->_getGuestInformations());
            }

            $this->setTemplate('pnrform.tpl');
        } else {
            /* Resume */
            $this->context->cookie->passengers = serialize($_POST['Passenger']);
            $passengers = array();
            foreach ($_POST['Passenger'] as $passenger) {
                $id_gender = $passenger['id_gender'];
                $passenger['gender'] = MyGender::getGender($id_gender);
                $passengers[] = $passenger;
            }
            $this->context->smarty->assign('passengers', $passengers); //$_POST['Passenger'] );

            $this->setTemplate('resume.tpl');
        }
    }

    /**
     * Hate to violate the Do Not Repeat yourself rule
     * Todo: find another way. Sourceis OrderOpcConctroller.php
     * 
     * @return type  
     */
    protected function _getGuestInformations()
    {
        $customer = $this->context->customer;
        $address_delivery = new Address($this->context->cart->id_address_delivery);

        $id_address_invoice = $this->context->cart->id_address_invoice != $this->context->cart->id_address_delivery ? (int) $this->context->cart->id_address_invoice : 0;
        $address_invoice = new Address($id_address_invoice);

        if ($customer->birthday)
            $birthday = explode('-', $customer->birthday);
        else
            $birthday = array('0', '0', '0');

        return array(
            'id_customer' => (int) $customer->id,
            'email' => Tools::htmlentitiesUTF8($customer->email),
            'customer_lastname' => Tools::htmlentitiesUTF8($customer->lastname),
            'customer_firstname' => Tools::htmlentitiesUTF8($customer->firstname),
            'newsletter' => (int) $customer->newsletter,
            'optin' => (int) $customer->optin,
            'id_address_delivery' => (int) $this->context->cart->id_address_delivery,
            'company' => Tools::htmlentitiesUTF8($address_delivery->company),
            'lastname' => Tools::htmlentitiesUTF8($address_delivery->lastname),
            'firstname' => Tools::htmlentitiesUTF8($address_delivery->firstname),
            'vat_number' => Tools::htmlentitiesUTF8($address_delivery->vat_number),
            'dni' => Tools::htmlentitiesUTF8($address_delivery->dni),
            'address1' => Tools::htmlentitiesUTF8($address_delivery->address1),
            'postcode' => Tools::htmlentitiesUTF8($address_delivery->postcode),
            'city' => Tools::htmlentitiesUTF8($address_delivery->city),
            'phone' => Tools::htmlentitiesUTF8($address_delivery->phone),
            'phone_mobile' => Tools::htmlentitiesUTF8($address_delivery->phone_mobile),
            'id_country' => (int) ($address_delivery->id_country),
            'id_state' => (int) ($address_delivery->id_state),
            'id_gender' => (int) $customer->id_gender,
            'sl_year' => $birthday[0],
            'sl_month' => $birthday[1],
            'sl_day' => $birthday[2],
            'id_address_invoice' => $id_address_invoice,
            'company_invoice' => Tools::htmlentitiesUTF8($address_invoice->company),
            'lastname_invoice' => Tools::htmlentitiesUTF8($address_invoice->lastname),
            'firstname_invoice' => Tools::htmlentitiesUTF8($address_invoice->firstname),
            'vat_number_invoice' => Tools::htmlentitiesUTF8($address_invoice->vat_number),
            'dni_invoice' => Tools::htmlentitiesUTF8($address_invoice->dni),
            'address1_invoice' => Tools::htmlentitiesUTF8($address_invoice->address1),
            'address2_invoice' => Tools::htmlentitiesUTF8($address_invoice->address2),
            'postcode_invoice' => Tools::htmlentitiesUTF8($address_invoice->postcode),
            'city_invoice' => Tools::htmlentitiesUTF8($address_invoice->city),
            'phone_invoice' => Tools::htmlentitiesUTF8($address_invoice->phone),
            'phone_mobile_invoice' => Tools::htmlentitiesUTF8($address_invoice->phone_mobile),
            'id_country_invoice' => (int) ($address_invoice->id_country),
            'id_state_invoice' => (int) ($address_invoice->id_state),
            'id_address_invoice' => $id_address_invoice,
            'invoice_company' => Tools::htmlentitiesUTF8($address_invoice->company),
            'invoice_lastname' => Tools::htmlentitiesUTF8($address_invoice->lastname),
            'invoice_firstname' => Tools::htmlentitiesUTF8($address_invoice->firstname),
            'invoice_vat_number' => Tools::htmlentitiesUTF8($address_invoice->vat_number),
            'invoice_dni' => Tools::htmlentitiesUTF8($address_invoice->dni),
            'invoice_address' => $this->context->cart->id_address_invoice !== $this->context->cart->id_address_delivery,
            'invoice_address1' => Tools::htmlentitiesUTF8($address_invoice->address1),
            'invoice_address2' => Tools::htmlentitiesUTF8($address_invoice->address2),
            'invoice_postcode' => Tools::htmlentitiesUTF8($address_invoice->postcode),
            'invoice_city' => Tools::htmlentitiesUTF8($address_invoice->city),
            'invoice_phone' => Tools::htmlentitiesUTF8($address_invoice->phone),
            'invoice_phone_mobile' => Tools::htmlentitiesUTF8($address_invoice->phone_mobile),
            'invoice_id_country' => (int) ($address_invoice->id_country),
            'invoice_id_state' => (int) ($address_invoice->id_state),
        );
    }

}
