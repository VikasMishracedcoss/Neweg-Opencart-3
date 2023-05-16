<?php

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
 * @category    Ced
 * @package     Ced_NewEgg
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright © 2019 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

class CedNewegg
{
    private   $db;
    private   $session;
    private   $config;
    private   $currency;
    private   $request;
    private   $weight;
    protected $api_url = '';
    protected $api_key = '';
    protected $timestamp;
    protected $compatLevel;

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
        $this->db = $registry->get('db');
        $this->session = $registry->get('session');
        $this->config = $registry->get('config');
        $this->currency = $registry->get('currency');
        $this->request = $registry->get('request');
        $this->weight = $registry->get('weight');
        $this->openbay = $registry->get('openbay');
        $this->timestamp = time();
        $this->compatLevel = 991;
    }

    public function isCedNeweggInstalled()
    {
        if ($this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cednewegg_description_templates'")->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    public function installCedNewegg()
    {
        $cednewegg_product_sycn = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'cednewegg_product_sycn` (
            `id` int(15) NOT NULL auto_increment,
            `OperationType` varchar(250) NOT NULL,
            `RequestId` varchar(250) NOT NULL,
            PRIMARY KEY (`id`)
              )';
        $created = $this->db->query($cednewegg_product_sycn);
        if ($created)
            $this->log("cednewegg_product_sycn table created", 6, true);
            $cednewegg_profile_attribute = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'cednewegg_profile_attribute` (
            `id` int(15) NOT NULL auto_increment,
            `profile_id` varchar(250) NOT NULL,
            `profile_attr` longtext,
            `required_attribute` longtext,
            `subattr` varchar(250) NOT NULL,
            PRIMARY KEY (`id`)
              )';
        $created = $this->db->query($cednewegg_profile_attribute);
        if ($created)
            $this->log("cednewegg_profile_attribute table created", 6, true);

            $cednewegg_profile = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."cednewegg_profile`(
            `id` int(11) NOT NULL auto_increment,
            `profile_code` varchar(50) NOT NULL,
            `profile_name` varchar(50) NOT NULL,
            `status` varchar(20) NOT NULL,
            `profile_category` text NOT NULL DEFAULT '',
            `profile_category_name` varchar(250) NOT NULL,
            `profile_cat_attribute` text NOT NULL DEFAULT '',
            `profile_req_opt_attribute` text NOT NULL DEFAULT '',
            `profile_varient_attribute` text NOT NULL DEFAULT '',
            `varient_attr_name` text NOT NULL DEFAULT '',
            `account_id` int(11) NOT NULL DEFAULT ''
            `store_id` text NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
        )";

        $created = $this->db->query($cednewegg_profile);
        if ($created)
            $this->log("cednewegg_profile table created", 6, true);

        $cednewegg_products = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'cednewegg_products` (
            `id` int(15) NOT NULL auto_increment,
            `id_product` int(15) NOT NULL,
            `ebay_item_id` text NOT NULL,
            `ebay_status` varchar(50) NOT NULL,
            `product_error` text NOT NULL,
            `data` text NOT NULL,
            PRIMARY KEY (`id`)
              )';
        $created = $this->db->query($cednewegg_products);
        if ($created)
            $this->log("cednewegg_products table created", 6, true);


        $cednewegg_order = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'cednewegg_order` (
            `id` int(15) NOT NULL auto_increment,
            `opnecart_orderid` int(15),
            `newegg_order_id` text COLLATE utf8_unicode_ci,
            `order_place_date` varchar(250) COLLATE utf8_unicode_ci,
            `order_data` text COLLATE utf8_unicode_ci,
            `newegg_failed_reason` varchar(250) COLLATE utf8mb4_0900_ai_ci,
            `newegg_order_status` varchar(100) COLLATE utf8_unicode_ci,
            PRIMARY KEY (`id`)
              )';

        $created = $this->db->query($cednewegg_order);
        if ($created)
            $this->log("cednewegg_order table created", 6, true);

        $cednewegg_accounts = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'cednewegg_accounts` (
            `id` int(15) NOT NULL auto_increment,
            `account_title` varchar(255) NULL,
            `account_code` varchar(255) NULL,
            `account_location` varchar(50) NULL,
            `account_store` varchar(50) NULL,
            `account_status` tinyint(1) NULL,
            `root_cat` varchar(250) NULL,
            `seller_id` text NULL,
            `secret_key` text NULL,
            `authorization_key` text NULL,
            `warehouse_location` text NULL,
            PRIMARY KEY (`id`)
              )';
        $created = $this->db->query($cednewegg_accounts);
        if ($created)
            $this->log("cednewegg_accounts table created", 6, true);



        $cednewegg_profile_products = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cednewegg_profile_products` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
          `cednewegg_profile_id` int(11) NOT NULL,
          `account_id` varchar(55),
          `store_id` varchar(55),
          `newegg_validation` longtext,
          `newegg_validation_error` longtext,
          `newegg_status` varchar(55),
          `product_id` int(11) NOT NULL,
          `Request_id` text,
          PRIMARY KEY (`id`)
          )";

        $created = $this->db->query($cednewegg_profile_products);
        if ($created)
            $this->log("cednewegg_profile_products table created", 6, true);

        $cednewegg_products_chunk = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cednewegg_products_chunk` (
            `id` int(15) NOT NULL auto_increment,
            `key` varchar(255) NOT NULL,
            `values` longtext,
            PRIMARY KEY (`id`)
              )";

        $created = $this->db->query($cednewegg_products_chunk);
        if ($created)
            $this->log("cednewegg_products_chunk table created", 6, true);

        $cednewegg_specifics = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'cednewegg_specifics` (
            `id` int(15) NOT NULL auto_increment,
            `site_id` varchar(50),
            `key` varchar(255) NOT NULL,
            `value` longtext,
            PRIMARY KEY (`id`)
              )';

        $created = $this->db->query($cednewegg_specifics);
        if ($created)
            $this->log("cednewegg_specifics table created", 6, true);

        $cednewegg_logs = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cednewegg_logs` (
            `id` int(15) NOT NULL AUTO_INCREMENT,
            `method` text NOT NULL,   
            `type` varchar(150) NOT NULL,
            `message` text NOT NULL,   
            `data` longtext NOT NULL,   
            `created_at` datetime,   
            PRIMARY KEY (`id`) 
            );";
        $created = $this->db->query($cednewegg_logs);
        if ($created)
            $this->log("cednewegg_logs table created", 6, true);

    }

    public function log($method = '', $type = '', $message = '', $response = '', $force_log = false)
    {

        $created_at = date('Y-m-d H:i:s');
        if ($this->config->get('cednewegg_debug_mode') == '1' || $force_log == true) {
            $sql = "INSERT INTO `" . DB_PREFIX . "cednewegg_logs` (
         `method`,
         `type`,
          `message`,
          `data`,
           `created_at`
            ) VALUES (
             '" . $this->db->escape($method) . "',
             '" . $this->db->escape($type) . "',
             '" . $this->db->escape($message) . "',
              '" . $this->db->escape(json_encode($response)) . "', 
              '" . $this->db->escape($created_at) . "'
              )";
            $this->db->query($sql);
        }
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $flag = false;
        if ($this->config->get('cednewegg_status')) {
            $flag = true;
        }
        return $flag;
    }

    public function sendHttpRequest($request_body, $variable)
    {
        try {
            $mode = $this->config->get('cednewegg_mode');
            $use_ced_details = $this->config->get('cednewegg_status');
            if($use_ced_details == '1') {

                $app_id = $this->config->get('cednewegg_auth_key');
                $dev_id = $this->config->get('cednewegg_auth_key');
                $cert_id = $this->config->get('cednewegg_auth_key');
                $runame = $this->config->get('cednewegg_auth_key');
           }
            
            $headers = array(
                'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,
                'X-EBAY-API-DEV-NAME: ' . $dev_id,
                'X-EBAY-API-APP-NAME: ' . $app_id,
                'X-EBAY-API-CERT-NAME: ' . $cert_id,
                'X-EBAY-API-CALL-NAME: ' . $variable,
                /*'X-EBAY-API-SITEID: ' . $site_id,*/
                'X-EBAY-API-DETAIL-LEVEL: 0'
            );
            if ($mode == "sandbox") {
                $serverUrl = 'https://api.newegg.com/marketplace/';
            } else {
                $serverUrl = 'https://api.newegg.com/marketplace/';
            }

            $connection = curl_init();
            curl_setopt($connection, CURLOPT_URL, $serverUrl);
            curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($connection, CURLOPT_POST, 1);
            curl_setopt($connection, CURLOPT_POSTFIELDS, $request_body);
            curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
            $responseXml = curl_exec($connection);
            curl_close($connection);
            $params = $this->xml2array($request_body);
            $response = $this->xml2array($responseXml);
            $this->log(
                __METHOD__,
                'Info',
                'NewEgg Api Request For '.$variable,
                json_encode(array(
                    'Request' => $headers,
                    'Param' => $params,
                    'Response' => $response,
                ))
            );
            return $responseXml;
        } catch (\Exception $e) {
            $this->log(
                __METHOD__,
                'Exception',
                $e->getMessage(),
                json_encode(array(
                    'Request' => $headers,
                    'Message' => $e->getMessage(),
                ))
            );
        }
    }

    public function getSessionId()
    {
        $mode = $this->config->get('cednewegg_mode');
        $use_ced_details = $this->config->get('cednewegg_use_ced');
        if($use_ced_details == 'yes') {
            if ($mode == 'sandbox') {
                $runame = 'CEDCOMMERCE-CEDCOMME-cedcom-irykz';
            } else {
                $runame = trim('CEDCOMMERCE-CEDCOMME-cedcom-oatipb');
            }
        } else {
            $runame = $this->config->get('cednewegg_runame');
        }
        $compatLevel = $this->compatLevel;
        $variable = "GetSessionID";
        $requestBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestBody .= '<GetSessionIDRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestBody .= "<Version>$compatLevel</Version>";
        $requestBody .= "<RuName>$runame</RuName>";
        $requestBody .= '</GetSessionIDRequest>';
        $response = $this->sendHttpRequest($requestBody, $variable);
        $data = $this->xml2array($response);
        if (isset($data['GetSessionIDResponse']['Ack'])
            && $data['GetSessionIDResponse']['Ack'] == 'Success') {
            $sessionId = $data['GetSessionIDResponse']['SessionID'];
            $result = array(
                'success' => true,
                'message' => $sessionId
            );
        } else {
            $message = 'Failed to get Session Id';
            if (isset($data['GetSessionIDResponse']['Errors']['ShortMessage'])
                && !empty($data['GetSessionIDResponse']['Errors']['ShortMessage'])) {
                $message = $data['GetSessionIDResponse']['Errors']['ShortMessage'].
                    ' '.$data['GetSessionIDResponse']['Errors']['LongMessage'];
            }
            $result = array(
                'success' => false,
                'message' => $message
            );
        }
        return $result;
    }

    public function getEbayToken()
    {
        $session_id = $this->config->get('cednewegg_session_id');
        $variable = "FetchToken";
        $requestBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestBody .= '<FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestBody .= "<SessionID>$session_id</SessionID>";
        $requestBody .= '</FetchTokenRequest>';
        $response = $this->sendHttpRequest($requestBody, $variable);
        $data = $this->xml2array($response);
        if (isset($data['FetchTokenResponse']['Ack'])
            && $data['FetchTokenResponse']['Ack'] == 'Success') {
            $ebayToken = $data['FetchTokenResponse']['eBayAuthToken'];
            $result = array(
                'success' => true,
                'message' => $ebayToken
            );
        } else {
            $message = 'Failed to fetch Token';
            if (isset($data['FetchTokenResponse']['Errors']['ShortMessage'])
                && !empty($data['FetchTokenResponse']['Errors']['ShortMessage'])) {
                $message = $data['FetchTokenResponse']['Errors']['ShortMessage'].
                    ' '.$data['FetchTokenResponse']['Errors']['LongMessage'];
            }
            $result = array(
                'success' => false,
                'message' => $message
            );
        }
        return $result;
    }


    public function xml2array($contents, $get_attributes = 1, $priority = 'tag')
    {
        if (!$contents) {
            return array();
        }
        if (is_array($contents)) {
            return array();
        }
        if (!function_exists('xml_parser_create')) {
            // print "'xml_parser_create()' function not found!";
            return array();
        }
        // Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); // http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        if (!$xml_values) {
            return ''; //Hmm...
        }
        // Initializations
        $xml_array = array();
//        $parents = array();
//        $opened_tags = array();
//        $arr = array();
        $current = &$xml_array; //Refference
        // Go through the tags.
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
        $attributes = array();
        $value = array();
        $parent = array();
        $type = '';
        $level = '';
        $tag = '';
        foreach ($xml_values as $data) {
            unset($attributes, $value); //Remove existing values, or there will be trouble
            // This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data); //We could use the array by itself, but this cooler.
            $result = array();
            $attributes_data = array();
            if (isset($value)) {
                if ($priority == 'tag') {
                    $result = $value;
                } else {
                    $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
                }
            }
            // Set the attributes too.
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($attr == 'ResStatus') {
                        $current[$attr][] = $val;
                    }
                    if ($priority == 'tag') {
                        $attributes_data[$attr] = $val;
                    } else {
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                    }
                }
            }
            // See tag status and do the needed.
            //echo"<br/> Type:".$type;
            if ($type == "open") { //The starting of the tag '<tag>'
                $parent[$level - 1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributes_data) {
                        $current[$tag . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    $current = &$current[$tag];
                } else { //There was another element with the same tag name
                    if (isset($current[$tag][0])) { //If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else { //This section will make the value an array if multiple tags with
                        // the same name appear together
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        ); //This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag . '_' . $level] = 2;
                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th)
                            // tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
                // See if the key is already taken.
                if (!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data) {
                        $current[$tag . '_attr'] = $attributes_data;
                    }
                } else { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag])) { //If it is already an array...
                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else { //If it is not an array...
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        ); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th)
                                // tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }
                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            } elseif ($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level - 1];
            }
        }
        return ($xml_array);
    }
    public function getNeweggLocations()
    {
        return array(
            array('label' => 'US', 'value' => '0'),

            array('label' => 'Australia', 'value' => 15),

            array('label' => 'Canada', 'value' => 2),

            array('label' => 'India', 'value' => 203),

            array('label' => 'Italy', 'value' => 101),

            array('label' => 'CanadaFrench', 'value' => 210),

            array('label' => 'HongKong', 'value' => 201),

            array('label' => 'Malaysia', 'value' => 207),

            array('label' => 'Philippines', 'value' => 211),

            array('label' => 'Singapore', 'value' => 216),

            array('label' => 'UK', 'value' => 3),

            array('label' => 'Austria', 'value' => 16),

            array('label' => 'Belgium Dutch', 'value' => 123),

            array('label' => 'Belgium French', 'value' => 23),

            array('label' => 'France', 'value' => 71),

            array('label' => 'Germany', 'value' => 77),

            array('label' => 'Ireland', 'value' => 205),

            array('label' => 'Netherlands', 'value' => 146),

            array('label' => 'Poland', 'value' => 212),

            array('label' => 'Spain', 'value' => 186),

            array('label' => 'Switzerland', 'value' => 193),

            array('label' => 'UK', 'value' => 3)
        );
    }

}