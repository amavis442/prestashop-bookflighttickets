<?php

/**
 * @since 1.5.0
 */
if (!defined('_PS_VERSION_'))
    exit;

include(dirname(__FILE__) . '/../../classes/Reservation.php');
include(dirname(__FILE__) . '/../../classes/ReservationDetails.php');

class bookflightticketsReservationModuleFrontController extends ModuleFrontController
{

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function postProcess()
    {
        if ($this->context->cookie->exists() && !$this->errors && $this->context->cookie->flightfrom) {
            if ($this->context->cart->id) {
                $id_cart = $this->context->cart->id;
            } else {
                /* Nodig om niet bij elke aanroep een nieuwe cart aan te maken.
                 * Wel rot dat de naamgevin mobile_theme niet zo duidelijk is.
                 */
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id) {
                    $id_cart = $this->context->cart->id;
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                }
            }
            $id_product_1 = (int) $this->context->cookie->id_product_1;
            $id_schedule_1 = Product::getScheduleId($id_product_1);
            $id_product_2 = (int) $this->context->cookie->id_product_2;
            $id_schedule_2 = Product::getScheduleId($id_product_2);
            $n = 1;
            if ($id_schedule_1) {
                $n = 1;
            }
            if ($id_schedule_2) {
                $n = 2;
            }


            $passengers = unserialize($this->context->cookie->passengers);
            foreach ($passengers as $passenger) {
                unset($r);
                /*if (ReservationDetails::alreadyExists($id, $passenger)) {
                    continue;
                }*/

                /*
                 * TODO: check inbouwen of we al dezelfde gegevens hebben of niet.
                 */
                $r = new ReservationDetails();
                $r->gender = $passenger['id_gender'];
                $r->id_cart = $id_cart;
                //if ($n == 1) {
                    $r->id_schedule = $id_schedule_1;
                //}
                //if ($n == 2) {
                //    $r->id_schedule = $id_schedule_2;
                //}

                $r->id_product = $id_product_1;
                $r->firstname = $passenger['firstname'];
                $r->surname = $passenger['lastname'];
                $r->birthdate = $passenger['years'] . '-' . $passenger['months'] . '-' . $passenger['days'];
                $r->email = $passenger['email'];
                $r->phone = $passenger['phone'];
                $r->adress = $passenger['address1'];
                $r->postalcode = $passenger['postalcode'];
                $r->place = $passenger['city'];
                $r->identification_number = $passenger['id_number'];
                $r->country = 'NL'; //$passenger->id_gender;
                $r->add();
                if ($n == 2) {
                    unset($r->id);
                    $r->id_product = $id_product_2;
                    $r->id_schedule = $id_schedule_2;
                    $r->add();
                }
                
            }

            /* Producten aan de cart toevoegen
             * en redirect naar de betaling.
             */
            $qnty = (int) $this->context->cookie->flightnumadults + (int) $this->context->cookie->flightnumchildren;

            if ($id_product_1) {
                $this->context->cart->updateQty($qnty, $id_product_1);
            }
            if ($id_product_2) {
                $this->context->cart->updateQty($qnty, $id_product_2);
            }


            /* pAyment options */
            $passenger = $passengers[1]; /* Main passenger */


            if (!$this->context->customer->isLogged() && Validate::isEmail($passenger['email'])) {
                $customer = new Customer();
                $customer->id_gender = $passenger['id_gender'];
                $customer->email = $passenger['email'];
                $customer->lastname = $passenger['lastname'];
                $customer->firstname = $passenger['firstname'];
                $customer->birthday = $passenger['years'] . '-' . $passenger['months'] . '-' . $passenger['days'];
                $pwd = 'wsxedc';
                $customer->passwd = md5(pSQL(_COOKIE_KEY_ . $pwd));
                $customer->is_guest = 1;
                $customer->active = 1;

                if ($customer->add()) {
                    $context = Context::getContext();
                    $context->customer = $customer;
                    $context->smarty->assign('confirmation', 1);
                    $context->cookie->id_customer = (int) $customer->id;
                    $context->cookie->customer_lastname = $customer->lastname;
                    $context->cookie->customer_firstname = $customer->firstname;
                    $context->cookie->passwd = $customer->passwd;
                    $context->cookie->logged = 1;
                    // if register process is in two steps, we display a message to confirm account creation
                    if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE'))
                        $context->cookie->account_created = 1;
                    $customer->logged = 1;
                    $context->cookie->email = $customer->email;
                    $context->cookie->is_guest = 1; //!Tools::getValue('is_new_customer', 1);
                    // Update cart address
                    $context->cart->secure_key = $customer->secure_key;

                    $id_lang = (int) (Configuration::get('PS_LANG_DEFAULT'));

                    //echo $customer->id;  
                    $address = new Address();
                    $address->id_customer = (int) $customer->id;
                    $address->lastname = $customer->lastname;
                    $address->firstname = $customer->firstname;
                    $address->address1 = $passenger['address1'];
                    $address->postcode = $passenger['postalcode'];
                    $address->city = $passenger['city'];
                    $address->phone = $passenger['phone'];
                    $address->country = Country::getNameById($id_lang, 13);
                    $address->id_country = Country::getByIso('NL');
                    $address->alias = Country::getNameById($id_lang, 13);
                    if ($address->add()) {
                        $this->context->cart->id_customer = (int) $customer->id;
                        $this->context->cart->id_address_delivery = (int) $address->id;
                        $this->context->cart->id_address_invoice = (int) $address->id;
                        $this->context->cookie->id_address_invoice = (int) $address->id;
                        $this->context->cookie->id_address_delivery = (int) $address->id;
                        $this->context->cart->update();
                    }
                } else {
                    die('Kan geen klant aanmaken. Waarschijnlijk staat gast afrekenen in admin uit.');
                }
            }
            Tools::redirect('index.php?controller=order-opc');
        } else {
            $this->errors[] = Tools::displayError('Geen vlucht geselecteerd.', false);
            Tools::redirect('index.php');
        }
    }

    protected function processChangeProductInCart()
    {
        $mode = (Tools::getIsset('update') && $this->id_product) ? 'update' : 'add';

        if ($this->qty == 0)
            $this->errors[] = Tools::displayError('Null quantity.');
        elseif (!$this->id_product)
            $this->errors[] = Tools::displayError('Product not found');

        $product = new Product($this->id_product, true, $this->context->language->id);
        if (!$product->id || !$product->active) {
            $this->errors[] = Tools::displayError('This product is no longer available.', false);
            return;
        }

        $qty_to_check = $this->qty;
        $cart_products = $this->context->cart->getProducts();

        if (is_array($cart_products))
            foreach ($cart_products as $cart_product) {
                if ((!isset($this->id_product_attribute) || $cart_product['id_product_attribute'] == $this->id_product_attribute) &&
                        (isset($this->id_product) && $cart_product['id_product'] == $this->id_product)) {
                    $qty_to_check = $cart_product['cart_quantity'];

                    if (Tools::getValue('op', 'up') == 'down')
                        $qty_to_check -= $this->qty;
                    else
                        $qty_to_check += $this->qty;

                    break;
                }
            }

        // Check product quantity availability
        if ($this->id_product_attribute) {
            if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->id_product_attribute, $qty_to_check))
                $this->errors[] = Tools::displayError('There isn\'t enough product in stock.');
        }
        elseif ($product->hasAttributes()) {
            $minimumQuantity = ($product->out_of_stock == 2) ? !Configuration::get('PS_ORDER_OUT_OF_STOCK') : !$product->out_of_stock;
            $this->id_product_attribute = Product::getDefaultAttribute($product->id, $minimumQuantity);
            // @todo do something better than a redirect admin !!
            if (!$this->id_product_attribute)
                Tools::redirectAdmin($this->context->link->getProductLink($product));
            elseif (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->id_product_attribute, $qty_to_check))
                $this->errors[] = Tools::displayError('There isn\'t enough product in stock.');
        }
        elseif (!$product->checkQty($qty_to_check))
            $this->errors[] = Tools::displayError('There isn\'t enough product in stock.');

        // If no errors, process product addition
        if (!$this->errors && $mode == 'add') {
            // Add cart if no cart found
            if (!$this->context->cart->id) {
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id)
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
            }

            // Check customizable fields
            if (!$product->hasAllRequiredCustomizableFields() && !$this->customization_id)
                $this->errors[] = Tools::displayError('Please fill in all of the required fields, and then save your customizations.');

            if (!$this->errors) {
                $cart_rules = $this->context->cart->getCartRules();
                $update_quantity = $this->context->cart->updateQty($this->qty, $this->id_product, $this->id_product_attribute, $this->customization_id, Tools::getValue('op', 'up'), $this->id_address_delivery);
                if ($update_quantity < 0) {
                    // If product has attribute, minimal quantity is set with minimal quantity of attribute
                    $minimal_quantity = ($this->id_product_attribute) ? Attribute::getAttributeMinimalQty($this->id_product_attribute) : $product->minimal_quantity;
                    $this->errors[] = sprintf(Tools::displayError('You must add %d minimum quantity', false), $minimal_quantity);
                } elseif (!$update_quantity)
                    $this->errors[] = Tools::displayError('You already have the maximum quantity available for this product.', false);
                elseif ((int) Tools::getValue('allow_refresh')) {
                    // If the cart rules has changed, we need to refresh the whole cart
                    $cart_rules2 = $this->context->cart->getCartRules();
                    if (count($cart_rules2) != count($cart_rules))
                        $this->ajax_refresh = true;
                    else {
                        $rule_list = array();
                        foreach ($cart_rules2 as $rule)
                            $rule_list[] = $rule['id_cart_rule'];
                        foreach ($cart_rules as $rule)
                            if (!in_array($rule['id_cart_rule'], $rule_list)) {
                                $this->ajax_refresh = true;
                                break;
                            }
                    }
                }
            }
        }

        $removed = CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
        if (count($removed) && (int) Tools::getValue('allow_refresh'))
            $this->ajax_refresh = true;
    }

}
