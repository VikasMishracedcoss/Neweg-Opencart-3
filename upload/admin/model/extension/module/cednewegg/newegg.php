<?php
class ModelExtensionModuleCedNewEggNewegg extends Model {

    public function prepareData($productdata,$profile_id,$type='')
    {   

        $product_data['NeweggEnvelope']=[];
        $product_data['NeweggEnvelope']['Header'] = ["DocumentVersion" => 1.0];
        $product_data['NeweggEnvelope']['MessageType'] = 'BatchItemCreation';
        $product_data['NeweggEnvelope']['Overwrite']   = 'No';
        
        $category_data = $this->getProfile($profile_id);
        if(isset($category_data['profile_category'])){
        $category_name = $this->attribute_name($category_data['profile_category']);
        $category_name= $category_name['required_attribute'];
        }
        //print_r($category_data); die(__DIR__);
        
        if(empty($type)){
             $type =  ['Action' => 'Create Item'];
           }else{
             $type =  ['Action' => 'Update Item'];
           }
       $products=[];
       $varient_product='';
       foreach($productdata as $key=>$ids){
    
        $data = $this->getProductById($ids);
        $varient_data = $this->getProductOptions($ids,$data);
        // echo "<pre>";
        // print_r($varient_data); die();
        $profile_id = $this->getProductInfo($ids);
        
         if($profile_id['newegg_validation_error']=='Invalid'){
           $this->invalid_product($ids); 
            continue;
         }
       if(!empty($varient_data['Variation'])){ 
         $profile_id = $profile_id['cednewegg_profile_id'];
        $varient_product = $this->variation($profile_id,$ids,$varient_data);
        //print_r($varient_product); die();
       }else{ 
        $data = $this->getProductById($ids);
        //print_r($data); die();
        $profile_id = $profile_id['cednewegg_profile_id'];
        $attributes=$this->attributes_data($profile_id);
        $attributesvalue=$this->getProductAttributeById($profile_id,$attributes['required_attributes']['0']['opencart_attribute_code']);
        $req_attr = $this->get_attr($category_data['profile_category']);

       foreach($attributes['required_attributes'] as $key=>$required_attributes){
            if($category_name == $required_attributes['newegg_attribute_name']){ 
                   if(!empty($required_attributes['default'])){
                $products_req[$required_attributes['newegg_attribute_name']] = $require_attributes['default'];
              }else{ 
                  $products_req[$required_attributes['newegg_attribute_name']] = $required_attributes['opencart_attribute_code'];
                 }
                }elseif($required_attributes['opencart_attribute_code']=='price'){
                    if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{ 
                  $products[$required_attributes['newegg_attribute_name']] = 
                  $this->getPrice($data[$required_attributes['opencart_attribute_code']]);
                 }
                } /*elseif($required_attributes['newegg_attribute_name']=='UPCOrISBN'){
                  if(!empty($required_attributes['default'])){
                   $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                    }else{ $products[$required_attributes['newegg_attribute_name']] = rand(12,1000000000000);
                 }
                 }*/
                 elseif($required_attributes['opencart_attribute_code']=='weight'){
                 if(!empty($required_attributes['default'])){

                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{ 
                $weight = number_format((float)$data[$required_attributes['opencart_attribute_code']], 2, '.', '');
                if($weight!=''){ $weight='1'; }
                  $products[$required_attributes['newegg_attribute_name']] = $weight;
                 }
                }elseif($required_attributes['opencart_attribute_code']=='shipping'){
                 if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{  $shipping='';
                    if($data[$required_attributes['opencart_attribute_code']]=='1'){ $shipping="Default"; }else{ $shipping="Free"; }
                  $products[$required_attributes['newegg_attribute_name']] = $shipping;
                 }
                }elseif($required_attributes['newegg_attribute_name']=='ManufacturerPartsNumber'){
                 if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{ 
                   $products[$required_attributes['newegg_attribute_name']] = rand(12,10000000000);
                 }}else{    
            if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
              }else{ 
                  $products[$required_attributes['newegg_attribute_name']] = $data[$required_attributes['opencart_attribute_code']];
                 }
             }
         }   
            $Value='';
            $Value = json_decode($category_data['profile_varient_mapping'],true);
            if(isset($Value['varient_mapping'][0])){
             $Value['varient_mapping'][0]['varient_attributes'];
            }

          if(isset($variationdata['VariationSpecifics']['NameValueList']['0']['Value'])){
            $Value = $variationdata['VariationSpecifics']['NameValueList']['0']['Value'];
          } 
            $d = [$category_data['varient_attr_name']=>$Value];

               $Length='';
               if($data['length']=='0'){ $Length='1.00'; }else{ $Length=number_format((float)$data['length'], 2, '.', ''); } 
               $width =''; 
               if($data['width']=='0'){ $width='1.00'; }else{ $width=number_format((float)$data['width'], 2, '.', ''); }
                $height =''; 
                 if($data['height']=='0'){ $height='1.00'; }else{ $height=number_format((float)$data['height'], 2, '.', ''); }
                
                $productdata=array(
                /*"RelatedSellerPartNumber" =>"",*/
                "ItemDimension"            => ["ItemImages"=>["ItemLength" =>$Length,
                "ItemWidth"                => $width,
                "ItemHeight"               => $height]],
                "PacksOrSets"              => '1',                        
                "ProductDescription"       => "Good Product",
                "BulletDescription"        => "Good Product",
                "ItemPackage"              => "OEM",
                "Currency"                 => "USD",
                "LimitQuantity"            => "1",
                "ActivationMark"           => "True",
                "ItemImages"               => $this->getProductImages($ids),
                "Warning"                  => array("Prop65"  => "No",
                "Prop65Motherboard"        => "Yes",
                "CountryOfOrigin"          => "USA",
                "OverAge18Verification"    => "Yes",
                "ChokingHazard"            => array("SmallParts"=>"Yes",
                "SmallBall"                => "Is a small ball",
                "Balloons"                 => "Yes",
                "Marble"                   => "Is a marble") ),
                );
        $categorydata = str_replace(" ","",$category_data['profile_category_name']);
        $SubCategoryProperty = ["SubCategoryProperty"      => [$categorydata=>array_merge($products_req,$d)]];
              
    $product['BasicInfo'] = array_merge($products,$productdata);
    $products = array_merge($product,$SubCategoryProperty);
    $product_finals['Item'] = array_merge($type,$products); 
    $product_datas['SummaryInfo']=['SubCategoryID'=>$category_data['profile_category']];
    $product_final[]= array_merge($product_datas,$product_finals);
    //print_r($product_final); die;
    $varient_product = $this->variation($profile_id,$ids,$varient_data);
    //print_r($varient_product); die();
    $product_final_product='';
    
    }
    if($varient_product!='') {
    //print_r($varient_product); die();
        if(isset($product_final)){ 
            $product_final_product = array_merge($product_final,$varient_product); 
        }else{ 
            $product_final_product = $varient_product; 
        }
    
    }else{
        $product_final_product = $product_final;
    }

 }
   
    $product_data['NeweggEnvelope']['Message']['Itemfeed']= $product_final_product;
    //print_r($product_data); die('1');
    return $product_data;
}
   
