<?php 
class ControllerExtensionModuleCedNewEggProfile extends Controller
{
  private $error = array();
    private $helper;
    private $session_token_key = '';
    private $session_token = '';
    private $module_path = '';

    public function __construct($registry)
    {
        // pass `$registry` to parent `__construct`
        parent::__construct($registry);

        if (VERSION >= 2.0 && VERSION <= 2.2) {
            $this->session_token_key = 'token';
            $this->session_token = $this->session->data['token'];

            $this->extension_path = 'extension/module';
            $this->module_path = 'module';
        } else if (VERSION < 3.0) {
            $this->session_token_key = 'token';
            $this->session_token = $this->session->data['token'];

            $this->extension_path = 'extension/extension';
            $this->module_path = 'extension/module';
        } else {
            $this->session_token_key = 'user_token';
            $this->session_token = $this->session->data['user_token'];

            $this->extension_path = 'marketplace/extension';
            $this->module_path = 'extension/module';
        }
        $this->load->library('cednewegg');
        $this->helper = CedNewEgg::getInstance($this->registry);
    }

    public function index()
    {
        $this->language->load('extension/module/cednewegg/profile');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/cednewegg/profile');
        $this->getList();
    }

    protected function getList()
    {   //die('2');
        $data = array();
        if (VERSION < 3.0) {
            $data = $this->language->load('extension/module/cednewegg/profile');
        } else {
            $this->language->load('extension/module/cednewegg/profile');
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'profile_name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . $url, 'SSL')
        );

        $data['insert'] = $this->url->link('extension/module/cednewegg/profile/insert', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['delete'] = $this->url->link('extension/module/cednewegg/profile/delete', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'); 

        $data['profiles'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $profile_total = $this->model_extension_module_cednewegg_profile->getTotalProfiles();

        $results = $this->model_extension_module_cednewegg_profile->getProfiles($filter_data);
        $this->load->model('extension/module/cednewegg');
      
        foreach ($results as $result) {
            $product_count = $this->model_extension_module_cednewegg_profile->getTotalProductByProfileId($result['id']);
            $data['profiles'][] = array(
                'id' => $result['id'],
               'profile_name'      => $result['profile_name'],
               'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'product_count' => $product_count,
                'selected'       => isset($this->request->post['selected']) && in_array($result['id'], $this->request->post['selected']),
                'edit' => $this->url->link('extension/module/cednewegg/profile/update', $this->session_token_key . '=' . $this->session_token . '&id=' . $result['id'] . $url, 'SSL'),
                'deletebyid' => $this->url->link('extension/module/cednewegg/profile/deletebyid', $this->session_token_key . '=' . $this->session_token . '&id=' . $result['id'] . $url, 'SSL')
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_list'] = $this->language->get('text_list');
        $data['column_profile_name'] = $this->language->get('column_profile_name');
        $data['column_id'] = $this->language->get('column_id');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');
        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_edit'] = $this->language->get('button_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $this->load->model('catalog/attribute');
        $data_attribute = $this->model_catalog_attribute->getAttributes();
        if (isset($data_attribute)) {
           // print_r($data_attribute); die(__DIR__); 
         $data['opencart_attribute'] = $data_attribute;
        } else {
            $data['opencart_attribute'] = '';
        }
       
        
        $url = '';
        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_profile_name'] = $this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . '&sort=profile_name' . $url, 'SSL');
        $data['sort_id'] = $this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . '&sort=id' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . '&sort=status' . $url, 'SSL');
        
        

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $profile_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);
        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($profile_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($profile_total - $this->config->get('config_limit_admin'))) ? $profile_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $profile_total, ceil($profile_total / $this->config->get('config_limit_admin')));
   

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        // echo '<pre>';
        // print_r($data);
        // die(__DIR__);
       


        if (VERSION > '3.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/profile/profile_list', $data));
        } 
    }

