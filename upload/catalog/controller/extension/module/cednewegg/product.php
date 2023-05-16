<?php
include_once (DIR_SYSTEM.'library/cednewegg/product.php');
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
class ControllerExtensionModuleCedneweggProduct extends Controller
{
    public function syncinventoryprice()
    {
    	if($this->config->get('cednewegg_cron_token') == $this->request->get['token']) {
	        $product_lib = new CedNeweggProduct($this->registry);
	        $result = $product_lib->updateAllInventoryPrices();
	        print_r(json_encode($result));die;
	    }
    }
}