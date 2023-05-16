<?php
include_once(DIR_SYSTEM . 'library/cednewegg.php');
include_once(DIR_SYSTEM . 'library/cednewegg/product.php');

class CedNewEggOrder
{
    private $db;
    private $session;
    private $config;
    private $registry;
    private $currency;
    private $request;
    private $weight;
    protected $api_url = '';
    protected $api_key = '';
    protected $timestamp;
    protected $cedebay_helper;

    private static $instance;

    /**
     * @param  object $registry Registry Object
     */
    public static function getInstance($registry)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($registry);
        }

        return static::$instance;
    }

    /**
     * @param  object $registry Registry Object
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->session = $registry->get('session');
        $this->config = $registry->get('config');
        $this->currency = $registry->get('currency');
        $this->request = $registry->get('request');
        $this->weight = $registry->get('weight');
        $this->openbay = $registry->get('openbay');
        $this->timestamp = time();
        $this->cednewegg_helper = new CedNewEgg($this->registry);
    }

    public function fetchOrders($params = array())
    {
        
        $successes = array();
        $errors = array();
        try {
            $result = $this->getOrders();
            $count = 0;
            if(isset($result['success']) && $result['success']) {
                if(isset($result['message']) && !empty($result['message'])) {
                    $orders[] = $result['message'];
                    ///print_r($orders); die();
                    foreach ($orders as $order) {
                        $purchase_order_id = $order['OrderNumber'];
                        $order_date = $order['OrderDate'];
                        $order_status = $order['OrderStatus'];
                        $shipment_data = $order['ShipToAddress1'].' '.$shipment_data = $order['ShipToAddress2'];
                        $is_exist = $this->isneweggOrderExist($purchase_order_id);
                        if($is_exist) {
                            continue;
                        }
                        $oc_order_id = $this->createOpencartOrder($order);
                        if ($oc_order_id) {
                            $count++;
                        $sql = "INSERT INTO `".DB_PREFIX."cednewegg_order` SET 
                        opnecart_orderid   ='".(int)$oc_order_id."',
                        newegg_order_id     ='".$purchase_order_id."',
                        order_place_date   ='".$order_date."',
                        newegg_failed_reason = newegg_failed_reason,
                        newegg_order_status  ='".$order_status."',
                        order_data        ='".$this->db->escape(json_encode($order))."'";
                            $this->db->query($sql);
                            $successes[] = "Order $purchase_order_id Imported Successfully";
                        } else {
                            $errors[] = "Failed To Create NewEgg Order $purchase_order_id. Please Check Failed Order Log";
                            $cron_tab_update = $this->cron_tab_update($errors);
                        }
                    }
                } else {
                    $errors[] = "No New Order(s) Found";
                    $cron_tab_update = $this->cron_tab_update($errors);
                }
            } else {
                if(isset($result['message']) && !empty($result['message'])) {
                    $errors = array_merge($errors, $result['message']);
                } else {
                    $errors[] = "Failed To Get Response From NewEgg";
                    $cron_tab_update = $this->cron_tab_update($errors);
                }
            }
            if($count == 0 && empty($errors)) {
                $errors[] = "No New Order(s) Found";
                $cron_tab_update = $this->cron_tab_update($errors);
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $this->cedebay_helper->log(
                __METHOD__,
                'Info',
                'Fetched Order Data',
                array(
                    'data' => $params,
                    'response' => $e->getMessage()
                ),
                true
            );
        }
        return array(
            'success' => $successes,
            'error' => $errors
        );
    }

    public function isneweggOrderExist($order_id)
    {
        $is_exist = false;
        if ($order_id) {
            $sql = "SELECT `id` FROM `" . DB_PREFIX . "cednewegg_order` where `newegg_order_id` = '" . $order_id . "'";
            $result = $this->db->query($sql);
            if ($result->num_rows && isset($result->row['id']) && !empty($result->row['id'])) {
                $is_exist = true;
            }
        }
        return $is_exist;
    }

   public function cron_tab_update($msg)
    {  
        $this->db->query("INSERT INTO " . DB_PREFIX . "cednewegg_cron SET 
        job_code    = 'NewEgg Order', 
        created_at  = CURRENT_TIMESTAMP,
        finished_at = CURRENT_TIMESTAMP,
        message     = '".$msg[0]."',
        status      = '0'");
        return true;
    }

    public function log_tab($msg,$method,$type,$data)
    {  
        $this->db->query("INSERT INTO " . DB_PREFIX . "cednewegg_logs SET 
        method      = '".$method."', 
        type        = '".$type."',
        data        = '".$data."',,
        message     = '".$msg[0]."',
        created_at  = CURRENT_TIMESTAMP,");
        return true;
    }
    

    public function updateOrderStatus($newegg_order_id, $order_status_id = 1)
    {
        $result = $this->db->query("SELECT `opencart_orderid` FROM `" . DB_PREFIX . "cednewegg_order` 
        WHERE `newegg_order_id` = '" . $newegg_order_id . "'");
        $order_id = 0;
        if ($result && $result->num_rows) {
            $order_id = $result->row['opencart_orderid'];
        }
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history 
        SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', date_added = NOW()");
        $this->db->query("UPDATE " . DB_PREFIX . "order SET `order_status_id` = '" . (int)$order_status_id . "' 
        WHERE `order_id` = '" . (int)$order_id . "'");
    }

    public function getOrderDataById($newegg_order_id)
    {
        $sql = "SELECT `order_data` FROM `".DB_PREFIX."cednewegg_order` WHERE `newegg_order_id`='".$newegg_order_id."'";
        $q = $this->db->query($sql);
        if($q->num_rows) {
            $data = @json_decode($q->row['order_data'], true);
            return $data;
        }
        return array();
    }

    public function shipOrder($newegg_order_id, $carrier_code = '',$tracking_number = '')
    {
        $params = array();
        $params['shippingCarrier'] = $carrier_code;
        $params['trackingNumber'] = $tracking_number;
        $token = $this->config->get('cedebay_token');
        $shipToDatetime = strtotime(date('Y-m-d H:i:s'));
        $shipToDate = date("Y-m-d", $shipToDatetime) . 'T' . date("H:i:s", $shipToDatetime);
        try {
            $variable = "CompleteSale";
            $requestBody = '<?xml version="1.0" encoding="utf-8"?>
                    <CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                        <RequesterCredentials>
                            <eBayAuthToken>' . $token . '</eBayAuthToken>
                        </RequesterCredentials>
                        <WarningLevel>High</WarningLevel>
                        <OrderID>' . $newegg_order_id . '</OrderID>
                        <Shipment>
                            <ShipmentTrackingDetails>
                              <ShipmentTrackingNumber>' . $params['trackingNumber'] . '</ShipmentTrackingNumber>
                              <ShippingCarrierUsed>' . $params['shippingCarrier'] . '</ShippingCarrierUsed>
                            </ShipmentTrackingDetails>
                            <ShippedTime>' . $shipToDate . '</ShippedTime>
                        </Shipment>
                        <Shipped>' . 1 . '</Shipped>
                       <!-- <TransactionID>0</TransactionID>-->
                    </CompleteSaleRequest>';
            $response = $this->cedebay_helper->sendHttpRequest($requestBody, $variable);
            $data = $this->cedebay_helper->xml2array($response);
            $this->cedebay_helper->log(
                __METHOD__,
                'Info',
                'Ship Newegg Order '.$newegg_order_id,
                json_encode(array(
                    'NewEgg Order ID ' => $newegg_order_id,
                    'Param' => $params,
                    'Response' => $data
                ))
            );
            if (isset($data['CompleteSaleResponse']['Ack']) && $data['CompleteSaleResponse']['Ack'] == "Success") {
                $shipped_order_status = $this->config->get('cedebay_order_shipped_status');
                $this->updateOrderStatus($newegg_order_id, $shipped_order_status);
                return array(
                    'success' => true,
                    'message' => 'Order Shipped Successfully'
                );
            } else {
                $message = 'Failed To Ship Order '.$newegg_order_id;
                if (isset($data['CompleteSaleResponse']['Errors']['ShortMessage'])) {
                    $message = $data['CompleteSaleResponse']['Errors']['ShortMessage'];
                }
                return array(
                    'success' => false,
                    'message' => $message
                );
            }
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    public function createOpencartOrder($data)
    {
        //print_r(json_encode($data));die;
        $opencart_orderid = false;
        $order_info = $this->formatOrderData($data);
        //print_r($order_info); die('iopi');
        if(is_array($order_info) && !empty($order_info)) {
            $opencart_orderid = $this->addOrder($order_info);
        }
        return $opencart_orderid;
    }
    public function formatOrderData($data)
    {
       
        $newegg_order_id = $data['OrderNumber'];
        $order_data = array();
        $order_data['sorder_id'] = $data['OrderNumber'];
        $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        $order_data['store_id'] = $this->config->get('config_store_id');
        $order_data['store_name'] = $this->config->get('config_name');
        $base_url = HTTPS_SERVER;

        if (strpos($base_url, 'admin') !== false) {
            $base_url = str_replace('admin/', '', $base_url);
        }
 
        $order_data['store_url'] = $base_url;
        $order_data['customer_id'] = '';
        $order_data['customer_group_id'] = $this->config->get('config_customer_group_id');
        //print_r($data); die(); 
        if(isset($data['ItemInfoList'])){
            $transactions = $data['ItemInfoList'][0];
        }elseif(isset($data['ItemInfoList'])){
            $transactions[] = $data['ItemInfoList'];
        }
        
        $firstname = '';
        $lastname = '';
        $email = trim($this->config->get('cednewegg_order_email'));
        $telephone = '';
        if(isset($data['ShipToFirstName'])) {
            $firstname = $data['ShipToFirstName'];
        }
        if(isset($data['ShipToLastName'])) {
            $lastname = $data['ShipToLastName'];
        }
        if(isset($data['customeremailaddress'])) {
            $email = $data['customeremailaddress'];
        }
        if(empty($email)) {
            $email = $newegg_order_id.'@ebay.com';
        }
        if (isset($data['customerphonenumber'])) {
            $telephone = $data['customerphonenumber'];
        }

        $order_data['firstname'] = $firstname;
        $order_data['lastname'] = $lastname;
        $order_data['email'] = $email;
        $order_data['telephone'] = $telephone;
        $order_data['fax'] = '';
        $order_data['custom_field'] = array();
        $order_data['payment_firstname'] = $firstname;
        $order_data['payment_lastname'] = $lastname;
        $order_data['payment_company'] = '';

        $address_1 = '';
        $address_2 = '';
        $city = '';
        $postal_code = '';
        $state = '';
        $country = '';
        if(isset($data['ShipToAddress1'])) {
            $address_1 =  $data['ShipToAddress1'];
        }
        if(isset($data['ShipToAddress2'])) {
            $address_2 =  $data['ShipToAddress2'];
        }
        if(isset($data['ShipToCityName'])) {
            $city =  $data['ShipToCityName'];
        }
        if(isset($data['ShipToZipCode'])) {
            $postal_code =  $data['ShipToZipCode'];
        }
        if(isset($data['ShipToStateCode'])) {
            $state =  $data['ShipToStateCode'];
        }
        if(isset($data['ShipToCountryCode'])) { 
            $country =  $data['ShipToCountryCode'];
            if($country =="UNITED STATES"){
                $country = "USA";
             }
        }
       
        $getLocalizationDeatails = $this->getLocalizationDeatails($state, $country);
        $order_data['payment_address_1'] = $address_1;
        $order_data['payment_address_2'] = $address_2;
        $order_data['payment_city'] = $city;
        $order_data['payment_postcode'] = $postal_code;
        $order_data['payment_zone'] = $getLocalizationDeatails['name'];
        $order_data['payment_zone_id'] = $getLocalizationDeatails['zone_id'];
        $order_data['payment_country'] = $getLocalizationDeatails['country_name'];
        $order_data['payment_country_id'] = $getLocalizationDeatails['country_id'];
        $order_data['payment_address_format'] = '';
        $order_data['payment_custom_field'] = array();
        $order_data['payment_method'] = 'NewEgg Payment';
        $order_data['payment_code'] = 'CedNewEgg_payment';

        $order_data['shipping_firstname'] = $firstname;
        $order_data['shipping_lastname']  = $lastname;
        $order_data['shipping_company']   = '';
        $order_data['shipping_address_1'] = $address_1;
        $order_data['shipping_address_2'] = $address_2;
        $order_data['shipping_city']     = $city;
        $order_data['shipping_postcode'] = $postal_code;
        $order_data['shipping_zone']     = $getLocalizationDeatails['name'];
        $order_data['shipping_zone_id']  = $getLocalizationDeatails['zone_id'];
        $order_data['shipping_country']  = $getLocalizationDeatails['country_name'];
        $order_data['shipping_country_id'] = $getLocalizationDeatails['country_id'];;
        $order_data['shipping_address_format'] = '';
        $order_data['shipping_custom_field'] = array();
        $order_data['shipping_method'] = isset($data['ShipService'])
        && !empty($data['ShipService']) ? $data['ShipService'] : 'NewEgg Shipment';
        // for products
        $shippingCost = isset($data['ShippingAmount']) ?
            (float)$data['ShippingAmount'] : 0;
        try{  
            //print_r($data); die();
            if(isset($data['ItemInfoList'])){
                $items_array = $data['ItemInfoList'];
            }elseif(isset($data['ItemInfoList'])){
                $items_array[] = $data['ItemInfoList'];
            }
            if (!empty($items_array)) {
                foreach ($items_array as $orderLine => $item) {
                    $type = 'simple';
                    $sku  = $this->getProductSKU($item['SellerPartNumber']);  /*isset($item['SellerPartNumber']) ? $item['SellerPartNumber'] : '';
                    if (isset($item['SellerPartNumber']) && !empty($item['Variation'])) {
                        $type = 'variant';
                        $sku = $item['Variation']['SKU'];
                    }*/
                    if (!strlen($sku)) {
                        return false;
                    }
                $product_title = $item['Description'];
                $qty = isset($item['OrderedQty']) ? $item['OrderedQty'] : '0';
                $itemCost = isset($item['UnitPrice']) ? (float)$item['UnitPrice'] : 0;
                    $product = $this->getProductBySKU($sku, $qty, $product_title, $itemCost, $newegg_order_id, $data,$type);
                    //print_r($product); die('uiuouo');

                    if (is_array($product) && count($product)) {
                        $order_data['products'][] = $product;
                    } else {
                        return false;
                    }
                }
            }

        }catch(Exception $e){
            $errors[] = $e->getMessage();
            $this->log_tab($e->getMessage(),$e->getmethod(),$type,$data);
        }

        $sub_total = 0;
        $tax = 0;
        if (isset($order_data['products']) && count($order_data['products']) > 0) {
            foreach ($order_data['products'] as $key => $value) {
                $sub_total = $sub_total + (floatval($value['total']));
                $tax = $tax + (floatval($value['tax']));
            }  
            $order_data['comment'] = '';
            $order_data['total'] = (float)$data['OrderTotalAmount'];
            $order_data['affiliate_id'] = '0';
            $order_data['commission'] = '0';
            $order_data['marketing_id'] = '0';
            $order_data['tracking'] = '';
            $order_data['language_id'] = $this->config->get('config_language_id');

            if (isset($this->session->data['currency']) && $this->session->data['currency']) {
                $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
                $order_data['currency_code'] = $this->session->data['currency'];
                $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
            } else {
                $order_data['currency_id'] = $this->currency->getId($this->config->get('config_currency'));
                $order_data['currency_code'] = $this->config->get('config_currency');
                $order_data['currency_value'] = $this->currency->getValue($this->config->get('config_currency'));
            }
            $order_data['vouchers'] = array();

            $order_data['totals'][] = array(
                'code' => 'shipping',
                'title' => 'NewEgg Shipping',
                'text' => $this->currency->format($shippingCost,$this->config->get('config_currency')),
                'value' => (float)$shippingCost,
                'sort_order' => $this->config->get('shipping_sort_order')
            );
            $order_data['totals'][] = array(
                'code' => 'sub_total',
                'title' => 'Sub-Total',
                'text' => $this->currency->format($sub_total,$this->config->get('config_currency')),
                'value' => $sub_total,
                'sort_order' => $this->config->get('sub_total_sort_order')
            );
            $order_data['totals'][] = array(
                'code' => 'tax',
                'title' => 'Tax',
                'text' => $this->currency->format($tax,$this->config->get('config_currency')),
                'value' => $tax,
                'sort_order' => $this->config->get('tax_sort_order')
            );
            $order_data['totals'][] = array(
                'code' => 'total',
                'title' => 'Total',
                'text' => $this->currency->format(max(0, $order_data['total']),$this->config->get('config_currency')),
                'value' => max(0, $order_data['total']),
                'sort_order' => $this->config->get('total_sort_order')
            );
            $order_data['ip'] = $this->request->server['REMOTE_ADDR'];
            if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $order_data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_partner_id_AGENT'])) {
                $order_data['partner_id_agent'] = $this->request->server['HTTP_partner_id_AGENT'];
            } else {
                $order_data['partner_id_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $order_data['accept_language'] = '';
            }

            if (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $order_data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_partner_id_AGENT'])) {
                $order_data['partner_id_agent'] = $this->request->server['HTTP_partner_id_AGENT'];
            } else {
                $order_data['partner_id_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $order_data['accept_language'] = '';
            }
            //$order_data['order_status'] = $accept_order_status = $this->config->get('cednewegg_order_import_status');
            //print_r($order_data); die('ioo');
            return $order_data;
        } else {
            return array();
        }
    }

    public function getProductSKU($Sellerpartnumber)
    {
        $sql = "SELECT `sku` FROM `" . DB_PREFIX . "product` WHERE `ean` ='" . $Sellerpartnumber . "'";
        $query = $this->db->query($sql);   
        return $query->row['sku'];
    }
    public function getProductBySKU($sku, $q, $product_title, $cednewegg_price, $newegg_order_id, $worder_data, $type = 'simple'){
        $product = array(); 
        if ($type == 'variant') { 
            $product_id = 0;
            $option_value_id = 0;
            if(strpos($sku, '-')!==false) {
                $sku = explode('-',$sku);
                $product_id = $sku[0];
                $option_value_id = $sku[1];
            } else {
                $product_id = $sku;
            }
            if(!empty($product_id)) {
                $product['product_id'] = $product_id;
                $sql = "SELECT `status`,`quantity`,`model`,`price`FROM `" . DB_PREFIX . "product` WHERE `product_id`='" . $product['product_id'] . "'";
                $query = $this->db->query($sql);
                if (strlen($product_title) == '0') {
                    $sql_name = "SELECT `name` FROM `" . DB_PREFIX . "product_description` WHERE `product_id`='" . $product['product_id'] . "' and `language_id`='" . $this->config->get('config_language_id') . "'";
                    $query_name = $this->db->query($sql_name);
                    if ($query_name->num_rows)
                        $product_title = $query_name->row['name'];
                }
                if($option_value_id) {
                    $sql_option = "SELECT `product_option_value_id`,`product_option_id` FROM `" . DB_PREFIX . "product_option_value` WHERE `product_id`='" . $product_id . "' AND `option_value_id`='" . $option_value_id . "'";
                    $sql_option = $this->db->query($sql_option);
                    if($sql_option && $sql_option->num_rows) {
                        foreach ($sql_option->rows as $key => $option_d) {
                            $option_combination[$option_d['product_option_id']] = $option_d['product_option_value_id'];
                        }
                    }
                }
                if ($query->num_rows) {
                    $status = $query->row['status'];
                    $quant  = $query->row['quantity'];
                    $model  = $query->row['model'];
                    $price  = $query->row['price'];
                    if ($status) {
                        if ($quant >= $q) {
                            $product['quantity'] = $q;
                            $product['model'] = $model;
                            $product['subtract'] = $q;
                            $product['price'] = $cednewegg_price;
                            $product['total'] = ($cednewegg_price) ? ($q * $cednewegg_price) : $q * $price;
                            $product['tax'] = 0;
                            $product['reward'] = 0;
                            $product['name'] = $product_title;
                            $product['option'] = $option_combination;
                            $product['download'] = array();
                            return $product;
                        } else {
                            $this->orderErrorInformation($sku, $newegg_order_id, $worder_data, "REQUESTED QUANTITY FOR PRODUCT ID " . $product['product_id'] . " IS NOT AVAILABLE");
                            return array();
                        }
                    } else {
                        $this->orderErrorInformation($sku, $newegg_order_id, $worder_data, "PRODUCT STATUS IS DISABLED WITH ID " . $product['product_id'] . "");
                        return array();
                    }
                } else {
                    $this->orderErrorInformation($sku, $newegg_order_id, $worder_data, "PRODUCT ID" . $product['product_id'] . " DOES NOT EXIST");
                    return array();
                }
            } else {
                $this->orderErrorInformation($sku, $newegg_order_id, $worder_data, "PRODUCT ID" . $product['product_id'] . " DOES NOT EXIST");
            }
        } else { 
            // checking merchant sku in products
            $sql = "SELECT `product_id` FROM `" . DB_PREFIX . "product` WHERE `sku`='" . $sku . "'";
            $productdata = $query = $this->db->query($sql);
            //print_r($sku); die();
            $product['product_id'] = '';
            if ($productdata->num_rows) {
                $product['product_id'] = $productdata->row['product_id'];
            }
            if ($product['product_id']) {
                $sql = "SELECT `status`,`quantity`,`model`,`price`FROM `" . DB_PREFIX . "product` WHERE `product_id`='" . $product['product_id'] . "'";
                $query = $this->db->query($sql);
                if (strlen($product_title) == '0') {
                    $sql_name = "SELECT `name` FROM `" . DB_PREFIX . "product_description` WHERE `product_id`='" . $product['product_id'] . "' and `language_id`='" . $this->config->get('config_language_id') . "'";
                    $query_name = $this->db->query($sql_name);
                    if ($query_name->num_rows)
                        $product_title = $query_name->row['name'];
                }
                if ($query->num_rows) {
                    $status = $query->row['status'];
                    $quant = $query->row['quantity'];
                    $model = $query->row['model'];
                    $price = $query->row['price'];
                    if ($status) {
                        if (($quant >= $q)) {
                            $product['quantity']  = $q;
                            $product['model']     = $model;
                            $product['subtract']  = $q;
                            $product['price']     = $cednewegg_price;
                            $product['tax']       = 0;
                            $product['total'] = ($product['price']) ? ($q * $product['price']) + $product['tax'] : $q * $price + $product['tax'];
                            $product['reward']   = 0;
                            $product['name']     = $product_title;
                            $product['option']   = array();
                            $product['download'] = array();
                            return $product;
                        } else {
                            $this->orderErrorInformation($sku, $newegg_order_id, $worder_data, "REQUESTED QUANTITY FOR PRODUCT ID " . $product['product_id'] . " IS NOT AVAILABLE");

                            return array();
                        }
                    } else {
                        $this->orderErrorInformation($sku,$newegg_order_id, $worder_data, "PRODUCT STATUS IS DISABLED WITH ID " . $product['product_id'] . "");
                        return array();
                    }
                } else {
                    $this->orderErrorInformation($sku,$newegg_order_id, $worder_data, "PRODUCT ID" . $product['product_id'] . " DOES NOT EXIST");
                    return array();
                }
            } else {
                $this->orderErrorInformation($sku,$newegg_order_id, $worder_data, "MERCHANT SKU DOES NOT EXIST");
                return array();
            }
        }
    }

    public function orderErrorInformation($sku, $ordersn, $worder_data, $errormessage)
    {

        $sql_delete = "DELETE  FROM `" . DB_PREFIX . "cednewegg_order_error` WHERE `sku`='" . $sku . "' AND `newegg_order_id`='".$ordersn."'";
        $this->db->query($sql_delete);
        $sql_insert = "INSERT INTO `" . DB_PREFIX . "cednewegg_order_error` (`sku`,`newegg_order_id`,`order_data`,`reason`)VALUES('" . $this->db->escape($sku) . "','" . $ordersn . "','" . $this->db->escape(json_encode($worder_data)) . "','" . $errormessage . "')";
        $this->db->query($sql_insert);
    }

    public function getLocalizationDeatails($Statecode, $countryCode)
    {
        $query = $this->db->query("SELECT `country_id`,`name` FROM `" . DB_PREFIX . "country` WHERE `iso_code_3` LIKE '" . $countryCode . "'");
        if ($query->num_rows) {
            $country_id = 0;
            $country_name = '';
            if (isset($query->row['country_id']) && $query->row['country_id']) {
                $country_id = $query->row['country_id'];
                $country_name = $query->row['name'];
            }
            if ($country_id) {
                $query = $this->db->query("SELECT `zone_id`,`name` FROM " . DB_PREFIX . "zone WHERE country_id='" . $country_id . "' AND code='" . $Statecode . "'");
                if ($query->num_rows) {
                    if (isset($query->row['zone_id']) && isset($query->row['name'])) {
                        return array(
                            'country_id' => $country_id,
                            'zone_id' => $query->row['zone_id'],
                            'name' => $query->row['name'],
                            'country_name' => $country_name
                        );
                    };
                } else {
                    return array(
                        'country_id' => '',
                        'zone_id' => '',
                        'name' => '',
                        'country_name' => ''
                    );
                }
            } else {
                return array(
                    'country_id' => '',
                    'zone_id' => '',
                    'name' => '',
                    'country_name' => ''
                );
            }
        } else {
            return array(
                'country_id' => '',
                'zone_id' => '',
                'name' => '',
                'country_name' => ''
            );
        }
    }

    public function addOrder($data)
    {
    
        if (is_array($data) && count($data)) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET 
            invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "',
             store_id = '" . (int)$data['store_id'] . "',
             store_name = '" . $this->db->escape($data['store_name']) . "',
             store_url = '" . $this->db->escape($data['store_url']) . "',
             customer_id = '" . (int)$data['customer_id'] . "',
             customer_group_id = '" . (int)$data['customer_group_id'] . "', 
             firstname = '" . $this->db->escape($data['firstname']) . "', 
             lastname = '" . $this->db->escape($data['lastname']) . "', 
             email = '" . $this->db->escape($data['email']) . "', 
             telephone = '" . $this->db->escape($data['telephone']) . "', 
             fax = '" . $this->db->escape($data['fax']) . "', 
             payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', 
             payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', 
             payment_company = '" . $this->db->escape($data['payment_company']) . "',
             payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', 
             payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', 
             payment_city = '" . $this->db->escape($data['payment_city']) . "', 
             payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', 
             payment_country = '" . $this->db->escape($data['payment_country']) . "', 
             payment_country_id = '" . (int)$data['payment_country_id'] . "', 
             payment_zone = '" . $this->db->escape($data['payment_zone']) . "', 
             payment_zone_id = '" . (int)$data['payment_zone_id'] . "', 
             payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', 
             payment_method = '" . $this->db->escape($data['payment_method']) . "', 
             payment_code = '" . $this->db->escape($data['payment_code']) . "', 
             shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', 
             shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', 
             shipping_company = '" . $this->db->escape($data['shipping_company']) . "', 
             shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', 
             shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', 
             shipping_city = '" . $this->db->escape($data['shipping_city']) . "', 
             shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', 
             shipping_country = '" . $this->db->escape($data['shipping_country']) . "', 
             shipping_country_id = '" . (int)$data['shipping_country_id'] . "', 
             shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', 
             shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', 
             shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', 
             shipping_method = '" . $this->db->escape($data['shipping_method']) . "', 
             order_status_id = '" . $data['order_status'] . "', 
             shipping_code = '" . $this->db->escape($data['shipping_code']) . "', 
             comment = '" . $this->db->escape($data['comment']) . "', 
             affiliate_id  = '" . (int)$data['affiliate_id'] . "', 
             language_id = '" . (int)$this->config->get('config_language_id') . "', 
             currency_id = '" . (int)$data['currency_id'] . "', 
             currency_code = '" . $this->db->escape($data['currency_code']) . "', 
             currency_value = '" . (float)$data['currency_value'] . "', 
             date_added = NOW(), 
             date_modified = NOW()");

            $order_id = $this->db->getLastId();
            if($order_id) {
                // Products
                foreach ($data['products'] as $product) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET 
                order_id = '" . (int)$order_id . "', 
                product_id = '" . (int)$product['product_id'] . "', 
               `name` = '" . $this->db->escape($product['name']) . "', 
                model = '" . $this->db->escape($product['model']) . "', 
                quantity = '" . (int)$product['quantity'] . "', 
                price = '" . (float)$product['price'] . "', 
                total = '" . (float)$product['total'] . "', 
                tax = '" . (float)$product['tax'] . "', 
                reward = '" . (int)$product['reward'] . "'");

                    $order_product_id = $this->db->getLastId();

                    // for options mapping in product

                    if (isset($product['option']) && count($product['option']) > 0) {
                        foreach ($product['option'] as $option_id => $option_value) {
                            $sql = "SELECT pov.product_option_value_id,po.product_option_id,od.name, ovd.name as `value`,o.type 
FROM " . DB_PREFIX . "product_option_value pov 
LEFT JOIN " . DB_PREFIX . "product_option po ON (po.product_id=pov.product_id AND po.option_id=pov.option_id) 
LEFT JOIN " . DB_PREFIX . "option o ON (pov.option_id =o.option_id) 
LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id =ov.option_value_id) 
LEFT JOIN " . DB_PREFIX . "option_description od ON (pov.option_id =od.option_id) 
JOIN " . DB_PREFIX . "option_value_description ovd 
ON (pov.option_id =ovd.option_id AND pov.option_value_id=ovd.option_value_id) 
where ov.option_value_id =" . (int)$option_value . " AND ov.option_id =" . (int)$option_id . " AND pov.product_id=" . (int)$product['product_id'];
                            $options_data = $this->db->query($sql);
                            if ($options_data->num_rows) {
                                $option = $options_data->row;
                                $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
                            }
                        }
                    }
                }

                // Get the total
                $total = 0;
                if (isset($data['totals'])) {
                    foreach ($data['totals'] as $order_total) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET 
                    order_id = '" . (int)$order_id . "', 
                    code = '" . $this->db->escape($order_total['code']) . "', 
                    title = '" . $this->db->escape($order_total['title']) . "',
                   `value` = '" . (float)$order_total['value'] . "', 
                   sort_order = '" . (int)$order_total['sort_order'] . "'");
                        if ($order_total['code'] == 'total') {
                            $total += $order_total['value'];
                        }
                    }
                }
                // Update order total

                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET 
            total = '" . (float)$total . "' WHERE order_id = '" . (int)$order_id . "'");

                $newegg_order_id = $data['sorder_id'];
                $this->addOrderHistory($order_id, $newegg_order_id, (int)$data['order_status']);


                return $order_id;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function addOrderHistory($order_id, $newegg_order_id,$order_status_id, $comment = '', $notify = true)
    {
        $data = array();
        $data['order_status_id'] = (int)$order_status_id;
        $data['order_id'] = (int)$order_id;
        $data['comment'] = "NewEgg Order $newegg_order_id Imported Successfully";
        $data['notify'] = (int)$notify;
        // Stock subtraction
        $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        $ids_products = array();
        foreach ($order_product_query->rows as $order_product) {
            $result = $this->db->query("SELECT `quantity` FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . (int)$order_product['product_id'] . "'");
            if (!$this->config->get('cedebay_use_shipstation')) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "'");
                }
                if (isset($order_product['product_id']) && (int)$order_product['product_id'])
                    $ids_products[]=(int)$order_product['product_id'];
            }

        }

        if (!$this->config->get('cednewegg_use_shipstation'))

            if(!empty($ids_products)) {
                $productLib = new CedNeweggProduct($this->registry);
                $productLib->syncInventoryPrice($ids_products);
            }

