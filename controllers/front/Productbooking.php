<?php
/**
 * @since 1.5.0
 */
if (!defined('_PS_VERSION_'))
    exit;
include('/../../../config/config.inc.php');

include(dirname(__FILE__) . '/../../classes/ProductBooking.php');
//use booking\models\ProductBooking;

class bookingProductbookingModuleFrontController extends ModuleFrontController
{

    public $ajax_search;
    public $instant_search;
    public $id_product;
    
    public function __construct()
    {
        parent::__construct();
        $this->className = 'ProductBooking';

        $this->context = Context::getContext();
    }

    public function init()
    {
        parent::init();
        if (Tools::getValue('token') != Tools::getToken(false)) {
            //Tools::redirect('index.php');
            header('404 Page not found');
            die();
        }
    }

    public function postProcess()
    {
        //Tools::jsonDecode($json)
        if ($this->ajax) {
            if (!$this->context->cart->id && isset($_COOKIE[$this->context->cookie->getName()]))
            {
                $this->context->cart->add();
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
            }
                                
            $id_cart = $this->context->cart->id;
            $id_product = Tools::getValue('id_product');
            if (Tools::getValue('checkin_date')) {
                $checkin_date = preg_replace('/(\d{2})-(\d{2})-(\d{4})/',"$3-$2-$1",Tools::getValue('checkin_date'));
            }
            if (Tools::getValue('checkout_date')) {
                $checkout_date = preg_replace('/(\d{2})-(\d{2})-(\d{4})/',"$3-$2-$1",Tools::getValue('checkout_date'));
            }
            
            /* Check if there is already a record */
            $sql = sprintf('SELECT id_productbooking FROM '._DB_PREFIX_.'booking_productbooking WHERE id_cart = %d AND id_product = %d',$id_cart,$id_product);
            $result = Db::getInstance()->executeS($sql);
            $id_productbooking = ($result[0]['id_productbooking'] ? $result[0]['id_productbooking'] : null); 
                
            if ($id_productbooking) {
                $productbooking = new ProductBooking($id_productbooking); 
            } else {
                $productbooking = new ProductBooking();
                $productbooking->id_product = $id_product;
                $productbooking->id_cart = $id_cart;
                $productbooking->token = Tools::getToken(false);
            } 
            if (Tools::getValue('checkin_date')) {
                $productbooking->checkin_date = $checkin_date;
            }
            if (Tools::getValue('checkout_date')) {
                $productbooking->checkout_date = $checkout_date;
            }
            $productbooking->save();
            //$id_productbooking = $productbooking->id;
            
            //$json = Tools::jsonEncode(array('result'=>$checkin_date,'dbres'=>$result,'id_productbooking'=>$id_productbooking,'id_product'=>$id_product,'token'=>Tools::getValue('token'),'id_productbooking'=>$id_productbooking,'id_cart'=>$id_cart,'sql'=>$sql));
            $json = Tools::jsonEncode(array('result'=>$productbooking->id));
            
            
            die($json);
        }
        
        //die($json. ' WAT YH');
    }
}
