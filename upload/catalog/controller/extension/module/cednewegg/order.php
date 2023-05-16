<?php
include_once (DIR_SYSTEM.'library/cednewegg/order.php');
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  modules
 * @package   CedCatch
 * @author    CedCommerce Core Team
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
class ControllerExtensionModuleCedNeweggorder extends Controller
{
    public function fetchorders()
    {    
    	 if($this->config->get('cednewegg_cron_token') == $this->request->get['token']) {
	        $order_lib = new CedNeweggOrder($this->registry);
	        $result = $order_lib->fetchOrders();
	        print_r(json_encode($result)); die;
	    }
    }
}