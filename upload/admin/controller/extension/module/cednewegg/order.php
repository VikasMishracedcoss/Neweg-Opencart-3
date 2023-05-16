<?php
include_once(DIR_SYSTEM . 'library/cednewegg/order.php');
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
 * @category  modules
 * @package   NewEgg
 * @author    CedCommerce Core Team
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
 
class ControllerExtensionModuleCedNewEggOrder extends Controller
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
        $this->helper = CedNewegg::getInstance($this->registry);
    }

    public function index()
    {
        $this->load->language('extension/module/cednewegg/order');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->getList();
    }

    public function fetch()
    {     
        $this->load->language('extension/module/cednewegg/order');
        $this->load->model('extension/module/cednewegg/order');
        $this->load->library('cednewegg');
        $this->document->setTitle($this->language->get('heading_title'));
        $status = true;
        if ($status) {  
            $order_lib = new CedNewEggOrder($this->registry);
            $response = $order_lib->fetchOrders();
            //print_r($response); die();
            if (isset($response['success']) && $response['success']) {
                $this->session->data['success'] = implode('<br>', $response['success']);
            }
            if (isset($response['error']) && $response['error']) {
                foreach ($response['error'] as $err) {
                    $this->error['warning'][] = $err;
                }
            }
        } else {
            $this->error['error_module'] = $this->language->get('error_module');
        }
        $this->getList();
    }

    protected function getList()
    {
        if (VERSION < 3.0) {
            $this->load->language('extension/module/cednewegg/order');
        } else {
            $data = $this->load->language('extension/module/cednewegg/order');
        }
        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = null;
        }

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = null;
        }

        if (isset($this->request->get['filter_total'])) {
            $filter_total = $this->request->get['filter_total'];
        } else {
            $filter_total = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $filter_date_modified = $this->request->get['filter_date_modified'];
        } else {
            $filter_date_modified = null;
        }

        if (isset($this->request->get['filter_ebay_order_status'])) {
            $filter_ebay_order_status = $this->request->get['filter_newegg_order_status'];
        } else {
            $filter_ebay_order_status = null;
        }

        if (isset($this->request->get['filter_ebay_order_id'])) {
            $filter_ebay_order_id = $this->request->get['filter_newegg_order_id'];
        } else {
            $filter_ebay_order_id = null;
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        } else {
            $filter_order_status_id = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.opencart_orderid';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_ebay_order_status'])) {
            $url .= '&filter_ebay_order_status=' . $this->request->get['filter_ebay_order_status'];
        }

        if (isset($this->request->get['filter_ebay_order_id'])) {
            $url .= '&filter_ebay_order_id=' . $this->request->get['filter_ebay_order_id'];
        }


        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }

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
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->session_token_key . '=' . $this->session_token, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . $url, 'SSL')
        );

        $data['newegg_order_statuses'] = array(
            "Completed",
            "Active",
            "Inactive",
            "Cancelled",
        );

        $data['shipping'] = $this->url->link('extension/module/cednewegg/order/shipping', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['fetch'] = $this->url->link('extension/module/cednewegg/order/fetch', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['syncall'] = $this->url->link('extension/module/cednewegg/order/syncall', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');

        $data['orders'] = array();

        $filter_data = array(
            'filter_order_id' => $filter_order_id,
            'filter_customer' => $filter_customer,
            'filter_total' => $filter_total,
            'filter_date_added' => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'filter_ebay_order_id' => $filter_ebay_order_id,
            'filter_ebay_order_status' => $filter_ebay_order_status,
            'filter_order_status_id' => $filter_order_status_id,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        $this->load->model('extension/module/cednewegg/order');
        $order_total = $this->model_extension_module_cednewegg_order->getTotalOrders($filter_data);
        $results = $this->model_extension_module_cednewegg_order->getOrders($filter_data);
        //print_r($results); die(__DIR__);
        if ($results) {
            foreach ($results as $result) {
                //print_r($result['newegg_order_id']); die();
                $status='';
                if($result['wstatus']!='2'){ 
                     $status="Complate";
                }else{
                    $status="Pending";
                }
                $data['orders'][] = array(
                    'id'              => $result['id'],
                    'order_id'        => $result['order_id'],
                    'newegg_orderid'  => $result['newegg_order_id'],
                    'customer'        => $result['customer'],
                    'status'          => $status,
                    /*'wstatus'         => $result['wstatus'],*/
                    'total'           => !empty($result['currency_code']) && $result['total'] > 0 ? $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']) : 0,
                    'date_added'      => !empty($result['date_added']) ? date($this->language->get('date_format_short'), strtotime($result['date_added'])) : '',
                    'date_modified'    => !empty($result['date_modified']) ? date($this->language->get('date_format_short'), strtotime($result['date_modified'])) : '',
                    'shipping_code'    => $result['shipping_code'],
                    'sync'             => $this->url->link('extension/module/cednewegg/order/sync', $this->session_token_key . '=' . $this->session_token . '&order_id=' . $result['order_id'] . '&id=' . $result['id'] . $url, 'SSL'),
                    'view'             => $this->url->link('extension/module/cednewegg/order/info', $this->session_token_key . '=' . $this->session_token . '&order_id=' . $result['order_id'] . '&id=' . $result['id'] . $url, 'SSL'),
                    'selected'         => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
                );
            }
        }
        $data['button_sync_all'] = $this->language->get('button_sync_all');

        $data[$this->session_token_key] = $this->session_token;

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['error_module'])) {
            $data['error_warning'] = $this->error['error_module'];
        } else if (isset($this->error['warning'])) {
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

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_ebay_order_status'])) {
            $url .= '&filter_ebay_order_status=' . $this->request->get['filter_ebay_order_status'];
        }

        if (isset($this->request->get['filter_ebay_order_id'])) {
            $url .= '&filter_ebay_order_id=' . $this->request->get['filter_ebay_order_id'];
        }

        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=o.order_id' . $url, 'SSL');
        $data['sort_customer'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=customer' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=status' . $url, 'SSL');
        $data['sort_total'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=o.total' . $url, 'SSL');
        $data['sort_date_added'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=o.date_added' . $url, 'SSL');
        $data['sort_date_modified'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=o.date_modified' . $url, 'SSL');
        $data['sort_wstatus'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=wo.status' . $url, 'SSL');
        $data['sort_ebay_order_id'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=wo.ebay_order_id' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_ebay_order_status'])) {
            $url .= '&filter_ebay_order_status=' . $this->request->get['filter_ebay_order_status'];
        }

        if (isset($this->request->get['filter_ebay_order_id'])) {
            $url .= '&filter_ebay_order_id=' . $this->request->get['filter_ebay_order_id'];
        }

        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('extension/module/cedebay/order', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_order_id'] = $filter_order_id;
        $data['filter_customer'] = $filter_customer;
        $data['filter_total'] = $filter_total;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_date_modified'] = $filter_date_modified;
        $data['filter_ebay_order_status'] = $filter_ebay_order_status;
        $data['filter_ebay_order_id'] = $filter_ebay_order_id;
        $data['filter_order_status_id'] = $filter_order_status_id;

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

       //print_r($data); die();

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/order/order_list', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/order/order_list.tpl', $data));
        }
    }

    public function info()
    {
        $this->load->model('sale/order');
        $this->load->model('extension/module/cednewegg/order');
        $this->load->model('extension/module/cednewegg/specifics');
        $order_data = $this->model_extension_module_cednewegg_order->getOrder($this->request->get['id']);
        //print_r($order_data); die();
        $data['newegg_order_info'] = $order_data;
        $newegg_order_id = '';
        if (isset($order_data['OrderID'])) {
            $newegg_order_id = $order_data['OrderID'];
        }
        $data['newegg_order_id'] = $newegg_order_id;
        $order_items = array();
        if (isset($order_data['TransactionArray']['Transaction'][0])) {
            $order_items = $order_data['TransactionArray']['Transaction'];
        } elseif (isset($order_data['TransactionArray']['Transaction'])) {
            $order_items[] = $order_data['TransactionArray']['Transaction'];
        }
        $data['order_items'] = $order_items;
        $shipping_carriers = $this->model_extension_module_cednewegg_specifics->getShippingCarriers();

        //print_r($shipping_carriers); die(__FILE__);
        $data['shipping_carriers'] = $shipping_carriers;

        $this->load->language('extension/module/cednewegg/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_back'] = $this->language->get('button_back');

        $data[$this->session_token_key] = $this->session_token;

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

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
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->session_token_key . '=' . $this->session_token, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sale/order', $this->session_token_key . '=' . $this->session_token . $url, 'SSL')
        );

        $data['acknowledge'] = $this->url->link('extension/module/cednewegg/order/acknowledge', $this->session_token_key . '=' . $this->session_token . '&order_id=' . (int)$this->request->get['order_id'], 'SSL');

        $data['shipment'] = $this->url->link('extension/module/cednewegg/order/ship', $this->session_token_key . '=' . $this->session_token . '&order_id=' . (int)$this->request->get['order_id'], 'SSL');
        $data['back'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');

        $data['opencart_order_id'] = $this->request->get['order_id'];
        $data['opencart_order_link'] = $this->url->link('sale/order/info', $this->session_token_key . '=' . $this->session_token . '&order_id=' . $data['opencart_order_id'] . $url, 'SSL');


        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        //print_r($data); die();

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/order/order_info', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/order/order_info.tpl', $data));
        }
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/cednewegg/order')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function fail()
    {
        if (VERSION < 3.0) {
            $data = $this->load->language('extension/module/cedebay/failedorder');
        } else {
            $this->load->language('extension/module/cedebay/failedorder');
        }

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['filter_ebay_order_id'])) {
            $filter_ebay_order_id = $this->request->get['filter_ebay_order_id'];
        } else {
            $filter_ebay_order_id = null;
        }

        if (isset($this->request->get['filter_sku'])) {
            $filter_sku = $this->request->get['filter_sku'];
        } else {
            $filter_sku = null;
        }

        if (isset($this->request->get['filter_reason'])) {
            $filter_reason = $this->request->get['filter_reason'];
        } else {
            $filter_reason = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'ebay_order_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_ebay_order_id'])) {
            $url .= '&filter_ebay_order_id=' . $this->request->get['filter_ebay_order_id'];
        }

        if (isset($this->request->get['filter_sku'])) {
            $url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_reason'])) {
            $url .= '&filter_reason=' . $this->request->get['filter_reason'];
        }

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
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->session_token_key . '=' . $this->session_token, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/cedebay/order/rejected', $this->session_token_key . '=' . $this->session_token . $url, 'SSL')
        );

        $filter_data = array(
            'filter_order_id' => $filter_ebay_order_id,
            'filter_customer' => $filter_sku,
            'filter_order_status' => $filter_reason,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        $this->load->model('extension/module/cedebay/order');
        $results = $this->model_extension_module_cedebay_order->getrejectedOrders($filter_data);
        $order_total = $this->model_extension_module_cedebay_order->getRejectedTotals($filter_data);

        if ($results) {
            foreach ($results as $result) {
                $data['orders'][] = array(
                    'id' => $result['ebay_order_id'],
                    'merchantsku' => $result['sku'],
                    'reason' => $result['reason'],
                    'delete' => $this->url->link('extension/module/cedebay/order/deletefailed', $this->session_token_key . '=' . $this->session_token . '&id=' . $result['id'] . $url, 'SSL'),
                );
            }
        }


        // $data['heading_title'] = $this->language->get('heading_title');
        // $data['text_list'] = $this->language->get('text_list');
        // $data['text_no_results'] = $this->language->get('text_no_results');
        // $data['column_ebay_order_id'] = $this->language->get('column_ebay_order_id');
        // $data['column_sku'] = $this->language->get('column_sku');
        // $data['column_reason'] = $this->language->get('column_reason');
        // $data['column_action'] = $this->language->get('column_action');

        // $data['entry_ebay_order_id'] = $this->language->get('entry_ebay_order_id');
        // $data['entry_sku'] = $this->language->get('entry_sku');
        // $data['entry_reason'] = $this->language->get('entry_reason');

        // $data['button_edit'] = $this->language->get('viewrejected');
        // $data['button_cancel'] = $this->language->get('button_cancel');
        // $data['button_delete'] = $this->language->get('button_delete');

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

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_ebay_order_id'])) {
            $url .= '&filter_ebay_order_id=' . $this->request->get['filter_ebay_order_id'];
        }

        if (isset($this->request->get['filter_sku'])) {
            $url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_reason'])) {
            $url .= '&filter_reason=' . $this->request->get['filter_reason'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_ebay_order_id'] = $this->url->link('extension/module/cedebay/order/fail', $this->session_token_key . '=' . $this->session_token . '&sort=ebay_order_id' . $url, 'SSL');
        $data['sort_sku'] = $this->url->link('extension/module/cedebay/order/fail', $this->session_token_key . '=' . $this->session_token . '&sort=sku' . $url, 'SSL');
        $data['sort_reason'] = $this->url->link('extension/module/cedebay/order/fail', $this->session_token_key . '=' . $this->session_token . '&sort=reason' . $url, 'SSL');

        $data['delete_all'] = $this->url->link('extension/module/cedebay/order/deletefailedall', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');

        $data['filter_ebay_order_id'] = $filter_ebay_order_id;
        $data['filter_customersku'] = $filter_sku;
        $data['filter_reason'] = $filter_reason;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view('extension/module/cedebay/order/failed_orders', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/module/cedebay/order/failed_orders.tpl', $data)); 
        }
    }

    public function deletefailed()
    {
        if (isset($this->request->get['id']) && !empty($this->request->get['id'])) {
            $this->processDeleteFailedOrder($this->request->get['id']);
        }
        $this->fail();
    }
    public function deletefailedall()
    {
        $post_data = $this->request->post;
        if (isset($post_data['selected']) && count($post_data['selected'])) {

            foreach ($post_data['selected'] as $id) {
                $this->processDeleteFailedOrder($id);
            }
        } else {
            $this->error['warning'] = 'Please Select Order to delete';
        }
        $this->fail();
    }

    public function processDeleteFailedOrder($id)
    {
        if (!empty($id)) {
            $sql = "DELETE FROM `" . DB_PREFIX . "cedebay_order_error` WHERE `id`=" . (int)$id;
            $result = $this->db->query($sql);
            if ($result) {
                $this->session->data['success'] = 'Deleted';
            } else {
                $this->error['warning'] = 'Failed To Delete';
            }
        }
    }

    public function syncall()
    {
        $post_data = $this->request->post;
        if (isset($post_data['selected']) && count($post_data['selected'])) {

            foreach ($post_data['selected'] as $id) {
                $this->processSyncOrder($id);
            }
        } else {
            $this->error['warning'] = 'Please Select Order to sync';
        }
        $this->getList();
    }

    public function shipOrder()
    {
        $newegg_order_id = isset($this->request->post['newegg_order_id']) ? $this->request->post['opencart_order_id'] : '';
        $carrier_code = isset($this->request->post['carrier_code']) ? $this->request->post['carrier_code'] : '';
        $tracking_number = isset($this->request->post['tracking_number']) ? $this->request->post['tracking_number'] : '';
        if (empty($newegg_order_id) || empty($carrier_code) || empty($tracking_number)) {
            $result = array(
                'success' => false,
                'message' => 'Invalid Ebay Order Id Or Tracking Information'
            );
        } else {
            $order_lib = new CedNewEggOrder($this->registry);
            $result = $order_lib->shipOrder($newegg_order_id, $carrier_code, $tracking_number);
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));
    }
}
