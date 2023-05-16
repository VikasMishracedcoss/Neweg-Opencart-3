<?php

class ModelExtensionModuleCedNeweggdata extends Model
{


    public function setAccountSession() 
    {
        $accountId = '';
        $this->adminSession->unsAccountId();
        $params = $this->_getRequest()->getParams();
        if(isset($params['account_id']) && $params['account_id'] > 0) {
            $accountId = $params['account_id'];
        }else{
            $accounts = $this->multiAccountHelper->getAllAccounts();
            if($accounts) {
                $accountId = $accounts->getFirstItem()->getId();
            }
        }
        $this->adminSession->setAccountId($accountId);
        return $accountId;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAccountSession() {
        $accountId = '';
        $accountId = $this->adminSession->getAccountId();
        if(!$accountId) {
            $accountId = $this->setAccountSession();
        }
        return $accountId;
    }

    /**
     * @param $SecretKey
     * @param $Authorization
     * @param $sellerId
     * @param $accountlocation
     * @param $url
     * @return mixed
     */
    public function apiValidation($SecretKey,$Authorization,$sellerId,$accountlocation,$url){
        switch ($accountlocation) {
            case 2:
                $accountDetail['url'] = "https://api.newegg.com/marketplace/can/";
                break;
            case 1:
                $accountDetail['url'] = "https://api.newegg.com/marketplace/b2b/";
                break;
            default:
                $accountDetail['url'] = "https://api.newegg.com/marketplace/";
                break;

        }
        $url = $accountDetail['url'] . $url . "?sellerid=" .$sellerId;
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Accept: application/json";
        $headers[] = "Authorization: " . $Authorization;
        $headers[] = "SecretKey: " . $SecretKey;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $serverOutput = curl_exec ($ch);
        curl_close ($ch);
        return json_decode($this->formatJson($serverOutput),true);
    }

    /**
     * @param $url
     * @param array $params
     * @param $currentAccount
     * @return mixed
     */

    
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
      //$response = $this->product_status($server_output,$currentAccount,); die();
     return $this->formatJson($server_output);
    }

