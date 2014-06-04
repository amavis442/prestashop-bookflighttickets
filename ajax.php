<?php
/**
 * User: patrick
 * Date: 12/9/13.
 */
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
include_once(dirname(__FILE__).'/booking.php');
$context = Context::getContext();
$booking = new Booking();
echo $booking->hookAjaxCall(array('cookie' => $context->cookie, 'cart' => $context->cart));
 