<?php

class ModelExtensionModuleCedNewEggSpecifics extends Model
{

    public function getListingTypes()
    {
        return array(
            array('label'=>'FixedPriceItem', 'value'=>'FixedPriceItem'),
            array('label'=>'AdType', 'value'=>'AdType'),
            array('label' =>'Half', 'value' => 'Half'),
            array('label'=>'Chinese', 'value'=>'Chinese'),
            array('label'=>'LeadGeneration', 'value'=>'LeadGeneration'),
        );
    }

    public function getShippingServiceTypes()
    {
        $types = array(
            array('value' => 'Flat', 'label' => 'Flat'),
            array('value' => 'Calculated', 'label' => 'Calculated'),
            array('value' => 'CalculatedDomesticFlatInternational',
                'label' => 'Calculated Domestic Flat International'),
            array('value' => 'FlatDomesticCalculatedInternational',
                'label' => 'Flat Domestic Calculated International'),
            array('value' => 'FreightFlat', 'label' => 'Freight Flat'),
            array('value' => 'NotSpecified', 'label' => 'Not Specified')
        );
        return $types;
    }

    public function getListingDurations()
    {
        return array(
            array(
                'label' => 'Good Till Cancelled',
                'value' =>'GTC'
            ),
            array(
                'label' =>'Days 1',
                'value' => 'Days_1'
            ),
            array(
                'label' => 'Days 10',
                'value' => 'Days_10'
            ),
            array(
                'label' => 'Days 120',
                'value' => 'Days_120'
            ),
            array(
                'label' => 'Days 14',
                'value' => 'Days_14'
            ),
            array(
                'label' => 'Days 21',
                'value' => 'Days_21'
            ),
            array(
                'label' => 'Days 3',
                'value' => 'Days_3'
            ),
            array(
                'label' => 'Days 30',
                'value' => 'Days_30'
            ),
            array(
                'label' => 'Days 7',
                'value' => 'Days_7'
            ),
            array(
                'label' => 'Days 90',
                'value' => 'Days_90'
            ),
            array(
                'label' => 'Days 60',
                'value' => 'Days_60'
            ),
            array(
                'label' => 'Days 5',
                'value' => 'Days_5'
            ),
        );
    }

    public function getneweggLocations()
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

    public function getShippingServices()
    {
        $shippingServices = array(
            'domestic' => array(),
            'international' => array()
        );
        $details = $this->getShippingDetails();
        if (isset($details['ShippingServiceDetails']) && !empty($details['ShippingServiceDetails'])) {
            $domesticShipping = array();
            $internationalShipping = array();
            foreach ($details['ShippingServiceDetails'] as $detail) {
                if (!isset($detail['InternationalService'])) {
                    $domesticShipping[] = array(
                        'value' => $detail['ShippingService'],
                        'label' => $detail['Description']
                    );
                } else {
                    $internationalShipping[] = array(
                        'value' => $detail['ShippingService'],
                        'label' => $detail['Description']
                    );
                }
            }
            $shippingServices['domestic'] = $domesticShipping;
            $shippingServices['international'] = $internationalShipping;
        }
        return $shippingServices;
    }
    public function getShipToLocations()
    {
        $locations = array();
        $details = $this->getShippingDetails();
        if (isset($details['ShippingLocationDetails']) && !empty($details['ShippingLocationDetails'])) {
            foreach ($details['ShippingLocationDetails'] as $detail) {
                if (isset($detail['ShippingLocation'])) {
                    $locations[] = array(
                        'value' => $detail['ShippingLocation'],
                        'label' => $detail['Description']
                    );
                }
            }
        }
        return $locations;
    }
    public function getExcludeShippingLocations()
    {
        $locations = array();
        $details = $this->getShippingDetails();
        if (isset($details['ExcludeShippingLocationDetails'])
            && !empty($details['ExcludeShippingLocationDetails'])) {
            foreach ($details['ExcludeShippingLocationDetails'] as $detail) {
                if (isset($detail['Location'])) {
                    $locations[] = array(
                        'value' => $detail['Location'],
                        'label' => $detail['Description']
                    );
                }
            }
        }
        return $locations;
    }