    /**
     * @param $url
     * @param array $params
     * @param $currentAccount
     * @return mixed
     */
    public function putRequest($url,$currentAccount, $params = [])
    {
        $currentAccountDetail = $this->getAccountDetail($currentAccount['id']);
        $url = $currentAccountDetail['url'] . $url . "?sellerid=" . $currentAccountDetail['seller_id'];
        $headers = [];
        $headers[] = "Content-Type: application/json";
        $headers[] = "Accept: application/json";
        $headers[] = "Authorization: " . $currentAccountDetail['authorization_key'];
        $headers[] = "SecretKey: " . $currentAccountDetail['secret_key'];
        if(isset($params['body'])) {
            $params['body'] = stripslashes($params['body']);
            $putData = tmpfile();
            fwrite($putData, $params['body']);
            fseek($putData, 0);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_PUT, true);

        if (isset($params['body'])) {

            curl_setopt($ch, CURLOPT_POSTFIELDS, $params['body']);
            curl_setopt($ch, CURLOPT_INFILE, $putData);
            curl_setopt($ch, CURLOPT_INFILESIZE, strlen($params['body']));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $serverOutput = curl_exec ($ch);
        curl_close ($ch);
        //print_r($serverOutput); die();
        return json_decode($this->formatJson($serverOutput),true);
    }

    
        public function getRequest_sub_att($url, $currentAccount, $params = [])
        {
         if (!isset($params['append'])) {
            $params['append'] = '';
            }
        $currentAccountDetail = $this->getAccountDetail($currentAccount['id']);
        if (is_array($currentAccountDetail) && !empty($currentAccountDetail)) {

        //print_r($params); die(__DIR__);
            $url = $currentAccountDetail['url'] . $url . "?sellerid=" . $currentAccountDetail['seller_id'] . $params['append'];
            $headers = array();
            if (isset($params['json'])) {
                $headers[] = "Content-Type: application/json";
            }
            $headers[] = "Accept: application/json";
            $headers[] = "Authorization: " . $currentAccountDetail['authorization_key'];
            $headers[] = "SecretKey: " . $currentAccountDetail['secret_key'];
            if (isset($params['body'])) {
                $putString = stripslashes($params['body']);
                $putData = tmpfile();
                fwrite($putData, $putString);
                fseek($putData, 0);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            if (isset($params['body'])) {
                curl_setopt($ch, CURLOPT_PUT, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params['body']);
                curl_setopt($ch, CURLOPT_INFILE, $putData);
                curl_setopt($ch, CURLOPT_INFILESIZE, strlen($putString));
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $serverOutput = curl_exec($ch);
            curl_close($ch);
            return json_decode($this->formatJson($serverOutput), true);
            //print_r($serverOutput); die();
        }
        }


       public function getRequest_att($url, $currentAccount, $params = [])
        {
         if (!isset($params['append'])) {
            $params['append'] = '';
            }
        $currentAccountDetail = $this->getAccountDetail($currentAccount);
        if (is_array($currentAccountDetail) && !empty($currentAccountDetail)) {

        //print_r($params); die(__DIR__);
            $url = $currentAccountDetail['url'] . $url . "?sellerid=" . $currentAccountDetail['seller_id'] . $params['append'];
            $headers = array();
            if (isset($params['json'])) {
                $headers[] = "Content-Type: application/json";
            }
            $headers[] = "Accept: application/json";
            $headers[] = "Authorization: " . $currentAccountDetail['authorization_key'];
            $headers[] = "SecretKey: " . $currentAccountDetail['secret_key'];
            if (isset($params['body'])) {
                $putString = stripslashes($params['body']);
                $putData = tmpfile();
                fwrite($putData, $putString);
                fseek($putData, 0);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            if (isset($params['body'])) {
                curl_setopt($ch, CURLOPT_PUT, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params['body']);
                curl_setopt($ch, CURLOPT_INFILE, $putData);
                curl_setopt($ch, CURLOPT_INFILESIZE, strlen($putString));
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $serverOutput = curl_exec($ch);
            curl_close($ch);
            return json_decode($this->formatJson($serverOutput), true);
            //print_r($serverOutput); die();
        }
    }

    /**
     * @param $json_data
     * @return bool|mixed|string
     */
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

    /**
     * @param $subcatId
     * @param $account
     * @return array
     */
    public function getPropertyList($subcatId, $account)
    {  
        
        $body = '<NeweggAPIRequest> 
                    <OperationType>GetSellerSubcategoryPropertyRequest</OperationType>   
                    <RequestBody>     
                        <SubcategoryID>' . $subcatId . '</SubcategoryID>   
                    </RequestBody> 
                </NeweggAPIRequest>';
        $subcatFieldsArray = $this->getRequest_att('/sellermgmt/seller/subcategoryproperty', $account, ['body' => $body]);
       // echo '<pre>';
       //print_r($subcatFieldsArray['ResponseBody']['SubcategoryPropertyList']); die('pp');
        $propertyList = array();
        if (!isset($subcatFieldsArray['ResponseBody']['SubcategoryPropertyList'])) {
            return $propertyList;
        }
        $subcatFieldsResponse = $subcatFieldsArray['ResponseBody']['SubcategoryPropertyList'];
        $propertyList['all'] = $propertyList['required'] = "";
        foreach ($subcatFieldsResponse as $subcatFields) {
          
            $propertyList['name'] = $subcatFields['SubcategoryName'];
            $subcatFieldName = isset($subcatFields['PropertyName']) ? ($subcatFields['PropertyName']) : '';
            $propertyList['all'] = $propertyList['all'] == "" ? $subcatFieldName : $propertyList['all'] . "," . $subcatFieldName;

            if (isset($subcatFields['IsRequired']) && $subcatFields['IsRequired'] == '1') {
                $propertyList['required'] = $propertyList['required'] == "" ? $subcatFieldName : $propertyList['required'] . "," . $subcatFieldName;
             $subCatPropertyRes = $this->getPropertyDetail($subcatId, $subcatFieldName, $account);
            $propertyList['subcatPropertyValueResponse'] = $subCatPropertyRes;
            }
          
        }

        return $propertyList;
    }

    /**
     * @param $subcatId
     * @param $propertyName
     * @param $account
     * @return bool|mixed
     */
    public function getPropertyDetail($subcatId, $propertyName, $account)
    {

        try {
            $body = '<NeweggAPIRequest> 
                    <OperationType>GetSellerPropertyValueRequest</OperationType>   
                    <RequestBody>     
                        <SubcategoryID>' . $subcatId . '</SubcategoryID>
                        <PropertyName>' . $propertyName . '</PropertyName>
                    </RequestBody> 
                </NeweggAPIRequest>';
           //echo $body;
            //echo "ALL GOOD :"; print_r($body); die('000000');
            $response = $this->getRequest_sub_att('/sellermgmt/seller/propertyvalue', $account, ['body' => $body]);

           //print_r($response); die('000000');

            if (!$response)
                return false;

            $response = $response['ResponseBody']['PropertyInfoList'][0];
          
            return $response;

        } catch (\Exception $e) {
            echo $e->getMessage();die(__FILE__);
        }

    }

    /**
     * @param $industryCode
     * @param $currentAccount
     * @return mixed
     */
    public function getSubCategories($industryCode, $currentAccount)
    {
        //print_r($currentAccount);
        $body = '<NeweggAPIRequest>
                    <OperationType>GetSellerSubcategoryRequest</OperationType>
                    <RequestBody>
                        <GetItemSubcategory>
                            <IndustryCodeList> 
                                <IndustryCode>' . $industryCode . '</IndustryCode>       
                            </IndustryCodeList>
                            <Enabled>1</Enabled>
                        </GetItemSubcategory>
                    </RequestBody>
                </NeweggAPIRequest>';
        $subIndustries = $this->getRequest_att('sellermgmt/seller/subcategory', $currentAccount, ['body' => $body]);

        //print_r($subIndustries); die('99');
        return $subIndustries;
    }

    /**
     * @param $account
     * @return mixed
    */
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

    public function productUpload($value,$data_acc,$type = '')
    {
        //$url = 'datafeedmgmt/feeds/submitfeed';
        // echo '<pre>';
        // print_r($type);
        // die('u8');
        if ($type == 'update'){
            $url = 'datafeedmgmt/feeds/submitfeed';
            $jsonResponse = $this->postRequest($url,$data_acc,$value);
        }else{
            $url = 'datafeedmgmt/feeds/submitfeed';
            $jsonResponse = $this->postRequest($url,$data_acc,$value);
        } 
        $response = json_decode($jsonResponse, true);
        //print_r($response); die();
        return $response;
    }

    public function update_price($value,$data_acc,$type)
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

    

 public function product_status($RequestId,$acc_data)
    {   
        $url = "datafeedmgmt/feeds/result";
        $request ='';
      if(isset($RequestId)) {
            $RequestId = $RequestId;
          }
        $currentAccountDetail = $this->getAccountDetail($acc_data['id']);
          //print_r($currentAccountDetail); die();
        if (is_array($currentAccountDetail) && !empty($currentAccountDetail)) {
           
            $url = $currentAccountDetail['url'] . $url .'/'.$RequestId."?sellerid=" . $currentAccountDetail['seller_id'];
            //echo $url; die();
            $headers = array();
            $headers[] = "Accept: application/json";
            $headers[] = "Authorization: " . $currentAccountDetail['authorization_key'];
            $headers[] = "SecretKey: " . $currentAccountDetail['secret_key'];
            $request = curl_init();
            $curl = curl_init();
            curl_setopt($request, CURLOPT_URL, $url);
            curl_setopt($request, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($request, CURLOPT_HTTPHEADER , $headers);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($request);
            $errors = curl_error($request);
            curl_close($request);
            return json_decode($this->formatJson($response), true);
        }


      }

}