     public function variation($profile_id,$ids,$varient_data)
     {    
        //print_r($varient_data); die(__DIR__);
         if(empty($type)){
             $type =  ['Action' => 'Create Item'];
           }else{
             $type =  ['Action' => 'Update Item'];
           }
        $product_final=[];
        $product=[];
        $data = $this->getProductById($ids);
        $category_data = $this->getProfile($profile_id);
        if(isset($category_data['profile_category'])){
        $category_name = $this->attribute_name($category_data['profile_category']);
        $category_name= $category_name['required_attribute'];
        }
        $attributes=$this->attributes_data($profile_id);
        $attributesvalue=$this->getProductAttributeById($profile_id,$attributes['required_attributes']['0']['opencart_attribute_code']);
        $req_attr = $this->get_attr($category_data['profile_category']);
        if(isset($varient_data['Variation'])){ 

       foreach($varient_data['Variation'] as $key=>$variationdata){
        //print_r($variationdata);  die;
        foreach($attributes['required_attributes'] as $required_attributes){
           //print_r($required_attributes['newegg_attribute_name']); 
            if($category_name == $required_attributes['newegg_attribute_name']){ 
                   if(!empty($required_attributes['default'])){
                $products_req[$required_attributes['newegg_attribute_name']] = $require_attributes['default'];
              }else{ 
                  $products_req[$required_attributes['newegg_attribute_name']] = $required_attributes['opencart_attribute_code'];
                 }
                }elseif($required_attributes['opencart_attribute_code']=='price'){
                    if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{
                  $products[$required_attributes['newegg_attribute_name']] = 
                  $this->getPrice($variationdata['StartPrice']);
                 } 
                }elseif($required_attributes['opencart_attribute_code']=='weight'){
                 if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{ 
                   $weight = number_format((float)$data[$required_attributes['opencart_attribute_code']], 2, '.', '');
                if($weight=='0'){ $weight='1'; }
                  $products[$required_attributes['newegg_attribute_name']] = $weight;
                  }
                }elseif($required_attributes['opencart_attribute_code']=='shipping'){
                 if(!empty($required_attributes['default'])){
                   $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{  $shipping='';
                    if($data[$required_attributes['opencart_attribute_code']]=='1'){ $shipping="Default"; }else{ $shipping="Free"; }
                  $products[$required_attributes['newegg_attribute_name']] = $shipping;
                 } 
                }elseif($required_attributes['opencart_attribute_code']=='ean'){
                 if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{ 
                    if($key=='0'){ 
                            $sellerpartnumber = $data['ean'];
                            $products[$required_attributes['newegg_attribute_name']] =$sellerpartnumber;
                      }else{
                    $sellerpartnumber = str_replace("-","A",$variationdata['SKU']);
                    $sellerpartnumber = str_replace("_","B",$sellerpartnumber);
                    $products[$required_attributes['newegg_attribute_name']] =$sellerpartnumber;
                     }
                 }
                }elseif($required_attributes['newegg_attribute_name']=='ManufacturerPartsNumber'){
                 if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{ 
                   $products[$required_attributes['newegg_attribute_name']] = rand(12,10000000000);
                 }
                }/*elseif($required_attributes['newegg_attribute_name']=='UPCOrISBN'){
                 if(!empty($required_attributes['default'])){
                  $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{ $products[$required_attributes['newegg_attribute_name']] = rand(12,1000000000000);
                 }
                }*/
                elseif($required_attributes['opencart_attribute_code']=='quantity'){
                 if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
                   }else{ $products[$required_attributes['newegg_attribute_name']] = 
                   $variationdata['Quantity'];
                 }
                }else{    
            if(!empty($required_attributes['default'])){
                $products[$required_attributes['newegg_attribute_name']] = $required_attributes['default'];
              }else{ 
                  $products[$required_attributes['newegg_attribute_name']] = $data[$required_attributes['opencart_attribute_code']];
                 }
             }
         }
            if(isset($category_data['profile_varient_attribute'])){ 
            $varient_attributes = json_decode($category_data['profile_varient_attribute'],true);
            foreach($varient_attributes['varient_attributes'] as $varientattributes){
            if(!empty($varientattributes['default'])){
                $varient[$varientattributes['varient_attributes']] = $varientattributes['default'];
              }else{ 
                  $varient[$varientattributes['varient_attributes']]  = $varientattributes['opencart_attribute'];
                 }
            }
        }  $Value='';
          if(isset($variationdata['VariationSpecifics']['NameValueList']['0']['Value'])){
            $Value = $variationdata['VariationSpecifics']['NameValueList']['0']['Value'];
          }
           $d = ["GroupHairStylingToolsColor"=>$Value];
              // print_r(array_merge($products_req,$d)); die();
               $Length='';
               if($data['length']=='0'){ $Length='1'; }else{ $Length=number_format((float)$data['length'], 2, '.', ''); } 
               $width =''; 
               if($data['width']=='0'){ $width='1'; }else{ $width=number_format((float)$data['width'], 2, '.', ''); }
                $height =''; 
                 if($data['height']=='0'){ $height='1'; }else{ $height=number_format((float)$data['height'], 2, '.', ''); }
               
                $productdata=array(
                "ItemDimension"            => ["ItemImages"=>["ItemLength" =>$Length,
                "ItemWidth"                => $width,
                "ItemHeight"               => $height]],
                "PacksOrSets"              => '1',                        
                "ProductDescription"       => "Good Product",
                "BulletDescription"        => "Good Product",
                "ItemPackage"              =>  "OEM",
                "Currency"                 =>  "USD",
                "LimitQuantity"            =>  "1",
                "ActivationMark"           =>  "True",
                "ItemImages"               =>  $this->getProductImages($ids),
                "Warning"                  => array("Prop65"          => "No",
                "Prop65Motherboard"        => "Yes",
                "CountryOfOrigin"          => "USA",
                "OverAge18Verification"    => "Yes",
                "ChokingHazard"            => array("SmallParts"=>"Yes",
                "SmallBall"                => "Is a small ball",
                "Balloons"                 => "Yes",
                "Marble"                   => "Is a marble") ),  
                );
                if($key!='0'){
                $productdata1=array(
                "RelatedSellerPartNumber"  => ($key == '0') ? "" : $data['ean'],);
                $productdata = array_merge($productdata,$productdata1);
                }
                $categorydata = str_replace(" ","",$category_data['profile_category_name']);
                $SubCategoryProperty = ["SubCategoryProperty"      => [
                        $categorydata =>array_merge($products_req,$d)]];
                     
                $product['BasicInfo']   = array_merge($products,$productdata);
                $productss = array_merge($product,$SubCategoryProperty);
                $product_finals['Item'] = array_merge($type,$productss);
                $product_datas=["SummaryInfo"=>['SubCategoryID'=>$category_data['profile_category']]];
                $product_final[]= array_merge($product_datas,$product_finals);
       }
   }  
    //print_r($product_final); die(__FILE__);
    return $product_final;
 }




