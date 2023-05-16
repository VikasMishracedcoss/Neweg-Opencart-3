<?php

class ModelExtensionModuleCedNewEggProfile extends Model
{

    public function addProfile($data)
    {
        // echo '<pre>';
        // print_r($data); 
        // die(__DIR__);
        $store_cat = (isset($data['product_category']) && $data['product_category']) ? $data['product_category'] : array();
        foreach($data['newegg_req_attribute'] as $req_attr){
            //print_r($req_attr);
            $required_attributes['required_attributes'][]=
            array( 
                "newegg_attribute_name"=>$req_attr[0],
                "opencart_attribute_code"=>$req_attr[1],
                "default"=>$req_attr[2],
                "required"=>'1'
               );
        }
    $newegg_profile_category =json_encode($required_attributes);
    $optinol_attributes='';
    if(isset($data['newegg_opt_attribute'])){
    foreach($data['newegg_opt_attribute'] as $req_attr){
            //print_r($req_attr);
            $required_attributes['optional_attributes'][]=
            array( 
                "newegg_attribute_name"=>$req_attr[0],
                "opencart_attribute_code"=>$req_attr[1],
                "default"=>$req_attr[2],
                "required"=>'1'
               );
        }
    $profile_cat_attribute =json_encode($optinol_attributes);
   }
    $var_attrs='';
    if(isset($data['varient_attribute'])){
    foreach($data['varient_attribute'] as $var_attr){
            $var_attrs['varient_attributes'][]=
            array( 
                "varient_attributes"=>$var_attr[0],
                "opencart_attribute"=>$var_attr[1],
                "default"=>$var_attr[2],
               );
        }
     $var_attrs =json_encode($var_attrs);
     }
     foreach($data['varient_map'] as $varnt_attr){
        $varnt_attrs['varient_mapping'][]=
            array( 
                "varient_attributes"=>$varnt_attr[1],
                "opencart_attribute"=>$varnt_attr[0]
                );
        }
    $varnt_attrss =json_encode($varnt_attrs);
    
    $var_attr_mapping = (isset($data['profile_variant_mapping']) && $data['profile_variant_mapping']) ? $data['profile_variant_mapping'] : array();
    $specifics = (isset($data['profile_specifics']) && $data['profile_specifics']) ? $data['profile_specifics'] : array();
    
    $this->db->query("INSERT INTO " . DB_PREFIX . "cednewegg_profile SET 
           profile_name = '" . $data['profile_name'] . "',
           profile_category_name = '" . $data['cate_name'] . "', 
           profile_code = '" . str_replace(' ', '_', $data['profile_name']) . "',
           status = '" . (int)$data['status'] . "',
           profile_req_opt_attribute = '" . $newegg_profile_category. "',
           profile_varient_attribute = '" .$var_attrs. "',
           profile_varient_mapping = '" .$varnt_attrss. "',
           profile_cat_attribute = '" . $profile_cat_attribute. "'
            ");

        $profile_id = $this->db->getLastId();
        if ($profile_id) {
            $this->removeProductFromProfile($profile_id);
            $this->add_product($profile_id,$data['selected']);
        }

        $this->cache->delete('profile');
    }

    public function addchild_attr($profile_cat_id,$attribute)
    {
        // echo '<pre>';
        // print_r($attribute); 
        // die('ytuyt');
        $PropertyValueList = implode(",",$attribute['subcatPropertyValueResponse']['PropertyValueList']);

        $this->db->query("INSERT INTO " . DB_PREFIX . "cednewegg_profile_attribute SET 
           profile_id = '" . $profile_cat_id . "',
           profile_attr = '" .$attribute['all']. "',
           required_attribute = '" .$attribute['required']. "',
           subattr = '" .$PropertyValueList. "'
            ");
        return $this->getchild_attr($profile_cat_id);
    }


 public function getchild_attr($profile_cat_id)
    {
        // echo '<pre>';
        // print_r($profile_cat_id); 
        // die(__DIR__);

        $sql = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_profile_attribute WHERE profile_id = '" . (int)$profile_cat_id . "'");
        $data = $sql->row;
        if(empty($data)){
            $data='';
        }
        return $data;
    }



     public function add_product($data)
     {  
        $account_id='1';
        if(isset($data['selected'])){ 
        $data_pro = array_chunk($data['selected'],500);
        foreach ($data_pro as $product_ids) {

                $sql = "INSERT INTO " . DB_PREFIX . "cednewegg_profile_products (id, product_id, cednewegg_profile_id,account_id) VALUES ";
                foreach ($product_ids as $product_id) 
                {
                   $sql .= "((SELECT `id` FROM " . DB_PREFIX . "cednewegg_profile_products pscp WHERE pscp.product_id='" . (int)$product_id . "' and pscp.account_id='" . (int)$account_id . "' LIMIT 1), '" . (int)$product_id . "', '" . (int)$data['id'] . "', '" . (int)$account_id . "'), ";
                }
                $sql = rtrim($sql, ', ');
                $sql .= " ON DUPLICATE KEY UPDATE product_id=values(product_id), cednewegg_profile_id=values(cednewegg_profile_id), account_id=values(account_id)";
                //print_r($sql); die();
                $query = $this->db->query($sql);
            }
        }
    }

    public function checkproduct_assigned($product_id)
    {   
        //echo '<pre>';
        //print_r($data);  
       //die(__DIR__);
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_profile_products 
       WHERE product_id = '".$product_id."'");
     
       $result = $query->row;
      // print_r($result); die;
       return $result;
    }
    public function editProfile($profile_id, $data)
    {
        // echo '<pre>';
        // print_r($data); 
        // die(__DIR__);
   
        foreach($data['newegg_req_attribute'] as $req_attr){
            //print_r($req_attr);
            $required_attributes['required_attributes'][]=
            array( 
                "newegg_attribute_name"=>$req_attr[0],
                "opencart_attribute_code"=>$req_attr[1],
                "default"=>$req_attr[2],
                "required"=>'1'
               );
        }

        $newegg_profile_req_category =json_encode($required_attributes);
        foreach($data['newegg_opt_attribute'] as $opt_attr){
            //print_r($req_attr);
            $optional_attributes['optional_attributes'][]=
            array( 
                "newegg_attribute_name"=>$opt_attr[0],
                "opencart_attribute_code"=>$opt_attr[1],
                "default"=>$opt_attr[2],
                "required"=>'0'
               );
        }
    $newegg_profile_opt_category =json_encode($optional_attributes);

    foreach($data['varient_attribute'] as $var_attr){
       
            $var_attrs['varient_attributes'][]=
            array( 
                "varient_attributes"=>$var_attr[0],
                "opencart_attribute"=>$var_attr[1],
                "default"=>$var_attr[2],
               );
        }
     $var_attrs =json_encode($var_attrs);

        foreach($data['varient_map'] as $varnt_attr){
        $varnt_attrs['varient_mapping'][]=
            array( 
                "varient_attributes"=>$varnt_attr[1],
                "opencart_attribute"=>$varnt_attr[0]
                );
        }
        $varnt_attrss =json_encode($varnt_attrs);

        $store_cat = (isset($data['product_category']) && $data['product_category']) ? $data['product_category'] : array();
  

        $this->db->query("UPDATE " . DB_PREFIX . "cednewegg_profile SET 
            profile_name = '" . $data['profile_name'] . "',
            profile_category_name = '" . $data['cate_name'] . "', 
            profile_code = '" . str_replace(' ', '_', $data['profile_name']) . "',
            profile_category = '" . $data['newegg_profile_category'] . "',
            profile_req_opt_attribute = '" . $newegg_profile_req_category. "',
            profile_cat_attribute = '" . $newegg_profile_opt_category. "',
            profile_varient_attribute = '" .$var_attrs. "',
            profile_varient_mapping = '" .$varnt_attrss. "',
            status = '" . (int)$data['status'] . "'
            WHERE id = '" . (int)$profile_id . "'");

        
        //$this->removeProductFromProfile($profile_id);
        $this->add_product($data);
        $this->cache->delete('profile');
    }

    public function deleteProfile($profile_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "cednewegg_profile WHERE id = '" . (int)$profile_id . "'");
        $this->removeProductFromProfile($profile_id);
        $this->cache->delete('profile');
    }

    public function getProfile($profile_id)
    {
        $data = array(
            'profile_name' => '',
            'status' => '',
            'profile_cat' =>'',
            'profile_category_name'=>'',
            'profile_product' =>'',
            'profile_newegg_req_attribute'=>'',
            'profile_newegg_opt_category'=>'',
            'profile_varient_attribute'=>'',
            'profile_varient_mapping'=>'',
        );
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_profile WHERE id = '" . (int)$profile_id . "'");

        $result = $query->row;
       // print_r($result); die;
        if (isset($result['profile_name'])) {
            $data['profile_name'] = $result['profile_name'];
        }
        if (isset($result['status'])) {
            $data['status'] = (int)$result['status'];
        }
         if (isset($result['profile_category'])) {
            $data['profile_cat'] = (int)$result['profile_category'];
        }

        if (isset($result['profile_req_opt_attribute'])) {
            $data['profile_newegg_req_attribute'] = json_decode($result['profile_req_opt_attribute'],true);
        }

        if (isset($result['profile_cat_attribute'])) {
            $data['profile_newegg_opt_category'] = json_decode($result['profile_cat_attribute'],true);
        }

        if (isset($result['profile_varient_attribute'])) {
            $data['profile_varient_attribute'] = json_decode($result['profile_varient_attribute'],true);
        }
        if (isset($result['profile_varient_mapping'])) {
            $data['profile_varient_mapping'] = json_decode($result['profile_varient_mapping'],true);
        }
        if (isset($data['profile_product'])) {
            $products = $this->getProductByProfileId($profile_id);
            $data['profile_product'] = $products;
        }
        //print_r($data); die(__FILE__);
        return $data;
    }



    public function getProfiles($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "cednewegg_profile cp WHERE id >= '0'";

        $sort_data = array(
            'profile_name',
            'status',
            'id'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY cp.profile_name";
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

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalProductByProfileId($profile_id){
        $sql = $this->db->query("SELECT COUNT(*) as total FROM `".DB_PREFIX."cednewegg_profile_products` WHERE cednewegg_profile_id = '".$profile_id."' ");

        if($sql->num_rows){
            return $sql->row['total'];
        }

        return 0;
    }


    public function getProductByProfileId($profile_id){
        $sql = $this->db->query("SELECT product_id  FROM `".DB_PREFIX."cednewegg_profile_products` WHERE cednewegg_profile_id = '".$profile_id."' ");

        if($sql->num_rows){
           // print_r($sql->rows); die();
            return $sql->rows;
        }

        return 0;
    }

    public function getTotalProfiles()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cednewegg_profile");

        return $query->row['total'];
    }

    public function getDefaultAttributesMapping()
    {
        return array(
            'name' => 'name',
            'description' => 'description',
            'price' => 'price',
            'stock' => 'quantity',
            'item_sku' => 'sku',
            'weight' => 'weight',
            'package_length' => 'length',
            'package_width' => 'width',
            'package_height' => 'height',
            'days_to_ship' => '',
        );
    }

    public function getDefaultAttributes()
    {
        return array(
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'sku' => 'SKU',
            'model' => 'Model',
            'weight' => 'Weight',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'days_to_ship' => '',
        );
    }

    public function addProductInProfile($profile_id)
    {
        $product_ids = array();
        $sql = "SELECT DISTINCT (p.product_id) FROM `" . DB_PREFIX . "product` p 
        LEFT JOIN `" . DB_PREFIX . "product_to_store` pts on (pts.product_id = p.product_id) 
        LEFT JOIN `" . DB_PREFIX . "product_to_category` ptc on (ptc.product_id = p.product_id) where p.product_id >0 ";

        $sql .= " AND p.product_id NOT IN (SELECT cpp.product_id FROM `".DB_PREFIX."cednewegg_profile_products` cpp)";
        
        /*if (!empty($categories)) {
            $sql .= "AND ptc.category_id IN(" . implode(',', $categories) . ") ";
        }*/

       /* if (!empty($manufacturer)) {
            $sql .= "AND p.manufacturer_id IN(" . implode(',', $manufacturer) . ") ";
        }
*/
        $result = $this->db->query($sql);

        if ($result && $result->num_rows) {

            foreach ($result->rows as $row) {
                $product_ids[] = $row['product_id'];
            }
        }
        if(!empty($product_ids)) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "cednewegg_profile_products` 
                WHERE `cednewegg_profile_id` != " . (int)$profile_id . " 
                AND `product_id` IN (" . implode(',', (array)$product_ids) . ")");
            $products = array_chunk($product_ids, 1000);
            foreach ($products as $key => $value) {
                $sql = "INSERT INTO `" . DB_PREFIX . "cednewegg_profile_products` (`product_id`,`cednewegg_profile_id`) values ";
                foreach ($value as $product_id) {
                    $sql .= "('" . $product_id . "','" . $profile_id . "'), ";
                }
                $sql = rtrim($sql, ', ');
                $this->db->query($sql);
            }
        }

    }

    public function removeProductFromProfile($profile_id)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "cednewegg_profile_products` WHERE `cednewegg_profile_id`='" . (int)$profile_id . "'");
    }
    