   public function getaddshipping($data)
    {
        if(isset($data)){
        $shiping = json_encode($data);
        $sql = "UPDATE `".DB_PREFIX."cednewegg_specifics` SET value='".$shiping."'
        WHERE `key`='shipping_details'";
        //echo $sql; die(__FILE__);     
        $res = $this->db->query($sql);
        }
    return true;
    }
    public function getShippingDetails()
    {
        $site_id = $this->config->get('cednewegg');
        
        $sql = "SELECT `value` FROM `".DB_PREFIX."cednewegg_specifics` 
        WHERE `key`='shipping_details'";
        $res = $this->db->query($sql);
        
        if ($res->num_rows && isset($res->row['value'])) {
            $methods = json_decode($res->row['value'], true);
        } else {
            $methods = array();
        }

        if (!empty($methods)) {
            $result = $methods;
        } else {
            $this->load->library('cednewegg');
            $newegg_lib = Cednewegg::getInstance($this->registry);
            $this->db->query("DELETE FROM `".DB_PREFIX."cednewegg_specifics` WHERE `key`='shipping_details' AND `site_id`='".$site_id."'");
            $token = $this->config->get('cednewegg_token');
            $variable = "GetneweggDetails";
            $requestBody = '<?xml version="1.0" encoding="utf-8"?> 
                      <GetneweggDetailsRequest xmlns="urn:newegg:apis:eBLBaseComponents"> 
                        <RequesterCredentials> 
                          <neweggAuthToken>' . $token . '</neweggAuthToken> 
                        </RequesterCredentials> 
                        <DetailName>ShippingCarrierDetails</DetailName> 
                        <DetailName>ShippingServiceDetails</DetailName> 
                        <DetailName>ShippingLocationDetails</DetailName> 
                        <DetailName>ExcludeShippingLocationDetails</DetailName> 
                      </GetneweggDetailsRequest>';

            $response = $newegg_lib->sendHttpRequest($requestBody, $variable);

            $data = $newegg_lib->xml2array($response);

            if (isset($data['GetneweggDetailsResponse']['Ack'])
                && $data['GetneweggDetailsResponse']['Ack'] == 'Success') {
                $result = $data['GetneweggDetailsResponse'];
            } else {
                $result = array();
            }
            $this->db->query(
                "INSERT INTO `".DB_PREFIX."cednewegg_specifics` SET 
                `key`='shipping_details',
                `value`='".$this->db->escape(json_encode($result))."',
                `site_id`='".$site_id."'
                "
            );
        }
        return $result;
    }
    public function getPaymentDetails()
    {
        $site_id = $this->config->get('cednewegg_site_id');
        $sql = "SELECT `value` FROM `".DB_PREFIX."cednewegg_specifics` 
        WHERE `key`='payment_methods' AND `site_id`='".$site_id."'";
        $res = $this->db->query($sql);
        if ($res->num_rows && isset($res->row['value'])) {
            $methods = json_decode($res->row['value'], true);
        } else {
            $methods = array();
        }
        if (!empty($methods)) {
            $result = $methods;
        } else {
            $this->load->library('cednewegg');
            $newegg_lib = Cednewegg::getInstance($this->registry);
            $this->db->query("DELETE FROM `".DB_PREFIX."cednewegg_specifics` WHERE `key`='payment_methods' AND `site_id`='".$site_id."'");

            $token = $this->config->get('cednewegg_token');
            $variable = "GetCategoryFeatures";
            $requestBody = '<?xml version="1.0" encoding="utf-8"?>
                    <GetCategoryFeaturesRequest xmlns="urn:newegg:apis:eBLBaseComponents">
                      <RequesterCredentials>
                        <neweggAuthToken>' . $token . '</neweggAuthToken>
                      </RequesterCredentials>
                      <DetailLevel>ReturnAll</DetailLevel>
                      <FeatureID>PaymentMethods</FeatureID>
                    </GetCategoryFeaturesRequest>';

            $response = $newegg_lib->sendHttpRequest($requestBody, $variable);
            $data = $newegg_lib->xml2array($response);


            if (isset($data['GetCategoryFeaturesResponse']['Ack'])
                && $data['GetCategoryFeaturesResponse']['Ack'] == 'Success') {
                $result = $data['GetCategoryFeaturesResponse']['SiteDefaults']['PaymentMethod'];
            } else {
                $result = array();
            }
            $this->db->query(
                "INSERT INTO `".DB_PREFIX."cednewegg_specifics` SET 
                `key`='payment_methods',
                `value`='".$this->db->escape(json_encode($result))."',
                `site_id`='".$site_id."'
                "
            );
        }
        return $result;
    }
    public function getRefundOptions()
    {
        return array(
            'MoneyBack' => 'Money Back',
            'MoneyBackOrExchange' => 'Money Back Or Exchange',
            'MoneyBackOrReplacement' => 'Money Back Or Replacement',
        );
    }
    public function getReturnDetails()
    {
        return array(
            'ReturnsNotAccepted' => 'Returns Not Accepted',
            'ReturnsAccepted' => 'Returns Accepted',
        );
    }
    public function getReturnsWithinOptions()
    {
        return array(
            'Days_14' => '14 Days',
            'Days_30' => '30 Days',
            'Days_60' => '60 Days',
        );
    }
    public function getShippingCostPaidByOptions()
    {
        return array(
            'Buyer' => 'Buyer',
            'Seller' => 'Seller',
        );
    }

    public function getShippingCarriers()
    {
        $carriers = array();
        $shippingDetails = $this->getShippingDetails();
        //print_r($shippingDetails); die(__FILE__);
        if (isset($shippingDetails['cednewegg_OpencartCarrierTitle'])
            && !empty($shippingDetails['cednewegg_OpencartCarrierTitle'])) {
                $carriers[] = array(
                    'id' => '1',
                    'name' => $shippingDetails['cednewegg_OpencartCarrierTitle'],
                );
        }
        return $carriers;
    }