    public function insert()
    {
        if (VERSION < 3.0) {
            $data = $this->language->load('extension/module/cednewegg/profile');
        }


        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/cednewegg/profile');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_cednewegg_profile->addProfile($this->request->post);
        /*
        print_r($this->request->post());
        die(__DIR__);
        */

            
            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->load->model('extension/module/cednewegg/profile');
        $ids = array();
        if (isset($this->request->post['selected']) && is_array($this->request->post['selected'])) {
            $ids = $this->request->post['selected'];
        } else {
            $this->error['warning'] = 'Please select Id(s) to delete';
        }
        foreach ($ids as $id) {
            $this->model_extension_module_cednewegg_profile->deleteProfile($id);
            $this->session->data['success'] = 'Profile(s) Deleted';
        }
        $this->getList();
    }

    public function deletebyid()
    {
        $this->load->model('extension/module/cednewegg/profile');
        $ids = array();
        if (isset($this->request->get['id']) && $this->request->get['id']) {
            $this->model_extension_module_cednewegg_profile->deleteProfile($this->request->get['id']);
            $this->session->data['success'] = 'Profile(s) Deleted';
        } else {
            $this->error['warning'] = 'Failed to delete Id ' . $this->request->get['id'];
        }
        $this->getList();
    }

    protected function getForm()
    {   //die('3');
        $this->load->model('extension/module/cednewegg/profile');
        $this->load->model('extension/module/cednewegg/specifics');
        if (VERSION < 3.0) {
            $data = $this->language->load('extension/module/cednewegg/profile');
        } else {
            $this->language->load('extension/module/cednewegg/profile');
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

       
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['profile_name'])) {
            $data['error_profile_name'] = $this->error['profile_name'];
        } else {
            $data['error_profile_name'] = array();
        }

