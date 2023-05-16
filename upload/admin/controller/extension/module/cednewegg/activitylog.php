<?php 
include_once(DIR_SYSTEM .'library/cednewegg/product.php');
class ControllerExtensionModuleCedNewEggactivitylog extends Controller
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
        $this->load->language('extension/module/cednewegg/log');

        $this->document->setTitle($this->language->get('Activity Log'));

        $this->load->model('extension/module/cednewegg/product');

        $this->getList();
    }

    protected function getList()
    {
        if (VERSION < 3.0) {
            $data = $this->load->language('extension/module/cednewegg/log');
        } else {
            $this->load->language('extension/module/cednewegg/log');
        }
        if (isset($this->request->get['profile_name'])) {
            $filter_profile_name = $this->request->get['profile_name'];
        } else {
            $filter_profile_name = null;
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_model'])) {
            $filter_model = $this->request->get['filter_model'];
        } else {
            $filter_model = null;
        }

        if (isset($this->request->get['filter_price'])) {
            $filter_price = $this->request->get['filter_price'];
        } else {
            $filter_price = null;
        }

        if (isset($this->request->get['filter_quantity'])) {
            $filter_quantity = $this->request->get['filter_quantity'];
        } else {
            $filter_quantity = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }
        if (isset($this->request->get['filter_cednewegg_item_id'])) {
            $filter_cednewegg_item_id = $this->request->get['filter_cednewegg_item_id'];
        } else {
            $filter_cednewegg_item_id = null;
        }
        if (isset($this->request->get['filter_cednewegg_status'])) {
            $filter_cednewegg_status = $this->request->get['filter_cednewegg_status'];
        } else {
            $filter_cednewegg_status = null;
        }
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'pd.name';
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
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['profile_name'])) {
            $url .= '&profile_name=' . urlencode(html_entity_decode($this->request->get['profile_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }
        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_cednewegg_item_id'])) {
            $url .= '&filter_cednewegg_item_id=' . $this->request->get['filter_cednewegg_item_id'];
        }
        if (isset($this->request->get['filter_cednewegg_status'])) {
            $url .= '&filter_cednewegg_status=' . $this->request->get['filter_cednewegg_status'];
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
            'href' => $this->url->link('common/dashboard', $this->session_token_key . '=' . $this->session_token, true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('Activity Log'),
            'href' => $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . $url, true)
        );
        $data['products'] = array();
        $filter_data = array(
        'filter_name' => $filter_name,
        'filter_model' => $filter_model,
        'filter_price' => $filter_price,
        'filter_quantity' => $filter_quantity,
        'filter_status' => $filter_status,
        'filter_cednewegg_item_id' => $filter_cednewegg_item_id,
        'filter_cednewegg_status' => $filter_cednewegg_status,
        'filter_profile_name' => $filter_profile_name,
        'sort' => $sort,
        'order' => $order,
        'start' => ($page - 1) * $this->config->get('config_limit_admin'),
        'limit' => $this->config->get('config_limit_admin')
        );
        $this->load->model('tool/image');
        $this->load->model('extension/module/cednewegg/product');

        $product_total = $this->model_extension_module_cednewegg_product->getTotalProducts($filter_data);
        $results = $this->model_extension_module_cednewegg_product->getProducts($filter_data);
        foreach ($results as $result) {
            $action = array();
            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('catalog/product/edit', $this->session_token_key . '=' . $this->session_token . '&product_id=' . $result['product_id'] . $url, 'SSL')
            );
            $data['view'] = $this->language->get('text_view');
            $action[] = array(
                'text' => $this->language->get('text_view'),
                'href' => 'false'
            );
            if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
            }
            $special = false;
            $product_specials = $this->model_extension_module_cednewegg_product->getProductSpecials($result['product_id']);
            foreach ($product_specials as $product_special) {
                if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
                    $special = $product_special['price'];
                    break;
                }
            }
            $cednewegg_inventry_choice  = $this->config->get('cednewegg_inventry_choice');
            $cednewegg_price_choice     = $this->config->get('cednewegg_price_choice');
        }
        $data['removeid'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        //sumit
        $data['refresh_url'] = $this->url->link('extension/module/cednewegg/activity/activitylog/refresh', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        //sumit
        $data['filter_cednewegg_status'] = $filter_cednewegg_status;
        $data['filter_cednewegg_item_id'] = $filter_cednewegg_item_id;
        $data['profile_filter'] = $filter_profile_name;
        $data['heading_title'] = $this->language->get('heading_title');
        $data[$this->session_token_key] = $this->session_token;
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
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['profile_name'])) {
            $url .= '&profile_name=' . urlencode(html_entity_decode($this->request->get['profile_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }
        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_cednewegg_item_id'])) {
            $url .= '&filter_cednewegg_item_id=' . $this->request->get['filter_cednewegg_item_id'];
        }
        if (isset($this->request->get['filter_cednewegg_status'])) {
            $url .= '&filter_cednewegg_status=' . $this->request->get['filter_cednewegg_status'];
        }
        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['cednewegg_status'] = $this->model_extension_module_cednewegg_product->getneweggStatuses();
        $data['sort_name'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . '&sort=pd.name' . $url, 'SSL');
        $data['sort_model'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . '&sort=p.model' . $url, 'SSL');
        $data['sort_price'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . '&sort=p.price' . $url, 'SSL');
        $data['sort_quantity'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . '&sort=p.quantity' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . '&sort=p.status' . $url, 'SSL');
        $data['sort_newegg_item_id'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . '&sort=p.newegg_item_id' . $url, 'SSL');
        $data['sort_wstatus'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . '&sort=p.cednewegg_status' . $url, 'SSL');
        $data['sort_order'] = $this->url->link('extension/module/cednewegg/activity/activitylog', $this->session_token_key . '=' . $this->session_token . '&sort=p.sort_order' . $url, 'SSL');
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['profile_name'])) {
            $url .= '&profile_name=' . urlencode(html_entity_decode($this->request->get['profile_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }
        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_cednewegg_status'])) {
            $url .= '&filter_cednewegg_status=' . $this->request->get['filter_cednewegg_status'];
        }
        if (isset($this->request->get['filter_cednewegg_item_id'])) {
            $url .= '&filter_cednewegg_item_id=' . $this->request->get['filter_cednewegg_item_id'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);
        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
        
        $data['filter_name']              = $filter_name;
        $data['filter_model']             = $filter_model;
        $data['filter_price']             = $filter_price;
        $data['filter_quantity']          = $filter_quantity;
        $data['filter_status']            = $filter_status;
        $data['filter_cednewegg_item_id'] = $filter_cednewegg_item_id;
        $data['filter_cednewegg_status']  = $filter_cednewegg_status;
        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        // echo '<pre>';
        // print_r($data); 
        // die(__DIR__);
        $this->response->setOutput($this->load->view('extension/module/cednewegg/activity/activitylog', $data));
    }
   }