    public function getCatchAttributes($catId = null)
    {
        try {

            $attributes_list = array();
            $catch_attributes = array(
                'required_attributes' => array(),
                'optional_attributes' => array(),
            );

            $sql = "SELECT * FROM " . DB_PREFIX . "cedcatch_attributes where `category_id` = '".$catId."' ";
            $query = $this->db->query($sql);
            $res = $query->rows;
            if (count($res) == 0) {
                $params = array(
                    'hierarchy' => $catId,
                );
                $method = 'products/attributes';
                $this->load->library('cedcatch');
                $cedcatch = CedCatch::getInstance($this->registry);
                $result = $cedcatch->WGetRequest($method, $params);
                if(isset($result['success'],$result['message']['attributes']) && $result['success']) {
                    $attributes = $result['message']['attributes'];
                    $sql = "INSERT INTO `".DB_PREFIX."cedcatch_attributes` (
                    category_id, `attribute_code`,`attribute_label`,
                    default_value, `required`,`is_variant`,
                    attribute_type, `values_list`,`values`
                    ) 
                VALUES" ;
                    foreach ($attributes as $attribute) {
                       $attr_code =  $attribute['code'];
                       $attr_label =  $attribute['label'];
                       $attr_default =  $attribute['default_value'];
                       $required =  $attribute['required'];
                       $is_variant =  $attribute['variant'];
                       $attr_type =  $attribute['type'];
                       $values_list =  $attribute['values_list'];
                       $values =  $attribute['values'];

                        $attributes_list[] = array(
                            'category_id' => $catId,
                            'attribute_code' => $attr_code,
                            'attribute_label' => $attr_label,
                            'default_value' => $attr_default,
                            'required' => $required,
                            'is_variant' => $is_variant,
                            'attribute_type' => $attr_type,
                            'values_list' => $values_list,
                            'values' => $values,
                        );

                        $sql .= "('".$catId."', '".$attr_code."','".$attr_label."','".$attr_default."','".(int)$required."','".(int)$is_variant."','".$attr_type."','".$values_list."','".json_encode($values)."'),";
                    }
                    $sql = rtrim($sql, ',');
                    $this->db->query($sql);

                } else {
                    return $result;
                }
            } else {
                $attributes_list = $res;
            }
            if(!empty($attributes_list)) {
                foreach ($attributes_list as $attr_list) {
                    if(isset($attr_list['required']) && $attr_list['required']) {
                        $catch_attributes['required_attributes'][] = $attr_list;
                    } else {
                        $catch_attributes['optional_attributes'][] = $attr_list;
                    }
                }
                return array(
                  'success' => true,
                  'message' => $catch_attributes
                );
            }
            return array(
                'success' => false,
                'message' => 'Failed to get Attributes'
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    public function getCatchAttributeOptions($attr_code, $value_list_code, $filter_name = '')
    {
        $sql = "SELECT `value_code`, `value_label` FROM " . DB_PREFIX . "cedcatch_attribute_options where `attribute_code` = '".$attr_code."' AND `values_list` = '".$value_list_code."'";
        $query = $this->db->query($sql);
        $res = $query->rows;
        if(count($res) == 0) {
            $params = array(
                'code' => $value_list_code,
            );
            $method = 'values_lists';
            $this->load->library('cedcatch');
            $cedcatch = CedCatch::getInstance($this->registry);
            $result = $cedcatch->WGetRequest($method, $params);
            if(isset($result['success']) && $result['success']) {
               if(isset($result['message']['values_lists'][0]['values']) && !empty($result['message']['values_lists'][0]['values'])) {
                   $sql = "INSERT INTO `".DB_PREFIX."cedcatch_attribute_options` (
                    category_id, `attribute_code`,`values_list`,
                    default_value, `value_code`,`value_label`
                    ) 
                VALUES" ;
                   foreach ($result['message']['values_lists'][0]['values'] as $value) {
                       $sql .= "('', '".$attr_code."','".$value_list_code."','','".$value['code']."','".$value['label']."'),";
                   }
                   $sql = rtrim($sql, ',');
                   $this->db->query($sql);
               }
            }
        }
        $searchsql = "SELECT `value_code`, `value_label` FROM " . DB_PREFIX . "cedcatch_attribute_options 
        where `attribute_code` = '".$attr_code."' 
        AND `values_list` = '".$value_list_code."'
        AND (`value_code` LIKE '%".strip_tags(html_entity_decode($this->db->escape($filter_name), ENT_QUOTES, 'UTF-8'))."%' 
        OR `value_label` LIKE '%".strip_tags(html_entity_decode($this->db->escape($filter_name), ENT_QUOTES, 'UTF-8'))."%')
        LIMIT 20";
        $query_result = $this->db->query($searchsql);
        $res = $query_result->rows;
        return $res;

    }

    public function getStoreAttributes()
    {
        $product_fields = array();
            $colomns = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product`;");
            if($colomns->num_rows) {
                $product_fields = $colomns->rows;
            }
            $this->array_sort_by_column($product_fields, 'Field');

        return $product_fields;
    }

    public function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
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