    public function getProductById($product_id) {
        $this->load->model('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($product_id);
        return $product_info;
    }

    public function getProductvarient_dataById($product_id) {
        $this->load->model('catalog/product');
        $product_info = $this->model_catalog_product->getProductOptions($product_id);
        return $product_info;
    }
   
   public function getProductOptions($product_id, $product)
    {
        $attirbute_combination = array();
        $product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ");  // AND o.type IN ('select','radio','checkbox')
        if ($product_option_query && $product_option_query->num_rows) {
            foreach ($product_option_query->rows as $option) {
                $product_option_value_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option_value` pov LEFT JOIN `".DB_PREFIX."option_value_description` ovd ON (pov.option_value_id=ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id='".(Int)$option['product_option_id']."' AND pov.option_id='".(Int)$option['option_id']."' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
                if($product_option_value_query && $product_option_value_query->num_rows) {
                    foreach ($product_option_value_query->rows as $option_value) {
                        if(isset($option_value['product_option_value_id']) && isset($option_value['product_option_id']) && isset($option_value['option_id']) && isset($option_value['option_value_id']))
                        {
                            $attirbute_combination[$option_value['product_option_id']][$option_value['product_option_value_id']] = $option_value['product_option_value_id'];
                            $variant_qty = $option_value['quantity'];
                            $price = $option_value['price'];
                            $price_prefix = $option_value['price_prefix'];
                            $variant_price = $this->calculateValueByPrefix($product['price'], $price, $price_prefix);
                            $name = substr(html_entity_decode($option_value['name']), 0, 20);
                            $name = mb_convert_encoding($name, "UTF-8");
                            $name = iconv(mb_detect_encoding($name), "UTF-8", $name);
                            $variant_sku = $option_value['option_id'].'_'.$option_value['option_value_id'];

                            $attirbute_combination_variant[$option_value['product_option_value_id']] = array(
                                'name' => (string)trim($name),
                                'option_name' => (string)trim($option['name']),
                                'stock' => (int)$variant_qty,
                                'price' => (float)$variant_price,
                                'variation_sku' => (string)$variant_sku,
                            );
                        }
                    }
                }
            }
        }
        $variations = array();
        if(count(array_values($attirbute_combination))>1) {
            $attirbute_combination_options = $this->combinations(array_values($attirbute_combination));
        } elseif(isset($attirbute_combination_variant) && !empty($attirbute_combination_variant)) {
            $attirbute_combination_options = array($attirbute_combination_variant);
        } else {
            $attirbute_combination_options = array();
        }
        if(!empty($attirbute_combination_options) && (count($attirbute_combination_options)>1)) {
            foreach ($attirbute_combination_options as $attirbute_combination_option) {
                $name = '';
                $combs = array();
                $qty = array();
                $price = array();
                $sku = '';
                $opt_name = '';
                foreach ($attirbute_combination_option as $attirbute_combination_opt) {

                    if(isset($attirbute_combination_variant[$attirbute_combination_opt]) && $attirbute_combination_variant[$attirbute_combination_opt]){
                        $attirbute_combination_variant[$attirbute_combination_opt];
                        $combs[$attirbute_combination_variant[$attirbute_combination_opt]['option_name']] = html_entity_decode($attirbute_combination_variant[$attirbute_combination_opt]['name']);
                        $name .= html_entity_decode($attirbute_combination_variant[$attirbute_combination_opt]['name'].' ');
                        $qty[] = $attirbute_combination_variant[$attirbute_combination_opt]['stock'];
                        $price[] = $attirbute_combination_variant[$attirbute_combination_opt]['price'];
                        $sku .= html_entity_decode($attirbute_combination_variant[$attirbute_combination_opt]['variation_sku']).'-';
                    }

                }
                $sku = rtrim($sku, '-');
                $variations[] = array(
                    'name' => $name,
                    'combinations' => $combs,
                    'stock' => (int)min($qty),
                    'option_name' => $opt_name,
                    'price' => (float)max($price),
                    'variation_sku' => $sku,
                );

            }
        } else {
            if(isset($attirbute_combination_options[0])) {
                foreach ($attirbute_combination_options[0] as $attirbute_combination_option) {
                    $combs = array();
                    $combs[$attirbute_combination_option['option_name']] = html_entity_decode($attirbute_combination_option['name']);
                    $sku = $attirbute_combination_option['variation_sku'];
                    $variations[] = array(
                        'name' => (string)html_entity_decode(trim($attirbute_combination_option['name'])),
                        'combinations' => $combs,
                        'stock' => (int)$attirbute_combination_option['stock'],
                        'price' => (float)$attirbute_combination_option['price'],
                        'variation_sku' => (string)$sku,
                    );

                }
            }
        }
        $variantion_data = $this->prepareVariation($product_id, $variations);
        return $variantion_data;
    }

   public function calculateValueByPrefix($original_value, $value, $prefix)
    {
        switch ($prefix) {
            case '+' :
                return (float)$original_value + (float)$value;
                break;
            case '-' :
                return (float)$original_value - (float)$value;
                break;
            default :
                return $original_value;
                break;
        }
    }

   public function combinations($arrays, $i = 0)
    {
        if (!isset($arrays[$i])) {
            return array();
        }
        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }
        $tmp = $this->combinations($arrays, $i + 1);

        $result = array();
        foreach ($arrays[$i] as $v) {
            foreach ($tmp as $t) {
                $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
            }
        }
        return $result;
    }
     public function getPrice($price)
    {
        $finalPrice = $price;
        $variation_type = $this->config->get('cednewegg_price_variation_type');
        $variation_value = (float)$this->config->get('cednewegg_price_variation_value');
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

    public function prepareVariation($product_id, $variations)
    {
        $data=$this->getProductById($product_id);
        $fixedQuantity = (int)$data['price'];
        $data = array();
        $final_combination = array();
        $all_attribute_values = array();
        if(!empty($variations)) {
            foreach ($variations as $variation) {
                $singleVariation = array();
                if (isset($variation['variation_sku']) && !empty($variation['variation_sku'])) {
                    $singleVariation['SKU'] = $product_id.'_'.$variation['variation_sku'];
                    $qty = $variation['stock'];
                    if($fixedQuantity > 0) {
                        $qty = $fixedQuantity;
                    }
                    $vPrice = $this->getPrice($variation['price']);
                    $singleVariation['Quantity'] = $qty;
                    $singleVariation['StartPrice'] = $vPrice;
                }
                $nameValueList = array();
                if (isset($variation['combinations']) && !empty($variation['combinations'])) {
                    foreach ($variation['combinations'] as $attKey => $attVal) {
                        $attrName = $attKey;
                        $all_attribute_values[$attrName][] = $attVal;
                        if (isset($variation['variation_sku']) && !empty($variation['variation_sku'])) {
                            $valueList = array(
                                'Name' => $attrName,
                                'Value' => $attVal,
                            );
                            $nameValueList[] = $valueList;
                        }
                    }
                }
                if (!empty($nameValueList)) {
                    $singleVariation['VariationSpecifics']['NameValueList'] = $nameValueList;
                    $final_combination[] = $singleVariation;
                }
            }
            $VariationSpecificsSets = array();
            foreach ($all_attribute_values as $attName => $allAttributeValue) {
                $VariationSpecificsSet = array(
                    'Name' => $attName,
                    'Value' => array_unique($allAttributeValue),
                );
                $VariationSpecificsSets['NameValueList'][] = $VariationSpecificsSet;
            }
            if (!empty($final_combination)) {
                $data = array(
                    'VariationSpecificsSet' => $VariationSpecificsSets,
                    'Variation' => $final_combination,
                );
            }
        }

        return $data;
    }



    public function attributes_data($profile_id)
    {
     $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cednewegg_profile WHERE id = '" . (int)$profile_id . "'");
     $query->row;
      ///print_r($query->row); die();
     return json_decode($query->row['profile_req_opt_attribute'],true);    
    }

  public function getProductImages($product_id)
   {
        $query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' limit 3");
        $query->rows;
        if(empty($query->rows)){ 
              $ItemImages['Image'][]= array(
                    "ImageUrl"=> "https://play-lh.googleusercontent.com/ZyWNGIfzUyoajtFcD7NhMksHEZh37f-MkHVGr5Yfefa-IX7yj9SMfI82Z7a2wpdKCA=w240-h480-rw",
                    //$value['image']
                );
        } else{
        foreach ($query->rows as $value) {
             //print_r($value['image']); die();
           $ItemImages['Image'][]= array(
                    "ImageUrl"=> "https://play-lh.googleusercontent.com/ZyWNGIfzUyoajtFcD7NhMksHEZh37f-MkHVGr5Yfefa-IX7yj9SMfI82Z7a2wpdKCA=w240-h480-rw",
                    //$value['image']
                );
         }
        }
        return $ItemImages;
    }


   public function getProductAttributeById($product_id, $attribute_id) {
    
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "'");

    return $query->rows;
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

    public function getProfile($profile_id)
    {
        $query = $this->db->query("SELECT profile_category_name,profile_category,profile_varient_attribute,varient_attr_name,profile_varient_attribute,profile_varient_mapping FROM " . DB_PREFIX . "cednewegg_profile WHERE id = '" . (int)$profile_id . "'");
        $result = $query->row;
        return $result;
    }
    
    public function attribute_name($profile_id)
    {
        $query = $this->db->query("SELECT required_attribute FROM " . DB_PREFIX . "cednewegg_profile_attribute WHERE profile_id = '" . (int)$profile_id . "'");
        $result = $query->row;
        return $result;
    }


 
    public function get_attr($cat_id)
    {
        $query = $this->db->query("SELECT required_attribute FROM " . DB_PREFIX . "cednewegg_profile_attribute WHERE profile_id = '" . (int)$cat_id . "'");
        $result = $query->row['required_attribute'];
        return $result;
    }


    public function update_inventory($productdata,$profile_id)
      {
        $product_data['NeweggEnvelope']=[];
        $product_data['NeweggEnvelope']['Header'] = ["DocumentVersion" => 2.0];
        $product_data['NeweggEnvelope']['MessageType'] = 'Inventory';

      $products=[];
       foreach($productdata as $key=>$ids){
    
        $data = $this->getProductById($ids);
        //print_r($data); die();
        $profile_id = $this->getProductInfo($ids);
         if($profile_id['newegg_validation_error']=='Invalid'){
           $this->invalid_product($ids); 
            continue;
         }
    
        $productdata=array(
              "SellerPartNumber"        =>  $data['ean'],
              "Shipping"                =>  "Default",
              "Inventory"               =>  number_format((float)$data['quantity'], 0, '.', ''),
          );
        $product[]=$productdata;
    }
    $product_data['NeweggEnvelope']['Message']['Inventory']['Item']= $product;
    //print_r($product_data); die(__FILE__);

    return $product_data;
  }

   public function update_price($productdata,$profile_id)
      { 
        $error='';
        $product_data['NeweggEnvelope']=[];
        $product_data['NeweggEnvelope']['Header'] = ["DocumentVersion" => 2.0];
        $product_data['NeweggEnvelope']['MessageType'] = 'Price';

      $products=[];
       foreach($productdata as $key=>$ids){
    
        $data = $this->getProductById($ids);
        if(empty($data['ean'])){ 
            return $error="SellerPartNumber Empty";
        }
        //print_r($data['shipping']); die();
        $profile_id = $this->getProductInfo($ids);
         if($profile_id['newegg_validation_error']=='Invalid'){
           $this->invalid_product($ids); 
            continue;
         }
     if($data['shipping']=='1'){ $shipping = "Default"; }else{ $shipping = "Free"; }
        $productdata=array(
              "SellerPartNumber"        =>  $data['ean'],
              "CountryCode"             =>  "USA",
              "Currency"                =>  "USD",
              "Shipping"                =>  $shipping,
              "SellingPrice"            =>  number_format((float)$data['price'], 2, '.', ''),
          );
        $product[]=$productdata;
    }
    $product_data['NeweggEnvelope']['Message']['Price']['Item']= $product;
    //print_r($product_data); die('io');
    return $product_data;
  }
}