   public function profile_label()
    {
        $data = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'oc_product'");
        //echo "<pre>";
        //print_r($product_data); die();
        return $data->rows;
    }

    
    public function getroot_cat()
    {
        $data = $this->db->query("SELECT root_cat FROM `oc_cednewegg_accounts` WHERE `id`='1'");
        // echo "<pre>";
        // print_r($product_data); die();
        return $data->row['root_cat'];
    }

   public function ProfilePropertyName($profile_id,$PropertyName)
    {
         $this->db->query("UPDATE " . DB_PREFIX . "cednewegg_profile SET 
            varient_attr_name = '" . $PropertyName. "'
            WHERE id = '" . (int)$profile_id . "'");
        return true;
    }

    public function getStoreOptions()
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "' AND `type` IN ('checkbox','select','radio') ORDER BY od.name";
        $result = $this->db->query($sql);
        $options = $result->rows;
        $option_value_data = array();
        if (!empty($options)) {
            foreach ($options as $option) {
                $option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int)$option['option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order ASC");
                foreach ($option_value_query->rows as $option_value) {
                    $option_value_data[$option['option_id']][] = array(
                        'option_value_id' => $option_value['option_value_id'],
                        'name' => $option_value['name'],
                    );
                }
            }
            return array('options' => $options, 'option_values' => $option_value_data);
        }
        return array('options' => $options, 'option_values' => $option_value_data);
    }

}

?>