//        $this->openbay->orderNew((int)$order_id);
        $order_info = $this->getOrder($order_id);
        if ($order_info) {

            // Update the DB with the new statuses
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($data['comment']) . "', date_added = NOW()");

            // If order status is 0 then becomes greater than 0 send main html email
            if ($order_status_id) {
                $language = new Language($order_info['language_directory']);
                $language->load($order_info['language_filename']);
                $language->load('mail/order');

                $subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);

                $message = $language->get('text_order') . ' ' . $order_id . "\n";
                $message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

                $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

                if ($order_status_query->num_rows) {
                    $message .= $language->get('text_order_status') . "\n";
                    $message .= $order_status_query->row['name'] . "\n\n";
                }

                if ($order_info['customer_id']) {
                    $message .= $language->get('text_link') . "\n";
                    $message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
                }

                if ($data['comment']) {
                    $message .= $language->get('text_comment') . "\n\n";
                    $message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
                }

                $message .= $language->get('text_footer');

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->partner_idname = $this->config->get('config_smtp_partner_idname');
                $mail->shop_idword = $this->config->get('config_smtp_shop_idword');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');
                $mail->setTo($order_info['email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($order_info['store_name']);
                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                $mail->send();


            }
        }
    }

    public function getOrder($order_id)
    {
        $order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

        if ($order_query->num_rows) {
            $reward = 0;

            $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

            foreach ($order_product_query->rows as $product) {
                $reward += $product['reward'];
            }

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

            if ($country_query->num_rows) {
                $payment_iso_code_2 = $country_query->row['iso_code_2'];
                $payment_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $payment_zone_code = $zone_query->row['code'];
            } else {
                $payment_zone_code = '';
            }

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

            if ($country_query->num_rows) {
                $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                $shipping_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $shipping_zone_code = $zone_query->row['code'];
            } else {
                $shipping_zone_code = '';
            }

            if ($order_query->row['affiliate_id']) {
                $affiliate_id = $order_query->row['affiliate_id'];
            } else {
                $affiliate_id = 0;
            }

            $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "language` WHERE language_id = '" . (int)$order_query->row['language_id'] . "'");

            if ($query && $query->num_rows) {
                $language_info = $query->row;

                if ($language_info) {
                    $language_code = $language_info['code'];
                    $language_filename = '';
                    $language_directory = $language_info['directory'];
                } else {
                    $language_code = '';
                    $language_filename = '';
                    $language_directory = '';
                }
            }


            return array(
                'order_id' => $order_query->row['order_id'],
                'invoice_no' => $order_query->row['invoice_no'],
                'invoice_prefix' => $order_query->row['invoice_prefix'],
                'store_id' => $order_query->row['store_id'],
                'store_name' => $order_query->row['store_name'],
                'store_url' => $order_query->row['store_url'],
                'customer_id' => $order_query->row['customer_id'],
                'customer' => $order_query->row['customer'],
                'customer_group_id' => $order_query->row['customer_group_id'],
                'firstname' => $order_query->row['firstname'],
                'lastname' => $order_query->row['lastname'],
                'telephone' => $order_query->row['telephone'],
                'fax' => $order_query->row['fax'],
                'email' => $order_query->row['email'],
                'payment_firstname' => $order_query->row['payment_firstname'],
                'payment_lastname' => $order_query->row['payment_lastname'],
                'payment_company' => $order_query->row['payment_company'],
                'payment_company_id' => isset($order_query->row['payment_company_id'])?$order_query->row['payment_company_id']: '',
                'payment_tax_id' => isset($order_query->row['payment_tax_id'])?$order_query->row['payment_tax_id']: '',
                'payment_address_1' => $order_query->row['payment_address_1'],
                'payment_address_2' => $order_query->row['payment_address_2'],
                'payment_postcode' => $order_query->row['payment_postcode'],
                'payment_city' => $order_query->row['payment_city'],
                'payment_zone_id' => $order_query->row['payment_zone_id'],
                'payment_zone' => $order_query->row['payment_zone'],
                'payment_zone_code' => $payment_zone_code,
                'payment_country_id' => $order_query->row['payment_country_id'],
                'payment_country' => $order_query->row['payment_country'],
                'payment_iso_code_2' => $payment_iso_code_2,
                'payment_iso_code_3' => $payment_iso_code_3,
                'payment_address_format' => $order_query->row['payment_address_format'],
                'payment_method' => $order_query->row['payment_method'],
                'payment_code' => $order_query->row['payment_code'],
                'shipping_firstname' => $order_query->row['shipping_firstname'],
                'shipping_lastname' => $order_query->row['shipping_lastname'],
                'shipping_company' => $order_query->row['shipping_company'],
                'shipping_address_1' => $order_query->row['shipping_address_1'],
                'shipping_address_2' => $order_query->row['shipping_address_2'],
                'shipping_postcode' => $order_query->row['shipping_postcode'],
                'shipping_city' => $order_query->row['shipping_city'],
                'shipping_zone_id' => $order_query->row['shipping_zone_id'],
                'shipping_zone' => $order_query->row['shipping_zone'],
                'shipping_zone_code' => $shipping_zone_code,
                'shipping_country_id' => $order_query->row['shipping_country_id'],
                'shipping_country' => $order_query->row['shipping_country'],
                'shipping_iso_code_2' => $shipping_iso_code_2,
                'shipping_iso_code_3' => $shipping_iso_code_3,
                'shipping_address_format' => $order_query->row['shipping_address_format'],
                'shipping_method' => $order_query->row['shipping_method'],
                'shipping_code' => $order_query->row['shipping_code'],
                'comment' => $order_query->row['comment'],
                'total' => $order_query->row['total'],
                'reward' => $reward,
                'order_status_id' => $order_query->row['order_status_id'],
                'commission' => $order_query->row['commission'],
                'language_id' => $order_query->row['language_id'],
                'language_code' => $language_code,
                'language_filename' => $language_filename,
                'language_directory' => $language_directory,
                'currency_id' => $order_query->row['currency_id'],
                'currency_code' => $order_query->row['currency_code'],
                'currency_value' => $order_query->row['currency_value'],
                'ip' => $order_query->row['ip'],
                'forwarded_ip' => $order_query->row['forwarded_ip'],
                'partner_id_agent' => isset($order_query->row['partner_id_agent'])?$order_query->row['partner_id_agent']: '',
                'accept_language' => $order_query->row['accept_language'],
                'date_added' => $order_query->row['date_added'],
                'date_modified' => $order_query->row['date_modified']
            );
        } else {
            return false;
        }
    }

    public function getOrders()
    {
        $token = $this->config->get('cednewegg_token');
        $fetch_from = $this->config->get('cednewegg_order_from');
        $status = $this->config->get('cednewegg_order_status');
        $numberOFDays = (int)$fetch_from;
        $orderFrom = gmdate("Y-m-d\TH:i:s", strtotime("-" . (int)$numberOFDays . " days"));
        $orderTo     = gmdate("Y-m-d\TH:i:s");
        $variable    = "GetOrders";
        $requestBody = '';

        $response    = '1'; //$this->cednewegg_helper->sendHttpRequest($requestBody, $variable);
        if($response){ 
        $orderData = json_decode('{"SellerID":"TEST","OrderNumber":375289814,"InvoiceNumber":0,"OrderDownloaded":false,"OrderDate":"02\/26\/2023 09:51:50","OrderStatus":0,"OrderStatusDescription":"Unshipped","CustomerName":"David mailto:crossen","customerphonenumber":"859-327-4684","customeremailaddress":"cusa.q5zw1g5erfut9jkm@test.newegg.com","ShipToAddress1":"465 Rose St","ShipToAddress2":"Rm 202","ShipToCityName":"Lexington","ShipToStateCode":"KY","ShipToZipCode":"40508-3311","ShipToCountryCode":"UNITED STATES","ShipService":"Standard Shipping (5-7 business days)","ShipToFirstName":"David","ShipToLastName":"Crossen","ShipToCompany":"University of Kentucky - College of Fine Arts","OrderItemAmount":787.55,"ShippingAmount":0,"DiscountAmount":0,"RefundAmount":0,"SalesTax":0,"OrderTotalAmount":787.55,"OrderQty":1,"IsAutoVoid":false,"SalesChannel":0,"FulfillmentOption":0,"ItemInfoList":[{"SellerPartNumber":"78787878787878","NeweggItemNumber":"9SIAHPGJ8E6300","MfrPartNumber":"USW-Enterprise-8-KE","UPCCode":"690125236923","Description":"Ubiquiti Switch Enterprise 8 PoE | 8-Port Managed Layer 3 Multi-Gigabit PoE Switch USW-Enterprise-8-PoE","OrderedQty":1,"ShippedQty":0,"UnitPrice":787.55,"ExtendUnitPrice":787.55,"ExtendShippingCharge":0,"ExtendSalesTax":0,"Status":1,"StatusDescription":"Unshipped"}],"PackageInfoList":[]}',true);
        //print_r($orderData); die();
            return array(
                'success' => true,
                'message' => $orderData
            );
        } else {
            $errors = array();
            $errors[] = "No New Orders found ";
            $errs = array();

            if (!isset($data['GetOrdersResponse']['Errors']['0'])
                && isset($data['GetOrdersResponse']['Errors']['ShortMessage'])) {
                $singleError = $data['GetOrdersResponse']['Errors'];
                $data['GetOrdersResponse']['Errors'][] = $singleError;
                $errs = $data['GetOrdersResponse']['Errors'];
            }
            if (!empty($errs)) {
                foreach ($errs as $err) {
                    if (isset($err['ShortMessage'])) {
                        $errors[] = $err['ShortMessage'];
                    }
                    if (isset($err['LongMessage'])) {
                        $errors[] = $err['LongMessage'];
                    }
                }
            }
            return array(
                'success' => false,
                'message' => $errors
            );
        }
    }

}