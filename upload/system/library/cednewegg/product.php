<?php
include_once(DIR_SYSTEM . 'library/cednewegg.php');

class CedNewEggProduct
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
    protected $cednewegg_helper;

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
        $this->cednewegg_helper = new Cednewegg($this->registry);
    }


 public function cron_tab_update($msg)
    {  
        $this->db->query("INSERT INTO " . DB_PREFIX . "cednewegg_cron SET 
        job_code    = 'NewEgg Product Sync Inventry & Price', 
        created_at  = CURRENT_TIMESTAMP,
        finished_at = CURRENT_TIMESTAMP,
        message     = '".$msg."',
        status      = '0'");
        return true;
    }
    
    public function updateAllInventoryPrices()
    {
        $product_lib = new $this->registry;
        $response = array();
        $ids = array();
        $query = $this->db->query("SELECT `product_id` FROM `".DB_PREFIX."cednewegg_profile_products`");
        if($query->num_rows) {
            foreach ($query->rows as $r) {
                $ids[] = $r['product_id'];
            }
        }
        if(!empty($ids)) {
            //$response[] = $this->syncInventoryPrice($ids);
            $response[] = $this->syncall($ids);
        } else {
            $response = "No Product Ids found to updated";
            $this->cron_tab_update($response);
        }
        return $response;
    }


    public function getPrice($price)
    {
        $finalPrice = $price;
        $variation_type = $this->config->get('cednewegg_price_variation_value');
        $variation_value = (float)$this->config->get('cednewegg_price_variation_type');
        switch ($variation_type) {
            case 'increase_by_fixed':
                $finalPrice = $finalPrice + $variation_value;
                break;
            case 'decrease_by_fixed':
                $finalPrice = $finalPrice - $variation_value;
                break;
            case 'increase_by_percentage':
                $finalPrice = $finalPrice + (($finalPrice*$variation_value)/100);
                break;
            case 'decrease_by_percentage':
                $finalPrice = $finalPrice - (($finalPrice*$variation_value)/100);
                break;
        }
        $finalPrice = (float)number_format($finalPrice,'2','.','');
        return $finalPrice;
    }

    
     public function syncall($product_ids)
    {  

       try { 
        if(!empty($product_ids)){
            $data_acc= $this->account_details('cednewegg');
        if (isset($data_acc)){        
                $data_acc;
            }
        $result= $this->update_inventory($product_ids); 

        $upload_product = $this->update_price_data($result,$data_acc,'Inventory');
                if(isset($upload_product['0']['Message'])){
                    $this->error['warning'] = $upload_product['0']['Message'];
                }
        foreach($product_ids as $prodid){
                $this->update_product_queue($prodid,$upload_product);
            }   
        $result_price = $this->update_price($product_ids); 
        //print_r($result_price); die();
        $upload_products = $this->update_price_data($result_price,$data_acc,'Price');
                if(isset($upload_products['ResponseBody']['RequestList']['0'])) {
                    $success = $upload_products['ResponseBody']['RequestList']['0']['RequestId'];
                    $this->cron_tab_update($success);
                    return $error;
            }
        foreach($product_ids as $prodid){
                $this->update_product_queue($prodid,$upload_product);
            }   

        if (isset($upload_products['IsSuccess'])) {
        $RequestId = $upload_products['ResponseBody']['ResponseList']['0']['RequestId'];
        $this->session->data['success'] =  $RequestId. '</br>' . 'Upload Successfully';
            }
            //print_r($RequestId); die();
            $success = "Feed Submited ".$RequestId;
            $this->cron_tab_update($success);
            return $success;
        } else {
                $error ="No Products Found In Profile";
                $this->cron_tab_update($error);
                return $error;
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

    }



     public function update_inventory($productdata)
      {
        $product_data['NeweggEnvelope']=[];
        $product_data['NeweggEnvelope']['Header'] = ["DocumentVersion" => 2.0];
        $product_data['NeweggEnvelope']['MessageType'] = 'Inventory';

       $products=[];
       foreach($productdata as $key=>$ids){

        $data = $this->getProductById($ids);
        //print_r($data); die();
        $profile_id = $this->getProductInfo($ids);
         
    if(!empty($data['ean'])){ 
        $productdata=array(
              "SellerPartNumber"        =>  $data['ean'],
              "Shipping"                =>  "Default",
              "Inventory"               =>  number_format((float)$data['quantity'], 0, '.', ''),
          );
        $product[]=$productdata;
      }
    }
    $product_data['NeweggEnvelope']['Message']['Inventory']['Item']= $product;
    return $product_data;
  }

   public function update_price($productdata)
      {
        $error='';
        $product_data['NeweggEnvelope']=[];
        $product_data['NeweggEnvelope']['Header'] = ["DocumentVersion" => 2.0];
        $product_data['NeweggEnvelope']['MessageType'] = 'Price';
        $product=[];
       foreach($productdata as $key=>$ids){
        $data = $this->getProductById($ids);
        if(empty($data['ean'])){ 
            $this->invalid_product($ids); 
            continue;
        }
        //print_r($data['shipping']); die();
        $profile_id = $this->getProductInfo($ids);
         if($profile_id['newegg_validation_error']=='Invalid'){
           $this->invalid_product($ids); 
            continue;
         }
     if($data['shipping']=='1'){ $shipping = "Default"; }else{ $shipping = "Free"; }
       if(!empty($data['ean'])){ 
        $productdata=array(
              "SellerPartNumber"        =>  $data['ean'],
              "CountryCode"             =>  "USA",
              "Currency"                =>  "USD",
              "Shipping"                =>  $shipping,
              "SellingPrice"            =>  $this->getPrice($data['price']),
          );
        $product[]=$productdata;
       }  
     }
     $product_data['NeweggEnvelope']['Message']['Price']['Item']= $product;
     return $product_data;
    }

    public function getProductById($product_id) {
        $product_info = $this->getProduct($product_id);
        return $product_info;
    }

    public function getProduct($product_id) {
    $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

    return $query->row;
    }

    public function getProductInfo($product_id)
    {
        $data = array();
        $psql = "SELECT * FROM `".DB_PREFIX."cednewegg_profile_products` WHERE `product_id`=".(int)$product_id;
        $pquery = $this->db->query($psql);
        if($pquery->num_rows) {
            $data = $pquery->row;
            if(isset($data['data']) && is_array(json_decode($data['profile_req_opt_attribute'], true))) {
                $data['attribute_values'] = json_decode($data['data'], true);
            } else {
                $data['attribute_values'] = array();
            }
        }
        return $data;
    }

    public function invalid_product($ids)
    {
           $this->db->query("UPDATE " . DB_PREFIX . "cednewegg_profile_products SET 
            newegg_status = 'Invalid'
            WHERE product_id = '" . (int)$ids . "'");
    }

    public function account_details($key){
        $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_accounts WHERE `id` = '1' ");
        if($result->num_rows > 0){
            return $result->row;
        }
    }

    public function getAllneweggProductIds() 
    {
        $product_ids = array();
        $query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "cednewegg_profile_products`");
        if ($query && $query->num_rows) {
            foreach ($query->rows as $key => $value) {
                if (isset($value['product_id']) && $value['product_id']) {
                    $product_ids[] = $value['product_id'];
                }
            }
        }
        return $product_ids;
    }

    public function update_price_data($value,$data_acc,$type)
    {
        // echo '<pre>';
        // print_r($type);
        // die('u8');
        if ($type == 'Inventory'){
            $type='INVENTORY_DATA';
            $url = 'datafeedmgmt/feeds/submitfeed';
            $jsonResponse = $this->postRequest($url,$data_acc,$value,$type);
        }else{
            $url = 'datafeedmgmt/feeds/submitfeed';
            $type="PRICE_DATA";
            $jsonResponse = $this->postRequest($url,$data_acc,$value,$type);
        } 
        $response = json_decode($jsonResponse, true);
        //print_r($response); die();
        return $response;
    }

      public function update_product_queue($id,$data)
    { 
       
         if(isset($data['0']['Message'])){
            $Message = $data['0']['Message'];
            if(isset($data['0']['Message']['Result']['ErrorList']['ErrorDescription'])){
             $Message = $data['0']['Message']['Result']['ErrorList']['ErrorDescription'];
            }

            $sql = 'UPDATE ' . DB_PREFIX . 'cednewegg_profile_products SET 
            newegg_feed_error    = "'.json_encode($Message).'",
            newegg_status        = "Invalid"
            WHERE product_id = "' . (int)$id. '"';
            $this->db->query($sql);
        }else{
            if(isset($data['ResponseBody']['ResponseList']['0']['RequestId'])){  
                $Message = $data['ResponseBody']['ResponseList']['0']['RequestId'];
            $this->db->query("UPDATE " . DB_PREFIX . "cednewegg_profile_products SET 
            Request_id      = '".$Message."',
            newegg_status   = 'Sync Status'
            WHERE product_id = '" . (int)$id. "'");
          }
        }
        
        return true;
       }


    public function postRequest($url,$currentAccount,$body,$type='')
    {
        if(empty($type)){
            $type = 'ITEM_DATA';
        }
        $currentAccountDetail = $this->getAccountDetail($currentAccount['id']);
    if (is_array($currentAccountDetail) && !empty($currentAccountDetail)) {
        $url = $currentAccountDetail['url'] . $url . "?sellerid=" . $currentAccountDetail['seller_id'] .'&requesttype='. $type;
        $headers = array();
        $headers[] = "Authorization: " . trim($currentAccountDetail['authorization_key']);
        $headers[] = "SecretKey: " . trim($currentAccountDetail['secret_key']);
        $headers[] = "Content-Type: application/json";
        $headers[] = "Accept: application/json";
        
        //print_r(json_encode($body)); die();
        if (isset($body)) {
                $putString = stripslashes(json_encode($body));
                $putData = tmpfile();
                fwrite($putData, $putString);
                fseek($putData, 0);
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (isset($body)) {
                // curl_setopt($ch, CURLOPT_PUT, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                curl_setopt($ch, CURLOPT_INFILE, $putData);
                curl_setopt($ch, CURLOPT_INFILESIZE, strlen($putString));
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
        }
       //print_r($server_output); die;
      // $response = $this->product_status($server_output,$currentAccount,); die();
        return $this->formatJson($server_output);
    }

    public function getProfileIdByProductId($product_id)
    {
        $query = $this->db->query("SELECT `cednewegg_profile_id` FROM " . DB_PREFIX . "cednewegg_profile_products WHERE product_id = '" . (int)$product_id . "'");
       
        if ($query->row && isset($query->row['cednewegg_profile_id']) && !empty($query->row['cednewegg_profile_id'])) {
            return $query->row['cednewegg_profile_id'];
        }
        return false;
    }
     public function getAccountDetail($account)
    {  
        try {  
            if (isset($account)) {
              //  print_r($account['authorization_key']); die();
               $accountDetail=[];
                $country = '1'; //$account['account_location'];
                if(isset($account['seller_id'])){
                 $seller_id = $account['seller_id'];
                }else{
                   $seller_id = 'ABYU';
                }

                if(isset($account['secret_key'])){
                 $secret_key = $account['secret_key'];
                }else{
                   $secret_key = '24dbc5f5-ac3e-48b0-92d7-eafb98b20747';
                }

                if(isset($account['authorization_key'])){
                 $authorization_key = $account['authorization_key'];
                }else{
                   $authorization_key = '29bb526b1caeb49ac4e90a8bed288c3f';
                }

                $accountDetail['seller_id'] = $seller_id;
                $accountDetail['secret_key'] = $secret_key;
                $accountDetail['authorization_key'] = $authorization_key;
                switch ($country) {
                    case 1:
                        //$accountDetail['url'] = "https://api.newegg.com/marketplace/b2b/";
                       $accountDetail['url'] = "https://api.newegg.com/marketplace/";
                        break;
                    case 2:
                        $accountDetail['url'] = "https://api.newegg.com/marketplace/can/";
                        break;
                       
                    default:
                        $accountDetail['url'] = "https://api.newegg.com/marketplace/";
                        break;

                }
              //print_r($accountDetail); die();
                return $accountDetail;
            }
        } catch (\Exception $e) {
            $messages['error'] = $e->getMessage();
            //print_r($messages['error']); die();
            $this->logger->addError($messages['error'], ['path' => __METHOD__]);
        }
    }
     function formatJson($json_data)
    {
        for ($i = 0; $i <= 31; ++$i) {
            $json_data = str_replace(chr($i), "", $json_data);
        }
        $json_data = str_replace(chr(127), "", $json_data);
        if (0 === strpos(bin2hex($json_data), 'efbbbf')) {
            $json_data = substr($json_data, 3);
        }
        //sprint_r($json_data); die('ioio');
        return $json_data;
    }


}