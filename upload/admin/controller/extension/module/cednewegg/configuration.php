<?php 
class ControllerextensionmoduleCedNewEggConfiguration extends Controller
{
    
    private $helper;
    private $session_token_key = '';
    private $session_token = '';
    private $module_path = '';
    public function __construct($registry)
    {  
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
        $this->helper = CedNewegg::getInstance($this->registry);
    }

    public function index()
    {
        $data = array();
        if (VERSION < 3.0) {
            $data = $this->load->language($this->module_path . '/cednewegg');
        } else {
            $this->load->language('extension/module/cednewegg');
        }
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

                // echo " <pre>";
                // print_r($this->request->post);
                // die('okay');

            if ($this->request->post['cednewegg_status'])
                $this->model_setting_setting->editSetting('module_cednewegg', ['module_cedshopee_status' => 1]);
            else
                $this->model_setting_setting->editSetting('module_cedebay', ['module_cedshopee_status' => 0]);
            $this->model_setting_setting->editSetting('cednewegg', $this->request->post);
            if ($this->validate()) {
                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link($this->extension_path, $this->session_token_key . '=' . $this->session_token, true));
            }
        }


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['error_api_url'])) {
            $data['error_api_url'] = $this->error['error_api_url'];
        } else {
            $data['error_api_url'] = '';
        }

        if (isset($this->error['error_app_id'])) {
            $data['error_app_id'] = $this->error['error_app_id'];
        } else {
            $data['error_app_id'] = '';
        }

        if (isset($this->error['error_cert_id'])) {
            $data['error_cert_id'] = $this->error['error_cert_id'];
        } else {
            $data['error_cert_id'] = '';
        }

        if (isset($this->error['error_dev_id'])) {
            $data['error_dev_id'] = $this->error['error_dev_id'];
        } else {
            $data['error_dev_id'] = '';
        }

        if (isset($this->error['error_runame'])) {
            $data['error_runame'] = $this->error['error_runame'];
        } else {
            $data['error_runame'] = '';
        }

        if (isset($this->error['error_app_id'])) {
            $data['error_app_id'] = $this->error['error_app_id'];
        } else {
            $data['error_app_id'] = '';
        }

        if (isset($this->error['error_order_email'])) {
            $data['error_order_email'] = $this->error['error_order_email'];
        } else {
            $data['error_order_email'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->session_token_key . '=' . $this->session_token, true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/module', $this->session_token_key . '=' . $this->session_token . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/cednewegg', $this->session_token_key . '=' . $this->session_token, true)
        );
        $data[$this->session_token_key] = $this->session_token;
        $data['action'] = $this->url->link('extension/module/cednewegg', $this->session_token_key . '=' . $this->session_token, true);

        $data['cancel'] = $this->url->link('extension/module', $this->session_token_key . '=' . $this->session_token . '&type=module', true);


       //save data into data base oc_setting table


        $data['cednewegg_status'] = '';
        if (isset($this->request->post['cednewegg_status'])) {
            $data['cednewegg_status'] = $this->request->post['cednewegg_status'];
        } else if ($this->config->get('cednewegg_status')) {
            $data['cednewegg_status'] = $this->config->get('cednewegg_status');
        }

        $data['cednewegg_mode'] = '';
        if (isset($this->request->post['cednewegg_mode'])) {
            $data['cednewegg_mode'] = $this->request->post['cednewegg_mode'];
        } else if ($this->config->get('cednewegg_mode')) {
            $data['cednewegg_mode'] = $this->config->get('cednewegg_mode');
        }

        $data['cednewegg_account_location'] = '';
        if (isset($this->request->post['cednewegg_account_location'])) {
            $data['cednewegg_account_location'] = $this->request->post['cednewegg_account_location'];
        } else if ($this->config->get('cednewegg_account_location')) {
            $data['cednewegg_account_location'] = $this->config->get('cednewegg_account_location');
        }

        $data['cednewegg_account_code'] = '';
        if (isset($this->request->post['cednewegg_account_code'])) {
            $data['cednewegg_status'] = $this->request->post['cednewegg_account_code'];
        } else if ($this->config->get('cednewegg_account_code')) {
            $data['cednewegg_account_code'] = $this->config->get('cednewegg_account_code');
        }

         $data['cednewegg_seller_id'] = '';
        if (isset($this->request->post['cednewegg_seller_id'])) {
            $data['cednewegg_seller_id'] = $this->request->post['cednewegg_seller_id'];
        } else if ($this->config->get('cednewegg_seller_id')) {
            $data['cednewegg_seller_id'] = $this->config->get('cednewegg_seller_id');
        }

         $data['cednewegg_secret_key'] = '';
        if (isset($this->request->post['cednewegg_secret_key'])) {
            $data['cednewegg_secret_key'] = $this->request->post['cednewegg_secret_key'];
        } else if ($this->config->get('cednewegg_secret_key')) {
            $data['cednewegg_secret_key'] = $this->config->get('cednewegg_secret_key');
        }

         $data['cednewegg_root_category'] = '';
        if (isset($this->request->post['cednewegg_root_category'])) {
            $data['cednewegg_secret_key'] = $this->request->post['cednewegg_root_category'];
        } else if ($this->config->get('cednewegg_root_category')) {
            $data['cednewegg_root_category'] = $this->config->get('cednewegg_root_category');
        }


        $data['cednewegg_warehouse_location'] = '';
        if (isset($this->request->post['cednewegg_warehouse_location'])) {
            $data['cednewegg_warehouse_location'] = $this->request->post['cednewegg_warehouse_location'];
        } else if ($this->config->get('cednewegg_warehouse_location')) {
            $data['cednewegg_warehouse_location'] = $this->config->get('cednewegg_warehouse_location');
        }

        $data['cednewegg_price_variation_type'] = '';
        if (isset($this->request->post['cednewegg_price_variation_type'])) {
            $data['cednewegg_price_variation_type'] = $this->request->post['cednewegg_price_variation_type'];
        } else if ($this->config->get('cednewegg_price_variation_type')) {
            $data['cednewegg_price_variation_type'] = $this->config->get('cednewegg_price_variation_type');
        }

        $data['cednewegg_price_variation_value'] = '';
        if (isset($this->request->post['cednewegg_price_variation_value'])) {
            $data['cednewegg_price_variation_value'] = $this->request->post['cednewegg_price_variation_value'];
        } else if ($this->config->get('cednewegg_price_variation_value')) {
            $data['cednewegg_price_variation_value'] = $this->config->get('cednewegg_price_variation_value');
        }

        $data['cednewegg_Order_notification_email'] = '';
        if (isset($this->request->post['cednewegg_Order_notification_email'])) {
            $data['cednewegg_Order_notification_email'] = $this->request->post['cednewegg_Order_notification_email'];
        } else if ($this->config->get('cednewegg_Order_notification_email')) {
            $data['cednewegg_Order_notification_email'] = $this->config->get('cednewegg_Order_notification_email');
        }

         $data['cednewegg_Default_Customer_email'] = '';
        if (isset($this->request->post['cednewegg_Default_Customer_email'])) {
            $data['cednewegg_Default_Customer_email'] = $this->request->post['cednewegg_Default_Customer_email'];
        } else if ($this->config->get('cednewegg_Default_Customer_email')) {
            $data['cednewegg_Default_Customer_email'] = $this->config->get('cednewegg_Default_Customer_email');
        }

        $data['cednewegg_Fetch_For_Out_Stock_Product'] = '';
        if (isset($this->request->post['cednewegg_Fetch_For_Out_Stock_Product'])) {
            $data['cednewegg_Fetch_For_Out_Stock_Product'] = $this->request->post['cednewegg_Fetch_For_Out_Stock_Product'];
        } else if ($this->config->get('cednewegg_Fetch_For_Out_Stock_Product')) {
            $data['cednewegg_Fetch_For_Out_Stock_Product'] = $this->config->get('cednewegg_Fetch_For_Out_Stock_Product');
        }

        $data['cednewegg_create_new_Product'] = '';
        if (isset($this->request->post['cednewegg_create_new_Product'])) {
            $data['cednewegg_create_new_Product'] = $this->request->post['cednewegg_create_new_Product'];
        } else if ($this->config->get('cednewegg_create_new_Product')) {
            $data['cednewegg_create_new_Product'] = $this->config->get('cednewegg_create_new_Product');
        }

        $data['cednewegg_order_id_prefix'] = '';
        if (isset($this->request->post['cednewegg_order_id_prefix'])) {
            $data['cednewegg_order_id_prefix'] = $this->request->post['cednewegg_order_id_prefix'];
        } else if ($this->config->get('cednewegg_order_id_prefix')) {
            $data['cednewegg_order_id_prefix'] = $this->config->get('cednewegg_order_id_prefix');
        }

        $data['cednewegg_ordercron_status'] = '';
        if (isset($this->request->post['cednewegg_ordercron_status'])) {
            $data['cednewegg_ordercron_status'] = $this->request->post['cednewegg_ordercron_status'];
        } else if ($this->config->get('cednewegg_ordercron_status')) {
            $data['cednewegg_ordercron_status'] = $this->config->get('cednewegg_ordercron_status');
        }

        $data['cednewegg_priceinvcron_status'] = '';
        if (isset($this->request->post['cednewegg_priceinvcron_status'])) {
            $data['cednewegg_priceinvcron_status'] = $this->request->post['cednewegg_priceinvcron_status'];
        } else if ($this->config->get('cednewegg_priceinvcron_status')) {
            $data['cednewegg_priceinvcron_status'] = $this->config->get('cednewegg_priceinvcron_status');
        }
        

        $data['cednewegg_cron_token'] = '';
        if (isset($this->request->post['cednewegg_cron_token'])) {
            $data['cednewegg_cron_token'] = $this->request->post['cednewegg_cron_token'];
        } else if ($this->config->get('cednewegg_cron_token')) {
            $data['cednewegg_cron_token'] = $this->config->get('cednewegg_cron_token');
        }


        //$this->load->library('cednewegg');
       // $ebay_lib = CedNewegg::getInstance($this->registry);
        //$ebay_location = $ebay_lib->getEbayLocations();
       // $data['ebay_location'] = $ebay_location;

        $cron_array = array(
            'Sync Inventory/Price' => HTTP_CATALOG . 'index.php?route=extension/module/cednewegg/product/syncinventoryprice&token=',
            'Fetch Order' => HTTP_CATALOG . 'index.php?route=extension/module/cednewegg/order/fetchorders&token='
        );
        $data['crons'] = $cron_array;

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
        // echo "<pre>";
        // print_r($data); die(__FILE__);
       
        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (VERSION >= '3.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/configuration/cednewegg', $data));
        }
    }
}