        if (isset($this->error['store'])) {
            $data['error_store'] = $this->error['store'];
        } else {
            $data['error_store'] = array();
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . $url, 'SSL')
        );

        if (!isset($this->request->get['id'])) {
            $data['action'] = $this->url->link('extension/module/cednewegg/profile/insert', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        } else {
            $data['profile_id'] = $this->request->get['id'];
            $data['action'] = $this->url->link('extension/module/cednewegg/profile/update', $this->session_token_key . '=' . $this->session_token . '&id=' . $this->request->get['id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');

        $data[$this->session_token_key] = $this->session_token;

        $profile_info = array();

    

        if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $profile_info = $this->model_extension_module_cednewegg_profile->getProfile($this->request->get['id']);
        }
        // Store Categories
        $this->load->model('catalog/category');
        $data['product_categories'] = array();
        $this->load->model('extension/module/cednewegg/template');
        $data['description_templates'] = $this->model_extension_module_cednewegg_template->getTemplates();

        $this->load->model('catalog/attribute');
        $data_attribute = $this->model_catalog_attribute->getAttributes();
        if (isset($data_attribute)) {
           //print_r($data_attribute); die(__DIR__); 
         $data['opencart_attribute'] = $data_attribute;
        } else {
            $data['opencart_attribute'] = '';
        }
        $this->load->model('extension/module/cednewegg');
        $dataacc = $this->model_extension_module_cednewegg->account_details('cednewegg');
        $industryCode = $dataacc['root_cat'];
        $data = include(DIR_SYSTEM .'library/cednewegg/SubCat/'.$industryCode.'.php');
        //print_r($data); die();
        if (isset($data)) {         
           $data['sub_category'] = $data;
        } else {
            $data['sub_category'] = '';
        }
        $this->load->model('extension/module/cednewegg');
        $data_acc = $this->model_extension_module_cednewegg->accountdetails('cednewegg');
        //print_r($data_acc); die();
        $data['data_acc'] = $data_acc;


        //print_r($get_req_profile); die();
        //if(empty($get_profile_attr)){ 
        if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $profile_info = $this->model_extension_module_cednewegg_profile->getProfile($this->request->get['id']);
        }
          //print_r($profile_info); die();
        if (isset($this->request->post['newegg_profile_category'])) {
            $data['profile_cat'] = $this->request->post['newegg_profile_category'];
        } elseif (!empty($profile_info)) {
           $data['profile_cat'] = $profile_info['profile_cat'];
        } else {
            $data['profile_cat'] = 1;
        }
       if (isset($this->request->post['newegg_req_attribute'])) {
            $data['newegg_req_attribute'] = $this->request->post['newegg_req_attribute'];
        } elseif (!empty($profile_info)) {
            $data['newegg_req_attribute'] = $profile_info['profile_newegg_req_attribute'];
        } else {
            $data['newegg_req_attribute'] = '';
        }

        if (isset($this->request->post['profile_newegg_opt_category'])) {
            $data['profile_newegg_opt_category'] = $this->request->post['newegg_req_attribute'];
        } elseif (!empty($profile_info)) {
            $data['profile_newegg_opt_category'] = $profile_info['profile_newegg_opt_category'];

        } else {
            $data['profile_newegg_opt_category'] = '';
        }



        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['profile_language'])) {
            $data['profile_language'] = $this->request->post['profile_language'];
        } elseif (isset($profile_info['profile_language'])) {
            $data['profile_language'] = $profile_info['profile_language'];
        } else {
            $data['profile_language'] = 1;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($profile_info)) {
            $data['status'] = $profile_info['status'];
        } else {
            $data['status'] = 1;
        }


        if (isset($this->request->post['profile_name'])) {
            $data['profile_name'] = $this->request->post['profile_name'];
        } elseif (!empty($profile_info)) {
            $data['profile_name'] = $profile_info['profile_name'];
        } else {
            $data['profile_name'] = '';
        }

        $item_conditions = array();

        $shippingServices = $this->model_extension_module_cednewegg_specifics->getShippingServices();
        $data['listing_type'] = $this->model_extension_module_cednewegg_specifics->getListingTypes();
        $data['item_conditions'] = $item_conditions;
        $data['listing_durations'] = $this->model_extension_module_cednewegg_specifics->getListingDurations();
        $data['payment_methods'] = $this->model_extension_module_cednewegg_specifics->getPaymentDetails();
        $data['return_accepted'] = $this->model_extension_module_cednewegg_specifics->getReturnDetails();
        $data['refund_options'] = $this->model_extension_module_cednewegg_specifics->getRefundOptions();
        $data['return_within'] = $this->model_extension_module_cednewegg_specifics->getReturnsWithinOptions();
        $data['shipping_cost_paidby'] = $this->model_extension_module_cednewegg_specifics->getShippingCostPaidByOptions();
        $data['shipping_service_type'] = $this->model_extension_module_cednewegg_specifics->getShippingServiceTypes();
        $data['free_shipping'] = array(
            'false' => 'No',
            'true' => 'Yes'
        );

        $data['domestic_shipping_services'] = $shippingServices['domestic'];
        $data['international_shipping_services'] = $shippingServices['international'];
       // $data['profile_specifics'] = $profile_specifics;
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        

        $data['products'] = array();
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->getProducts();

        foreach ($results as $result) {
      
            if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
            }
   
       $data['products'][] = array(
                'product_id'   => $result['product_id'],
                'sku'          => $result['sku'],
                'product'      => $this->getselectedproduct($result['product_id']),
                'name'         => $result['name'],
                'model'        => $result['model'],
                'price'        => $result['price'],
                'image'        => $image,
                'quantity'     => $result['quantity'],
                'status'       => $result['status'],
            );
    }
      
     $data['url_cat'] =$this->url->link('extension/module/cednewegg/profile/getCategory_attribute', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['url_cat_opional'] =$this->url->link('extension/module/cednewegg/profile/getCategory_attr_optional', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['url_vat_opional'] =$this->url->link('extension/module/cednewegg/profile/varient_attribute', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['url_vat_opionals'] =$this->url->link('extension/module/cednewegg/profile/varient_attr_mapping', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['id'] = '';
        if (isset($this->request->get['id'])) {
            $data['id'] = $this->request->get['id'];
        }
   // echo '<pre>';
   // print_r($data);
   // die();
       
        if (VERSION > '3.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/profile/profile_form', $data));
        }
    }
  
    public function getselectedproduct($product_id)
     { 
       if (isset($this->request->get['id'])) { 
      $profile_info = $this->model_extension_module_cednewegg_profile->getProfile($this->request->get['id']);
       //print_r($profile_info); die();   
      if (!empty($profile_info['profile_product']))
        {     
           foreach($profile_info['profile_product'] as $ids_products)
           {  
            //print_r($ids_products); die();
              if($ids_products['product_id'] == $product_id)
              {
               return $selected = 'selected'; 
              }else{ $selected = ''; 
              }
            }
        }
       }
    }


public function getCategory_attribute()
{   
    $html=array();
    $subcatId = $_POST['id'];
    $acc_id = $_POST['acc_id'];

    $this->load->model('extension/module/cednewegg');
    $this->load->model('extension/module/cednewegg/data');
    $this->load->model('extension/module/cednewegg/profile');

    $industryCode = $this->model_extension_module_cednewegg_profile->getroot_cat($acc_id);
    if(isset($_POST['profile_id']))
    { 
    $profile_id = $_POST['profile_id'];
    $get_optional_attr_data = $this->model_extension_module_cednewegg_profile->getProfile($profile_id);
    if(isset($get_optional_attr_data['profile_newegg_req_attribute']['required_attributes'])){
    $req_opt = $get_optional_attr_data['profile_newegg_req_attribute']['required_attributes'];
        //print_r($req_opt); 
      }
    }
    //print_r($req_opt); die(__FILE__);
    $get_profile_attr = $this->model_extension_module_cednewegg_profile->getchild_attr($subcatId);
    if(!empty($subcatId)){ 
    if(empty($get_profile_attr)){ 
    if (isset($data_acc)) {        
           $data_acc;
    }
    $profile_sub_attr = $this->model_extension_module_cednewegg_data->getPropertyList($subcatId,$data_acc);
    $get_profile_attr = $this->model_extension_module_cednewegg_profile->addchild_attr($subcatId,$profile_sub_attr );
     }
    }
    //print_r($get_profile_attr['required_attribute']); die();
    if(isset($get_profile_attr['subattr'])){
     $subattrs= explode(",",$get_profile_attr['subattr']);
    }
    if(isset($get_profile_attr['profile_attr'])){
      $get_optinal_profile= explode(",",$get_profile_attr['profile_attr']);
    }
    
    $subattr = $this->model_extension_module_cednewegg_profile->profile_label();
    $pre_attr = array("SellerPartNumber","Manufacturer","ManufacturerPartsNumber",
    "WebsiteShortTitle","ItemWeight","Shipping","SellingPrice","ShippingRestriction","Inventory","ProductDescription");

    if(!empty($get_profile_attr['required_attribute'])){
        //print_r($get_profile_attr['required_attribute']); die();
        $get_req_profile= explode(",",$get_profile_attr['required_attribute']);
        $pre_attr=array_merge($pre_attr,$get_req_profile);
    }
   // print_r($pre_attr); die();
 
   foreach($pre_attr as $key=>$preattr)
    {    $select='';
         $default=''; 
         $req_val='';
       if(isset($req_opt)){ 
             foreach($req_opt as $req_opts){
               if($preattr  == $req_opts['newegg_attribute_name']) {
                  $req_val   = $req_opts['opencart_attribute_code']; 
                  $default   = $req_opts['default'];
            } }
        }
    $html[$key] = '<div class="row">
    <label class="col-md-4">
    <input type="text" class="form-control" name="newegg_req_attribute['.$key.'][]" value="'.$preattr.'" readonly required></label>
    <label class="col-md-4">
    <select class="form-control selectVal" onchange="selectVal('.$key.')" id="selectVal'.$key.'" name="newegg_req_attribute['.$key.'][]" style="text-transform: capitalize;">
    <option value="">Select Opencart Attribute / Default</option>';
    if(!empty($get_req_profile)){ 
       if($preattr == $get_profile_attr['required_attribute']){
        foreach($subattrs as $data_attrs) 
          {   
        $req_vals = strcmp($data_attrs,$req_val);
        if($req_vals==0){ 
        $html[$key] .='<option value="'.$data_attrs.'"selected>'.$data_attrs.'</option>';
        }else{   
            $html[$key] .='<option value="'.$data_attrs.'">'.$data_attrs.'</option>';
        } }
    }else{  
         foreach($subattr as $data_attr) {  
            $arraydata = implode(',',$data_attr);
            if($req_val==$arraydata){
        $html[$key] .='<option value="'.$arraydata.'" selected>'.$arraydata.'</option>';
            }else{
        $html[$key] .='<option value="'.$arraydata.'">'.$arraydata.'</option>';
            }
          }
        } 
     }else{ 
        foreach($subattr as $data_attr) 
        {   
          $arraydata = implode(',',$data_attr);
            if($req_val==$arraydata){
        $html[$key] .='<option value="'.$arraydata.'" selected>'.$arraydata.'</option>';
            }else{
        $html[$key] .='<option value="'.$arraydata.'">'.$arraydata.'</option>';
            }
        }
    }
    $html[$key] .='</select></label><label class="col-md-4"><input type="text" name="newegg_req_attribute['.$key.'][]" class="form-control" id="sel'.$key.'" value="'.$default.'" type="text" placeholder="Set Default"></div>';
 }
  //print_r($html); die();
  echo json_encode($html);  

}

public function getCategory_attr_optional()
{   
    $html=array();
    $subcatId   = $_POST['id'];
    $acc_id     = $_POST['acc_id'];

    $this->load->model('extension/module/cednewegg/profile');
    $industryCode = $this->model_extension_module_cednewegg_profile->getroot_cat($acc_id);
    //print_r($industryCode); die();
   
    if(isset($_POST['profile_id'])){ 
    $profile_id = $_POST['profile_id'];
    $get_optional_attr_data = $this->model_extension_module_cednewegg_profile->getProfile($profile_id);
   if(isset($get_optional_attr_data['profile_newegg_opt_category']['optional_attributes'])){
     $opt = $get_optional_attr_data['profile_newegg_opt_category']['optional_attributes'];
   }
    //print_r($opt); die();
    }
    
    $this->load->model('extension/module/cednewegg/data');
    $get_profile_attr = $this->model_extension_module_cednewegg_profile->getchild_attr($subcatId);
   if(!empty($subcatId)){ 
   if(empty($get_profile_attr)){ 
    $this->load->model('extension/module/cednewegg');
    $data_acc= $this->model_extension_module_cednewegg->account_details('cednewegg');
    if (isset($data_acc)) {        
           $data_acc;
    }
    $profile_sub_attr = $this->model_extension_module_cednewegg_data->getPropertyList($subcatId,$data_acc);
    $get_profile_attr = $this->model_extension_module_cednewegg_profile->addchild_attr($subcatId,$profile_sub_attr );
    }



    $subattr= explode(",",$get_profile_attr['profile_attr']);
    //print_r(subattr); die(); 
    $this->load->model('catalog/attribute');
    $data_attribute = $this->model_catalog_attribute->getAttributes();
        
   
    foreach($subattr as $key=>$preattr){
        $opt_val='';
        $default='';
        if(!empty($opt)){ 
        foreach($opt as $opts){
            //print_r($opts); die();
            if($preattr == $opts['newegg_attribute_name']){
              $opt_val  = $opts['opencart_attribute_code']; 
              $default  = $opts['default'];
             } 
            }
        }
     $subattr = $this->model_extension_module_cednewegg_profile->profile_label();
    $html[$key] = '<div class="row"><label class="col-md-4">
    <input type="text" class="form-control" name="newegg_opt_attribute['.$key.'][]" value="'.$preattr.'" readonly></label>
    <label class="col-md-4">
    <select class="form-control selectVal" name="newegg_opt_attribute['.$key.'][]"style="text-transform: capitalize;">
    <option value="">Select Opencart Attribute / Default</option>';
     foreach($subattr as $data_attr) { 
        $arraydata = implode(',',$data_attr);
      if($opt_val==$arraydata){
        $html[$key] .='<option value="'.$arraydata.'" selected>'.$arraydata.'</option>';
        }else{
            $html[$key] .='<option value="'.$arraydata.'">'.$arraydata.'</option>';
            }
        }
    $html[$key] .='</select></label><label class="col-md-4"><input type="text" name="newegg_opt_attribute['.$key.'][]" class="form-control" value="'.$default.'" type="text" placeholder="Set Default"></div>';
    }
  //print_r($html); die();
  echo json_encode($html);  
  }
}

public function varient_attribute()
{  
    $html=array();
    $default='';
    $subcatId   = $_POST['id'];
    $acc_id     = $_POST['acc_id'];
    $profile_id =  $_POST['profile_id'];
    $html=[];
    if($subcatId!=''){ 
    $this->load->model('extension/module/cednewegg/profile');
    $industryCode = $this->model_extension_module_cednewegg_profile->getroot_cat($acc_id);
    $data = include(DIR_SYSTEM . 
        'library/cednewegg/SubCat/SubCatFields/'.$industryCode.'/'.$subcatId.'.php');
    foreach($data as $vat_var){
        if($vat_var['IsGroupBy']=='1'){
           $vat[] = $vat_var;
        }
    }
    $this->load->model('extension/module/cednewegg/profile');
    $subattr = $this->model_extension_module_cednewegg_profile->profile_label();
   if(isset($profile_id)){ 
    $get_optional_attr_data = $this->model_extension_module_cednewegg_profile->getProfile($profile_id);
     $varient_attribute = $get_optional_attr_data['profile_varient_attribute']; 
    }

    $store_options = $this->model_extension_module_cednewegg_profile->getStoreOptions();
    $options = $store_options['options'];
    //echo "<pre>"; print_r($options); die();
         if(isset($vat)){
    foreach($vat as $key=>$vat_var_opt){
        $PropertyName = $vat_var_opt['PropertyName'];
        $data_var = include(DIR_SYSTEM . 
        'library/cednewegg/SubCat/SubCatFieldValues/'.$industryCode.'/'.$subcatId.'/'.$PropertyName.'.php');

       $this->model_extension_module_cednewegg_profile->ProfilePropertyName($profile_id,$PropertyName);
        //print_r($data_var['PropertyName']); die(__FILE__);
       // foreach($data_var['PropertyValueList'] as $key=>$datavar){
        $opt_val='';
        $default='';
        
        if(!empty($varient_attribute)){ 
        foreach($varient_attribute['varient_attributes'] as $var_opts){
            if($data_var['PropertyName'] == $var_opts['varient_attributes']){
              $opt_val  = $var_opts['opencart_attribute']; 
              $default  = $var_opts['default'];
             }
            }
        }
    //print_r($opt_val); die();
    $html[$key] = '<div class="row"><label class="col-md-4">
    <input type="text" class="form-control" name="varient_attribute['.$key.'][]" 
    value="'.$data_var['PropertyName'].'" readonly></label>
    <label class="col-md-4">
    <select class="form-control select_Value" id="select_value'.$key.'" onclick="select_Value('.$key.')" name="varient_attribute['.$key.'][]"style="text-transform: capitalize;">
    <option value="">Select Opencart Attribute / Default</option>';
       foreach($options as $options_attr) {
        if($options_attr['type']=='select'){ $name = $options_attr['name'].'[select]'; }
       // $arraydata = implode(',',$data_attr);
        //$html[$key] .='<option value="'.$arraydata.'">'.$arraydata.'</option>';
        if($opt_val==$options_attr['option_id']){
            $html[$key] .='<option value="'.$options_attr['option_id'].'" selected>'.$name.'</option>';
        }else{  
            $html[$key] .='<option value="'.$options_attr['option_id'].'">'.$name.'</option>';
        }
       }
    // $html[$key] .='<option value="" style="color:red">Select Opencart Option</option>';
    // foreach($options as $options_attr) { 
    //     $html[$key] .='<option value="'.$options_attr['name'].'">'.$options_attr['name'].'</option>';
    //     }
    $html[$key] .='</select></label><label class="col-md-4"><input type="text" name="varient_attribute['.$key.'][]" class="form-control" value="'.$default.'" type="text" placeholder="Set Default"></div>';
     }  
       //print_r($html); die();
       echo json_encode($html);
     }
    }
  }

   public function varient_attr_mapping()
    {
    $html=array();
    $default='';
    $subcatId   = $_POST['id'];
    $acc_id     = $_POST['acc_id'];
    $profile_id =  $_POST['profile_id'];
    $option_id  =  $_POST['option_id'];
    if(isset($subcatId)){ 
    $this->load->model('extension/module/cednewegg/profile');
    $industryCode = $this->model_extension_module_cednewegg_profile->getroot_cat($acc_id);
    //print_r($industryCode); die();
    $data = include(DIR_SYSTEM . 
        'library/cednewegg/SubCat/SubCatFields/'.$industryCode.'/'.$subcatId.'.php');
    foreach($data as $vat_var){
        if($vat_var['IsGroupBy']=='1'){
           $vat[] = $vat_var;
        }
    }
    $this->load->model('extension/module/cednewegg/profile');
    $subattr = $this->model_extension_module_cednewegg_profile->profile_label();
   if(isset($profile_id)){ 
    $get_optional_attr_data = $this->model_extension_module_cednewegg_profile->getProfile($profile_id);
    if(isset($get_optional_attr_data['profile_varient_mapping'])){
     $varient_attribute = $get_optional_attr_data['profile_varient_mapping']; 
     }
    }
   //print_r($get_optional_attr_data); exit(__FILE__);
    $store_options = $this->model_extension_module_cednewegg_profile->getStoreOptions();
    $options = $store_options['options'];
    $option_values = $store_options['option_values'];
    //print_r($option_values); die();
         if(isset($vat)){
    foreach($vat as $key=>$vat_var_opt){
        $PropertyName = $vat_var_opt['PropertyName'];
        $data_var = include(DIR_SYSTEM . 
        'library/cednewegg/SubCat/SubCatFieldValues/'.$industryCode.'/'.$subcatId.'/'.$PropertyName.'.php');
       $this->model_extension_module_cednewegg_profile->ProfilePropertyName($profile_id,$PropertyName);
        //print_r($data_var['PropertyName']); die(__FILE__);
       
       foreach($option_values[$option_id] as $key=>$options_attr){ 
         $opt_vals='';
        if(!empty($varient_attribute['varient_mapping'])){ 
        foreach($varient_attribute['varient_mapping'] as $opt_val){
            //print_r($opt_val); die();
            if($options_attr['name'] == $opt_val['opencart_attribute']){
              $opt_vals  = $opt_val['varient_attributes'];
             }
            }
        }
  // print_r($opt_vals); die(__FILE__);
    $html[$key] = '<div class="row"><label class="col-md-2"></label><label class="col-md-4"><input type="text" class="form-control" name="varient_map['.$key.'][]" value="'.$options_attr['name'].'" readonly></label>';
    $html[$key] .='<label class="col-md-4"><select class="form-control select_Value" id="select_map'.$key.'"  name="varient_map['.$key.'][]"style="text-transform: capitalize;">
        <option value="">Select Opencart Varient Option </option>';
        foreach($data_var['PropertyValueList'] as $datavar){
            if($datavar == $opt_vals){ 
            $html[$key] .='<option value="'.$datavar.'" selected>'.$datavar.'</option>';
           }else{
            $html[$key] .='<option value="'.$datavar.'">'.$datavar.'</option>';
           }
        }
    $html[$key] .='</select></label><label class="col-md-2"></label></div>';
    } 
   } 
       //print_r($html); die();
       echo json_encode($html);
     }
     }
    }
    public function update()
    { 
        if (VERSION < 3.0) {
            $data = $this->language->load('extension/module/cednewegg/profile');
        } else {
            $this->language->load('extension/module/cednewegg/profile');
        }
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/cednewegg/profile');
        $prfofile_product = $this->model_extension_module_cednewegg_profile->getProductByProfileId($this->request->get['id']);
       
    if (isset($this->request->get['id'])) {
    $data['prfofile_product'] = $prfofile_product = $this->model_extension_module_cednewegg_profile->getProductByProfileId($this->request->get['id']);
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_cednewegg_profile->editProfile($this->request->get['id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
        }
        $this->getForm();
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/cednewegg/profile')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!isset($this->request->post['profile_name']) || empty($this->request->post['profile_name'])) {
            $this->error['profile_name'] = $this->language->get('error_profile_name');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}