<?php
class ModelExtensionModuleCedNewEggProduct extends Model {

    public function getProducts($data = array()) {
        $sql = "SELECT cep.*,ceprof.*,p.*,p.product_id as product_id, pd.* FROM `" . DB_PREFIX . "product` p 
        LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) 
        JOIN `" . DB_PREFIX . "cednewegg_profile_products` cpp ON (p.product_id = cpp.product_id) 
        JOIN `".DB_PREFIX."cednewegg_profile` ceprof ON (ceprof.id = cpp.cednewegg_profile_id) 
        LEFT JOIN `".DB_PREFIX."cednewegg_profile_products` cep ON (cep.product_id = p.product_id)";

       
        $sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape(trim($data['filter_name'])) . "%'";
        }

        if (!empty($data['filter_profile_name'])){
            $sql .= 'AND ceprof.id = '.(int)$data['filter_profile_name'];
        }


        if (!empty($data['filter_cednewegg_status'])) {
            $sql .= " AND cpp.newegg_status LIKE '" . $this->db->escape(trim($data['filter_cednewegg_status'])) . "%'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '" . $this->db->escape(trim($data['filter_model'])) . "%'";
        }

        if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        if (isset($data['filter_newegg_item_id']) && !is_null($data['filter_newegg_item_id'])) {
            $sql .= " AND cep.newegg_item_id = '" . $data['filter_newegg_item_id'] . "'";
        }
        if (isset($data['filter_newegg_status']) && !is_null($data['filter_newegg_status'])) {
            $sql .= " AND cep.newegg_status = '" . $data['filter_newegg_status'] . "'";
        }

        if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
            if ($data['filter_image'] == 1) {
                $sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
            } else {
                $sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
            }
        }

        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.price',
            'p.quantity',
            'p.status',
            'p.sort_order',
            'cep.newegg_validation'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY pd.name";
        }
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        /*if ($_SERVER['REMOTE_ADDR'] == '103.97.184.162') {
            print_r($this->db->query($sql));die;
        }*/
//      print_r($sql);
        $query = $this->db->query($sql);
       //echo '<pre>'; print_r($query->rows); die;
        return $query->rows;
    }

    public function getProductOptions($product_id) {
        $product_option_data = array();

    $product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        foreach ($product_option_query->rows as $product_option) {
            $product_option_value_data = array();

            $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");

            foreach ($product_option_value_query->rows as $product_option_value) {
                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'option_value_id'         => $product_option_value['option_value_id'],
                    'quantity'                => $product_option_value['quantity'],
                    'subtract'                => $product_option_value['subtract'],
                    'price'                   => $product_option_value['price'],
                    'price_prefix'            => $product_option_value['price_prefix'],
                    'points'                  => $product_option_value['points'],
                    'points_prefix'           => $product_option_value['points_prefix'],
                    'weight'                  => $product_option_value['weight'],
                    'weight_prefix'           => $product_option_value['weight_prefix']
                );
            }

            $product_option_data[] = array(
                'product_option_id'    => $product_option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id'            => $product_option['option_id'],
                'name'                 => $product_option['name'],
                'type'                 => $product_option['type'],
                'value'                => $product_option['value'],
                'required'             => $product_option['required']
            );
        }

        return $product_option_data;
    }

    public function getProfileIdByProductId($product_id)
    {
        //echo $sql; die();
        $query = $this->db->query("SELECT `cednewegg_profile_id` FROM " . DB_PREFIX . "cednewegg_profile_products WHERE product_id = '" . (int)$product_id . "'");
        if ($query->row && isset($query->row['cednewegg_profile_id']) && !empty($query->row['cednewegg_profile_id'])) {
            return $query->row['cednewegg_profile_id'];
        }
        return false;
    }

    public function getMappedOptionIds($product_id)
    {
        $ids = array();
        $sql = "SELECT `cednewegg_profile_id` FROM `".DB_PREFIX."cednewegg_profile_products` WHERE `product_id`='".(int)$product_id."'";
        $q = $this->db->query($sql);
        if($q->num_rows && isset($q->row['cednewegg_profile_id'])) {
            $profile_id = $q->row['cednewegg_profile_id'];
            $s = "SELECT `profile_attribute_mapping` FROM `".DB_PREFIX."cednewegg_profile` WHERE `id`='".(int)$profile_id."'";
            $query = $this->db->query($s);
            if(isset($query->num_rows) && isset($query->row['profile_attribute_mapping'])) {
                $mappingj = $query->row['profile_attribute_mapping'];
                if(json_decode($mappingj, true)) {
                    $attrMapping = json_decode($mappingj, true);
                    foreach ($attrMapping as $ma) {
                        $map = explode('-', $ma['mapping']);
                        if(isset($map[1]) && $map[0] == 'option') {
                            $ids[] = $map[1];
                        }
                    }
                }
            }
        }
        return $ids;
    }

    function getOptionValues($option_id,$product_id){
        $option_value_data = array();

        $option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int)$option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order, ovd.name");
        $sql="SELECT `option_value_id` FROM `" . DB_PREFIX . "product_option_value` where `product_id`='".$product_id."' and `option_id`='". (int)$option_id ."'";
        $selectedOptions=$this->db->query($sql);
        $val=array();
        if($selectedOptions->num_rows){
            foreach ($selectedOptions->rows as $key => $value) {
                $val[]=$value['option_value_id'];
            }
        }
        foreach ($option_value_query->rows as $option_value) {
            if(in_array($option_value['option_value_id'],$val)){
                $option_value_data[] = array(
                    'option_value_id' => $option_value['option_value_id'],
                    'name'            => $option_value['name'],
                    'image'           => $option_value['image'],
                    'sort_order'      => $option_value['sort_order']
                );
            }
        }

        return $option_value_data;
    }

    public function saveProductInfo($data)
    {
        $product_id = isset($data['id']) ? $data['id']: '';
        $attributes = isset($data['attribute_values']) ? json_encode($data['attribute_values']):'';
        if(!empty($product_id)) {
            $ssql = "SELECT `id` FROM `".DB_PREFIX."cednewegg_profile_products` WHERE `product_id`=".(int)$product_id;
            $q =  $this->db->query($ssql);
            if($q->num_rows && isset($q->row['id'])) {
                $sql = "UPDATE `".DB_PREFIX."cednewegg_profile_products` SET `data`='".$attributes."' WHERE `product_id`=".(int)$product_id;
            } else {
                $sql = "INSERT INTO `".DB_PREFIX."cednewegg_profile_products` SET `data`='".$attributes."', `product_id`=".(int)$product_id;
            }
            $this->db->query($sql);
            return true;
        }
        return false;
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
     public function getProfileInfo($profileid)
    {
        $data = array();
        $psql = "SELECT * FROM `".DB_PREFIX."cednewegg_profile` WHERE `id`=".(int)$profileid;
        $pquery = $this->db->query($psql);
        if($pquery->num_rows) {
            $data = $pquery->row['profile_req_opt_attribute'];
            if(isset($data)) {
                
                $data = json_decode($data,true);
            
            } else {
                $data = array();
            }
        }
        return $data;
    }

 public function getProduct_data($product_id)
    {
        $data = array();
        $psql = "SELECT Request_id FROM `".DB_PREFIX."cednewegg_profile_products` WHERE `product_id`=".(int)$product_id;
        $pquery = $this->db->query($psql);
        if($pquery->num_rows) {
            $data = $pquery->row['Request_id'];
        }
        return $data;
    }
    

    public function getAllProductIds()
    {
        $ids = array();
        $query = $this->db->query("SELECT cpp.`product_id` FROM `".DB_PREFIX."cednewegg_profile_products` cpp
         JOIN `" . DB_PREFIX . "product` p ON (p.product_id = cpp.product_id) 
        ");
        if ($query->num_rows) {
            foreach ($query->rows as $id) {
                $ids[] = $id['product_id'];
            }
        }
        return $ids;
    }

    public function clearProductChunk()
    {  
        $this->db->query("TRUNCATE TABLE `".DB_PREFIX."cednewegg_products_chunk`");
        return true;
    }


    public function addProduct_statussync($ids = array(), $type = 'upload_chunk')
    {
       // print_r($ids); die;
        $this->db->query("INSERT INTO `".DB_PREFIX."cednewegg_products_chunk` 
        SET `key` = '".$type."', 
         `values` = '".$this->db->escape(json_encode($ids))."'");

        return true;
    }

   public function addProductsChunk($ids = array(), $type = 'upload_chunk')
    {
       // print_r($ids); die;
        $this->db->query("INSERT INTO `".DB_PREFIX."cednewegg_products_chunk` 
        SET `key` = '".$type."', 
         `values` = '".$this->db->escape(json_encode($ids))."'");

        return true;
    }

    public function getProd($SellerPartNumber) {
    
        $query = $this->db->query("SELECT * FROM `".DB_PREFIX."product` WHERE `ean`='".$SellerPartNumber."'");
        return $query->row;
    }


  public function product_sycned($data,$id)
    { 
       
        if(isset($data['0']['Message'])){ 
            $data = 'wait';
            return $data;
        }else{ 
        if(isset($data['NeweggEnvelope']['Message']['ProcessingReport']['ProcessingSummary']['SuccessCount'])){

        $WithErrorCount = $data['NeweggEnvelope']['Message']['ProcessingReport']['ProcessingSummary']['WithErrorCount'];

        if($WithErrorCount =='0'){ 
         $sql = 'UPDATE ' . DB_PREFIX . 'cednewegg_profile_products SET 
            newegg_feed_error    = "",
            newegg_status        = "Uploaded"
            WHERE product_id = "' . (int)$id. '"';
            $this->db->query($sql);
            }else{ 
                $ErrorDescription='';
            if(isset($data['NeweggEnvelope']['Message']['ProcessingReport']['Result']['ErrorList']['ErrorDescription'])){ 
                 $ErrorDescription = json_encode($data['NeweggEnvelope']['Message']['ProcessingReport']['Result']['ErrorList']['ErrorDescription']);
               }else{
                 if(isset($data['NeweggEnvelope']['Message']['ProcessingReport']['Result'][0]['ErrorList']['ErrorDescription'])){ 
                 $ErrorDescription = $data['NeweggEnvelope']['Message']['ProcessingReport']['Result'];
                  foreach($ErrorDescription as $err){ 
                  $SellerPartNumber = $err['AdditionalInfo']['SellerPartNumber'];
                  $selldata = $this->getProd($SellerPartNumber);
               if(isset($selldata)){ $Invalid='Invalid';
                    $sql = 'UPDATE ' . DB_PREFIX . 'cednewegg_profile_products SET 
                    newegg_feed_error    = "'.$err['ErrorList']['ErrorDescription'].'",
                    newegg_status        = "'.$Invalid.'"
                    WHERE product_id = "'. (int)$selldata['product_id']. '"';
                    $this->db->query($sql);
                }
                    }
                  }
               }
            }
        }else{

    if(isset($data['NeweggEnvelope']['Message']['Result']['ErrorList']['ErrorDescription'])){ 
    $Message = $data['NeweggEnvelope']['Message']['Result']['ErrorList']['ErrorDescription'];
            $sql = 'UPDATE ' . DB_PREFIX . 'cednewegg_profile_products SET 
            newegg_feed_error    = "'.$Message.'",
            newegg_status        = "Invalid"
            WHERE product_id = "' . (int)$id. '"';
            $this->db->query($sql);
            }
        }
       }
     return true;
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

    public function getProductsChunk($type = 'upload_chunk')
    {
        $ids = array();
        $query = $this->db->query("SELECT `values` FROM `".DB_PREFIX."cednewegg_products_chunk` WHERE `key`='".$type."'");
        if ($query->num_rows) {
           if(json_decode($query->row['values'],true)) {
               $ids = json_decode($query->row['values'],true);
           }
        }
        return $ids;
    }

    public function getAllProfiles()
    {
        $query = $this->db->query("SELECT * FROM `".DB_PREFIX."cednewegg_profile`");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return array();
        }
    }

    public function getProductSpecials($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

        return $query->rows;
    }

    public function getProductOptionValue($option_id,$product_id){
        $option_value_data = array();

        $option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int)$option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order, ovd.name");
        $sql="SELECT `option_value_id` FROM `" . DB_PREFIX . "product_option_value` where `product_id`='".$product_id."' and `option_id`='". (int)$option_id ."'";
        $selectedOptions=$this->db->query($sql);
        $val=array();
        if($selectedOptions->num_rows){
            foreach ($selectedOptions->rows as $key => $value) {
                $val[]=$value['option_value_id'];
            }
        }
        foreach ($option_value_query->rows as $option_value) {
            if(in_array($option_value['option_value_id'],$val)){
                $option_value_data[] = array(
                    'option_value_id' => $option_value['option_value_id'],
                    'name'            => $option_value['name'],
                    'image'           => $option_value['image'],
                    'sort_order'      => $option_value['sort_order']
                );
            }

        }

        return $option_value_data;
    }

    public function getTotalProducts($data = array()) {

        $sql = "SELECT count(DISTINCT cpp.product_id) as total FROM `" . DB_PREFIX . "product` p 
        LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) 
        JOIN `" . DB_PREFIX . "cednewegg_profile_products` cpp ON (p.product_id = cpp.product_id) 
        LEFT JOIN `".DB_PREFIX."cednewegg_profile` ceprof ON (ceprof.id = cpp.cednewegg_profile_id) 
        LEFT JOIN `".DB_PREFIX."cednewegg_profile_products` cp ON (cp.product_id = cpp.product_id) 
       ";

    
        $sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        //echo $sql; die(__DIR__);


        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape(trim($data['filter_name'])) . "%'";
        }

        if (!empty($data['filter_profile_name'])){
            $sql .= 'AND ceprof.id = '.(int)$data['filter_profile_name'];
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '" . $this->db->escape(trim($data['filter_model'])) . "%'";
        }

        if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        if (isset($data['filter_newegg_item_id']) && !is_null($data['filter_newegg_item_id'])) {
            $sql .= " AND cp.newegg_item_id = '" . $data['filter_newegg_item_id'] . "'";
        }

        if (isset($data['filter_newegg_status']) && !is_null($data['filter_newegg_status'])) {
            $sql .= " AND cp.newegg_status = '" . $data['filter_newegg_status'] . "'";
        }

        if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
            if ($data['filter_image'] == 1) {
                $sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
            } else {
                $sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
            }
        }

        $query = $this->db->query($sql);
        //print_r($query->row['total']); die();
        return $query->row['total'];
    }

    public function getneweggStatuses() {

        return array(
            array(
                'value' => 'Not Uploaded',
                'label' => 'Not Uploaded'
            ),
            array(
                'value' => 'uploaded',
                'label' => 'Uploaded'
            ),
            array(
                'value' => 'invalid',
                'label' => 'Invalid'
            ),
            array(
                'value' => 'ended',
                'label' => 'Ended'
            ),
        );
    }

    public function getProduct($product_id) {
        $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "product p 
        LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
        JOIN `".DB_PREFIX."cednewegg_profile_products` cpp ON (p.product_id = cpp.product_id) 
        LEFT JOIN `".DB_PREFIX."cednewegg_profile_products` cp ON (p.product_id = cp.product_id) 
        WHERE cp.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
     
        $result_product = $this->db->query($sql);
        if($result_product && $result_product->num_rows ){
            return $result_product->row;
        } else {
            return array();
        }
    }


    public function getneweggItemId($product_id=0) {
        if ($product_id) {
            $newegg_item_id = '';
            $sql = "SELECT `product_id` FROM `".DB_PREFIX."cednewegg_profile_products` where `product_id`='".$product_id."'";
            $result = $this->db->query($sql);
            if ($result && $result->num_rows && isset($result->row['product_id'])) {
                $newegg_item_id = $result->row['product_id'];
            }
            return $newegg_item_id;
        }
        return false;
    }

    public function getAllneweggProductIds() {
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

    public function editProductEvent ($product_id, $data) {

        $up_inv = $this->config->get('cednewegg_update_inventry');

        if ($up_inv) {
            $this->updateInvenetry($product_id, $data);
        }

        $up_pri = $this->config->get('cednewegg_update_price');

        if ($up_pri) {
            $this->updatePrice($product_id, $data);
        }

        $up_pro = $this->config->get('cednewegg_update_all');

        if ($up_pro) {
            $this->load->library('cednewegg');
            $cwal_lib = Cednewegg::getInstance($this->registry);
            $status = $cwal_lib->uploadProducts(array($product_id),false,'PARTIAL_UPDATE');
            $cwal_lib->log($status);
        }
    }

    public function syncneweggIds($items)
    {
        $successes = array();
        $errors = array();
        foreach ($items as $item) {
            $newegg_id = $item['ItemID'];
            if(!isset($item['SKU'])) {
                $errors[] = "newegg Id $newegg_id : Product SKU Is Empty In newegg Product Data";
                continue;
            }
            $sku = $item['SKU'];
            if(isset($item['SellingStatus']['ListingStatus']) && $item['SellingStatus']['ListingStatus'] == 'Ended') {
                $errors[] = "newegg Id $newegg_id : Product With SKU $sku Ended At newegg";
                continue;
            }

            $query = $this->db->query("SELECT `product_id` FROM `".DB_PREFIX."product` WHERE `sku`='".$sku."'");
            if($query->num_rows && !empty($query->row['product_id'])) {
                $sql = $this->db->query("SELECT `id` FROM `".DB_PREFIX."cednewegg_profile_products` WHERE `product_id`='".(int)$query->row['product_id']."'");
                if($sql->num_rows) {
                    $this->db->query("UPDATE `".DB_PREFIX."cednewegg_profile_products` 
                SET `newegg_item_id`='".$newegg_id."', `newegg_status` ='uploaded' WHERE `product_id`='".(int)$query->row['product_id']."'");
                } else {
                    $this->db->query("INSERT INTO `".DB_PREFIX."cednewegg_profile_products` 
                 SET `product_id`='".(int)$query->row['product_id']."',`newegg_item_id`='".$newegg_id."', `newegg_status` ='uploaded'");
                }
                $successes[] = "newegg Id $newegg_id : Product Synced Successfully With SKU $sku And product id ".$query->row['product_id'];
            } else {
                $errors[] = "newegg Id $newegg_id : Product Does Not Exist In Opencart With SKU $sku";
            }
        }
        return array(
            'success' => $successes,
            'error' => $errors,
        );
    }

    //sumit
    public function updateProduct($profile_id, $categories, $manufacturer)
    { 
        $sql = "SELECT DISTINCT (p.product_id) FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_to_store` pts on (pts.product_id = p.product_id) LEFT JOIN `" . DB_PREFIX . "product_to_category` ptc on (ptc.product_id = p.product_id) where p.product_id >0 ";
        
        if (!empty($categories)) {
            $sql .= " AND ptc.category_id IN(" . implode(',', $categories) . ") ";
        }

        if (!empty($manufacturer)) {
            $sql .= "AND p.manufacturer_id IN(" . implode(',', $manufacturer) . ") ";
        }

        $result = $this->db->query($sql);

        if ($result && $result->num_rows) {
            $products =  array_chunk($result->rows, 1000);
            foreach ($products as $key => $value) {
                foreach ($result->rows as $key => $product) {
                    $sql = $this->db->query("SELECT * FROM `" . DB_PREFIX . "cednewegg_profile_products` WHERE `product_id` = '" . $product['product_id'] . "' ");
                    if ($sql->num_rows == 0) {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "cednewegg_profile_products` SET `product_id` = '" . $product['product_id'] . "' ,`cednewegg_profile_id` = '" . $profile_id . "' ");
                    }
                }
            }
        }
    }

 
   public function getProductAttributes($product_id) {
        $product_attribute_data = array();

       $product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

        foreach ($product_attribute_query->rows as $product_attribute) {
            $product_attribute_description_data = array();

      
            $product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

            foreach ($product_attribute_description_query->rows as $product_attribute_description) {
                $product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
            }

            $product_attribute_data[] = array(
                'attribute_id'                  => $product_attribute['attribute_id'],
                'product_attribute_description' => $product_attribute_description_data
            );
        
    }
   
        return $product_attribute_data;
    }



 
     public function savevalidationdata($product_id,$data)
    {
        
        if(!empty($product_id)) {
            $ssql = "SELECT `id` FROM `".DB_PREFIX."cednewegg_profile_products` WHERE `product_id`=".(int)$product_id;
            $q =  $this->db->query($ssql);
            if($q->num_rows && isset($q->row['id'])) {
                $sql = "UPDATE `".DB_PREFIX."cednewegg_profile_products` SET `newegg_validation`='".$data['status']."', `newegg_validation_error`='".$data['error']."' WHERE `product_id`=".(int)$product_id;
            }
            $this->db->query($sql);
            return true;
        }
        return false;
    }
}