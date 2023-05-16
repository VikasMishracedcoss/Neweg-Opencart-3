<?php 
include_once(DIR_SYSTEM . 'library/cednewegg/product.php');
class ControllerExtensionModuleCedNewEggProduct extends Controller
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
        $this->load->language('extension/module/cednewegg/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/cednewegg/product');

        $this->getList();
    }

    protected function getList()
    {
        if (VERSION < 3.0) {
            $data = $this->load->language('extension/module/cednewegg/product');
        } else {
            $this->load->language('extension/module/cednewegg/product');
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
            'text' => $this->language->get('heading_title'),
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

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'name' => $result['name'],
                'model' => $result['model'],
                'price' => ($cednewegg_price_choice == 6) ? $result['wprice'] : $result['price'],
                'special' => $special,
                'image' => $image,
                'profile_name' => $result['profile_name'],
                'quantity' => ($cednewegg_inventry_choice == 2) ? $result['wquantity'] : $result['quantity'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'newegg_validation_error'=> !empty($result['newegg_validation']) ? $result['newegg_validation'] : '',
                'cednewegg_status' => $result['newegg_status'],
                'newegg_product_id' => $result['product_id'],
                'selected' => isset($this->request->post['selected']) && in_array($result['product_id'], $this->request->post['selected']),
                'action' => $action
            );
        }
        $data['profiles'] = $this->model_extension_module_cednewegg_product->getAllProfiles();
        $data['validate'] = $this->url->link('extension/module/cednewegg/product/validate', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['upload'] = $this->url->link('extension/module/cednewegg/product/upload', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['update'] = $this->url->link('extension/module/cednewegg/product/update', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['sync_status'] = $this->url->link('extension/module/cednewegg/product/sync', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['delete'] = $this->url->link('extension/module/cednewegg/product/delete', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['uploadall'] = $this->url->link('extension/module/cednewegg/product/uploadall', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['syncall'] = $this->url->link('extension/module/cednewegg/product/syncall', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['relist'] = $this->url->link('extension/module/cednewegg/product/relist', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        $data['removeid'] = $this->url->link('extension/module/cednewegg/product/removeid', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        //sumit
        $data['refresh_url'] = $this->url->link('extension/module/cednewegg/product/refresh', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
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

        $data['sort_name'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . '&sort=pd.name' . $url, 'SSL');
        $data['sort_model'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . '&sort=p.model' . $url, 'SSL');
        $data['sort_price'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . '&sort=p.price' . $url, 'SSL');
        $data['sort_quantity'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . '&sort=p.quantity' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . '&sort=p.status' . $url, 'SSL');
        $data['sort_newegg_item_id'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . '&sort=p.newegg_item_id' . $url, 'SSL');
        $data['sort_wstatus'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . '&sort=p.cednewegg_status' . $url, 'SSL');
        $data['sort_order'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . '&sort=p.sort_order' . $url, 'SSL');
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

        $data['filter_name'] = $filter_name;
        $data['filter_model'] = $filter_model;
        $data['filter_price'] = $filter_price;
        $data['filter_quantity'] = $filter_quantity;
        $data['filter_status'] = $filter_status;
        $data['filter_cednewegg_item_id'] = $filter_cednewegg_item_id;
        $data['filter_cednewegg_status'] = $filter_cednewegg_status;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
       // echo '<pre>';
       // print_r($data); 
       // die(__DIR__);
            $this->response->setOutput($this->load->view('extension/module/cednewegg/product/product_list', $data));
        
    }

    public function save()
    {
        $this->load->model('extension/module/cednewegg/product');
        if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $results = $this->model_extension_module_cednewegg_product->saveProductInfo($this->request->post);
            if ($results) {
                $this->session->data['success'] = 'Product Info Saved Successfully From Id :' . $this->request->get['product_id'];
                $this->getList();
            } else {
                $this->error['warning'] = 'Failed to save';
                $this->getForm();
            }
        }
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
            $this->load->model('extension/module/cednewegg/product');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_model'])) {
                $filter_model = $this->request->get['filter_model'];
            } else {
                $filter_model = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 5;
            }

            $filter_data = array(
                'filter_name' => $filter_name,
                'filter_model' => $filter_model,
                'start' => 0,
                'limit' => $limit
            );

            $results = $this->model_extension_module_cednewegg_product->getProducts($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'product_id' => $result['product_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'model' => $result['model'],
                    'price' => $result['price']
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function items()
    {
        $json = array();

        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
            $this->load->model('extension/module/cednewegg/product');
            $this->load->model('extension/module/cednewegg/option');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_model'])) {
                $filter_model = $this->request->get['filter_model'];
            } else {
                $filter_model = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 5;
            }

            $filter_data = array(
                'filter_name' => $filter_name,
                'filter_model' => $filter_model,
                'start' => 0,
                'limit' => $limit
            );

            $results = $this->model_extension_module_cednewegg_product->getProducts($filter_data);
            //print_r($results); die();
            foreach ($results as $result) {
                $json[] = array(
                    'product_id' => $result['product_id'],
                    'newegg_item_id' => $result['newegg_item_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'model' => $result['model'],
                    'price' => $result['price']
                );
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function edit()
    {
        $this->load->language('extension/module/cednewegg/product');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->getForm();
    }



    protected function getForm()
    {
        if (VERSION < 3.0) {
            $data = $this->load->language('extension/module/cednewegg/product');
        } else {
            $this->load->language('extension/module/cednewegg/product');
        }
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        

        $product_id = $this->request->get['product_id'];
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
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
            'href' => $this->url->link('catalog/product', $this->session_token_key . '=' . $this->session_token . $url, 'SSL')
        );

        if (isset($this->request->get['product_id'])) {
            $data['action'] = $this->url->link('extension/module/cednewegg/product/edit', $this->session_token_key . '=' . $this->session_token . '&product_id=' . $this->request->get['product_id'] . $url, 'SSL');
            $data['jet_edit_action'] = $this->url->link('extension/module/cednewegg/product/save', $this->session_token_key . '=' . $this->session_token . '&product_id=' . $this->request->get['product_id'] . $url, 'SSL');
        }
        $this->load->model('catalog/option');
        $this->load->model('catalog/product');
        $this->load->model('extension/module/cednewegg/product');
        //$this->load->model('extension/module/cednewegg/category');

        $data['attribute_values'] = array();
        $data['cancel'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');
        if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $product_data = $this->model_extension_module_cednewegg_product->getProductInfo($this->request->get['product_id']);
            $product = $this->model_extension_module_cednewegg_product->getProduct($this->request->get['product_id']);
            if (!empty($product_data)) {
                $data['product'] = $product_data;
                $data['attribute_values'] = $product_data['attribute_values'];
            }
            if (isset($product['sku']))
                $data['product_sku'] = $product['sku'];
        }

        $data[$this->session_token_key] = $this->session_token;
        $data['profile_id'] = $this->model_extension_module_cednewegg_product->getProfileIdByProductId($product_id);
        $product_lib = new cedneweggProduct($this->registry);

        $data['attributes'] = array(); //$this->model_extension_module_cednewegg_category->getAttrbiuteListByProductId($product_id);
       
        $data['options'] = array();
        $data['has_options'] = false;

        $data[$this->session_token_key] = $this->session_token;
        $data['product_id'] = $this->request->get['product_id'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (VERSION > '2.2.0.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/product/product_form', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/product/product_form.tpl', $data));
        }
    }



    public function neweggEdit()
    {
        if (VERSION < 3.0) {
            $data = $this->language->load('extension/module/cednewegg/product');
        } else {
            $this->language->load('extension/module/cednewegg/product');
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/cednewegg/product');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_cednewegg_product->addProduct($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

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

            $this->response->redirect($this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function viewDetails()
    {
        $id = $this->request->post['product_id'];
        $sql = "SELECT `newegg_validation_error` FROM `" . DB_PREFIX . "cednewegg_profile_products` WHERE `product_id`=" . (int)$id;

        $query = $this->db->query($sql);

        $data = array();
        if ($query->num_rows) { 
              //print_r($query->rows[0]); die();
            $data = $query->rows[0];
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    public function view_error_status()
    {
        $id = $this->request->post['product_id'];
        $sql = "SELECT `newegg_feed_error` FROM `" . DB_PREFIX . "cednewegg_profile_products` WHERE `product_id`=" . (int)$id;

        $query = $this->db->query($sql);

        $data = array();
        if ($query->num_rows) { 
              //print_r($query->rows[0]); die();
            $data = $query->rows[0];
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }
    protected function validateForm()
    {
        $this->session->data['form_data'] = $this->request->post;
        if (!$this->user->hasPermission('modify', 'extension/module/cednewegg/product')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }


    public function upload()
    {   
        $this->load->model('catalog/product');
        $this->load->model('extension/module/cednewegg/newegg');
        $this->load->model('extension/module/cednewegg/product');
        $this->load->model('extension/module/cednewegg/data');
        $this->load->model('extension/module/cednewegg');

        if(isset($_GET['profile_name']))
         { 
            $profile_id = $_GET['profile_name'];
       
        //print_r($profile_id); die();
        $this->model_extension_module_cednewegg_product->clearProductChunk();
        $product_ids = array();
        $params = $this->request->post;
        if (isset($params['selected']) && !empty($params['selected'])) {
            $product_ids = $params['selected'];
        }

        if (empty($product_ids)) {
            $this->error['warning'] = 'Please Select Product(s)';
        } else { 
            if (count($product_ids) > 10) {
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
                $this->response->redirect($this->url->link('extension/module/cednewegg/product/uploadall', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
            } else {
               
                $data_acc= $this->model_extension_module_cednewegg->account_details('cednewegg');
                if (isset($data_acc)) {        
                $data_acc;
                }

               $result= $this->model_extension_module_cednewegg_newegg->prepareData($product_ids,$profile_id);
  
               $upload_product = $this->model_extension_module_cednewegg_data->productUpload($result,$data_acc);

               if(isset($upload_product['0']['Message'])){
                $this->error['warning'] = $upload_product['0']['Message'];
                $this->getList();
               }
               foreach($product_ids as $id){
                $this->model_extension_module_cednewegg_product->update_product_queue($id,$upload_product);
               }
              

            if (isset($upload_product['IsSuccess'])) {
                $RequestId = $upload_product['ResponseBody']['ResponseList']['0']['RequestId'];
                $this->session->data['success'] =  $RequestId. '</br>' . 'Upload Successfully';
                 }
       }
    }
        $this->getList();
        }else{
            $this->error['warning'] = 'Please select profile and filtered Product , profile wise.';
            $this->getList();
         }
    }

    public function sync()
    {
         if(isset($_POST['selected'])){ 
        $this->load->model('extension/module/cednewegg');
        $data_acc= $this->model_extension_module_cednewegg->account_details('cednewegg');
        
        if (isset($data_acc)) {        
        $data_acc;
        }
        $this->load->model('extension/module/cednewegg/product');
        $this->load->model('extension/module/cednewegg/data');

    foreach ($_POST['selected'] as $key => $ids) {
        $product_quee= $this->model_extension_module_cednewegg_product->getProduct_data($ids);
        if(isset($product_quee)){ 
        $upload_product = $this->model_extension_module_cednewegg_data->product_status($product_quee,$data_acc);
        //print_r($upload_product); die();
        $product= $this->model_extension_module_cednewegg_product->product_sycned($upload_product,$ids);
        if($product=='wait'){  
            $this->error['warning'] = 'Sync Product Status Some time later.';
            }
      }else{
        $this->error['warning'] = 'Product not available for status  sync';
      }
    }
    }else{
        $this->error['warning'] = 'Select Product for Status Sync';
    }
    
        $this->getList();
    }
    public function delete()
    {
        $this->load->model('extension/module/cednewegg/product');
        $this->model_extension_module_cednewegg_product->clearProductChunk();
        $product_ids = array();
        $params = $this->request->post;
        if (isset($params['selected']) && !empty($params['selected'])) {
            $product_ids = $params['selected'];
        }
        if (empty($product_ids)) {
            $this->error['warning'] = 'Please Select Product(s)';
        } else {
            if (count($product_ids) > 10) {
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

                $this->model_extension_module_cednewegg_product->addProductsChunk($product_ids, 'delete_chunk');
                $this->response->redirect($this->url->link('extension/module/cednewegg/product/deleteall', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
            } else {
                $newegg_product = new cedneweggProduct($this->registry);
                $result = $newegg_product->deleteneweggItems($product_ids);
                if (isset($result['success']) && $result['success']) {
                    $this->session->data['success'] =  implode($result['success'], '</br>') . '';
                }
                if (isset($result['error']) && $result['error']) {
                    $this->error['warning'] = $result['error'];
                }
            }
        }
        $this->getList();
    }

    public function relist()
    {
        $this->load->model('extension/module/cednewegg/product');
        $this->model_extension_module_cednewegg_product->clearProductChunk();
        $product_ids = array();
        $params = $this->request->post;
        if (isset($params['selected']) && !empty($params['selected'])) {
            $product_ids = $params['selected'];
        }
        if (empty($product_ids)) {
            $this->error['warning'] = 'Please Select Product(s)';
        } else {
            if (count($product_ids) > 5000) {
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

                $this->model_extension_module_cednewegg_product->addProductsChunk($product_ids, 'relist_chunk');
                $this->response->redirect($this->url->link('extension/module/cednewegg/product/relistall', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
            } else {
                $newegg_product = new cedneweggProduct($this->registry);
                $result = $newegg_product->relistneweggitems($product_ids);
                if (isset($result['success']) && $result['success']) {
                    $this->session->data['success'] =  implode($result['success'], '</br>') . '';
                }
                if (isset($result['error']) && $result['error']) {
                    $this->error['warning'] = $result['error'];
                }
            }
        }
        $this->getList();
    }


    public function uploadall()
    {
        $this->load->model('catalog/product');
        $this->load->model('extension/module/cednewegg/newegg');
        $this->load->model('extension/module/cednewegg/product');
        $this->load->model('extension/module/cednewegg/data');
        $this->load->model('extension/module/cednewegg');
        
       if(isset($_GET['profile_name']))
         { 
            $profile_id = $_GET['profile_name'];
             if(isset($_POST['selected'])){ 
               $product_ids = $_POST['selected'];
               //print_r($product_ids); die();
            
            $data_acc= $this->model_extension_module_cednewegg->account_details('cednewegg');
                if (isset($data_acc)) {        
                $data_acc;
                }
               $result= $this->model_extension_module_cednewegg_newegg->prepareData($product_ids,$profile_id);
               $upload_product = $this->model_extension_module_cednewegg_data->productUpload($result,$data_acc);
               if(isset($upload_product['0']['Message'])){
                $this->error['warning'] = $upload_product['0']['Message'];
                $this->getList();
               }
               foreach($product_ids as $id){
                $this->model_extension_module_cednewegg_product->update_product_queue($id,$upload_product);
               }
            if (isset($upload_product['IsSuccess'])) {
                $RequestId = $upload_product['ResponseBody']['ResponseList']['0']['RequestId'];
                $this->session->data['success'] =  $RequestId. '</br>' . 'Upload Successfully';
                 }
            }else{
                $this->error['warning'] = "Please Select Product,Profile wise.";
            }
            $this->getList();
        }else{
            $this->error['warning'] = 'Please select profile and filtered Product , profile wise.';
            $this->getList();
         }
    }


    public function update()
    {
        $this->load->model('catalog/product');
        $this->load->model('extension/module/cednewegg/newegg');
        $this->load->model('extension/module/cednewegg/product');
        $this->load->model('extension/module/cednewegg/data');
        $this->load->model('extension/module/cednewegg');
        
    if(isset($_POST['selected'])) { 
        if(isset($_GET['profile_name'])) { 
            $profile_id = $_GET['profile_name'];
            $product_ids = $_POST['selected'];
            $data_acc= $this->model_extension_module_cednewegg->account_details('cednewegg');
                if (isset($data_acc)) {        
                $data_acc;
                }
               $result= $this->model_extension_module_cednewegg_newegg->prepareData($product_ids,$profile_id,'update');
               //print_r($result); die();
               $upload_product = $this->model_extension_module_cednewegg_data->productUpload($result,$data_acc,'update');

               if(isset($upload_product['0']['Message'])){
                $this->error['warning'] = $upload_product['0']['Message'];
               }
               foreach($product_ids as $id){
                $this->model_extension_module_cednewegg_product->update_product_queue($id,$upload_product);
               }
            if (isset($upload_product['IsSuccess'])) {
                $RequestId = $upload_product['ResponseBody']['ResponseList']['0']['RequestId'];
                $this->session->data['success'] =  $RequestId. '</br>' . 'Update product feed created Successfully';
                 }
        }else{
            $this->error['warning'] = 'Please select profile and filtered Product , profile wise.';
         }
        }else{
            $this->error['warning'] = 'Select Product.';
         }
        $this->getList();
    }


    public function syncall()
    {
        $this->load->model('catalog/product');
        $this->load->model('extension/module/cednewegg/newegg');
        $this->load->model('extension/module/cednewegg/product');
        $this->load->model('extension/module/cednewegg/data');
        $this->load->model('extension/module/cednewegg');

        if(isset($_GET['profile_name']) && isset($_POST['selected'])){
           $product_ids = $_POST['selected'];
           $profile_id  = $_GET['profile_name'];
        $data = $this->load->language('extension/module/cednewegg/product');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/cednewegg/product');
       
        if ($product_ids && count($product_ids)) {
            
        $data_acc= $this->model_extension_module_cednewegg->account_details('cednewegg');
        if (isset($data_acc))
         {        
            $data_acc;
         }
        $result= $this->model_extension_module_cednewegg_newegg->update_inventory($product_ids,$profile_id); 

        $upload_product = $this->model_extension_module_cednewegg_data->update_price($result,$data_acc,'Inventory');
        //print_r($result); die();
            if(isset($upload_product['0']['Message'])){
                $this->error['warning'] = $upload_product['0']['Message'];
            }

        foreach($product_ids as $id){
            $this->model_extension_module_cednewegg_product->update_product_queue($id,$upload_product);
        }   

        $result_price= $this->model_extension_module_cednewegg_newegg->update_price($product_ids,$profile_id); 
        $upload_products = $this->model_extension_module_cednewegg_data->update_price($result_price,$data_acc,'Price');
       
        //print_r($result); die();
            if(isset($upload_products['0']['Message'])){
                $this->error['warning'] = $upload_products['0']['Message'];
            }
        foreach($product_ids as $id){
            $this->model_extension_module_cednewegg_product->update_product_queue($id,$upload_product);
        }   

    if (isset($upload_product['IsSuccess'])) {
        $RequestId = $upload_product['ResponseBody']['ResponseList']['0']['RequestId'];
        $this->session->data['success'] =  $RequestId. '</br>' . 'Upload Successfully';
        }
         $this->getList();
   } else {
            $this->error['warning'] = 'No Products Found In Profile';
            $this->getList();
     }
  } else {
            $this->error['warning'] = 'Select Product Profile';
            $this->getList();
    }
 }
    public function deleteall()
    {
        if (VERSION < 3.0) {
            $data = $this->load->language('extension/module/cednewegg/product');
        } else {
            $this->load->language('extension/module/cednewegg/product');
        }
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/cednewegg/product');

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
        $product_ids = $this->model_extension_module_cednewegg_product->getProductsChunk('delete_chunk');
        if ($product_ids && count($product_ids)) {
            $data['product_ids'] = json_encode($product_ids);

            $data['heading_title'] = $this->language->get('heading_title');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('tool/backup', $this->session_token_key . '=' . $this->session_token, 'SSL')
            );
            $data[$this->session_token_key] = $this->session_token;

            $data['cancel'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . $url, true);
            $data['header']  = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->model_extension_module_cednewegg_product->clearProductChunk();
            if (VERSION > '2.2.0.0') {
                $this->response->setOutput($this->load->view('extension/module/cednewegg/product/deleteall', $data));
            } else {
                $this->response->setOutput($this->load->view('extension/module/cednewegg/product/deleteall.tpl', $data));
            }
        } else {
            $this->error['warning'] = 'No Products Found to delete';
            $this->getList();
        }
    }


    public function deleteallProcess()
    {
        $successes = array();
        $errors = array();
        $this->load->library('cednewegg');
        $productIds = $this->request->post;
        if (!empty($productIds) && isset($productIds['selected']) && !empty($productIds['selected'])) {
            $newegg_product = new cedneweggProduct($this->registry);
            $result = $newegg_product->deleteneweggItems($productIds['selected']);
            if (isset($result['success']) && !empty($result['success'])) {
                $successes = array_merge($successes, $result['success']);
            }
            if (isset($result['error']) && !empty($result['error'])) {
                $errors = array_merge($errors, $result['error']);
            }
        } else {
            $errors[] = 'No Product Found ';
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(array(
            'success' => $successes,
            'error' => $errors
        )));
    }

    public function import()
    {
        $img_dir = DIR_IMAGE . 'cednewegg/';
        if (VERSION < 3.0) {
            $data = $this->load->language('extension/module/cednewegg/product');
        } else {
            $this->load->language('extension/module/cednewegg/product');
        }
        $this->document->setTitle($this->language->get('heading_title'));
        $data['error_warning'] = '';
        $total_chunk = 0;
        $total_items = 0;
        $product_lib = new cedneweggProduct($this->registry);
        $total_items_res = $product_lib->getneweggItems(1, true);
        if (isset($total_items_res['success']) && $total_items_res['success']) {
            $total_items = $total_items_res['message'];
            $total_chunk = (int)ceil($total_items / 20);
        } else {
            $data['error_warning'] = $total_items_res['message'];
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tool/backup', $this->session_token_key . '=' . $this->session_token, 'SSL')
        );
        $data[$this->session_token_key] = $this->session_token;
        $data['img_dir'] = $img_dir;
        $data['total_chunk'] = $total_chunk;
        $data['total_items'] = $total_items;

        $data['cancel'] = $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token, true);
        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/product/import', $data));
        } else {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/product/import.tpl', $data));
        }
    }

    public function removeid()
    {
        $product_ids = array();
        $params = $this->request->post;
        if (isset($params['selected']) && !empty($params['selected'])) {
            $product_ids = $params['selected'];
        }

        if (empty($product_ids)) {
            $this->error['warning'] = 'Please Select Product(s)';
        } else {
            $product_idsChunk = array_chunk($product_ids, 100);
            $successes = array();
            $errors = array();

            foreach ($product_idsChunk as $chunk) {
               // echo '<pre>';
               // print_r($chunk); die();
                $sql = "UPDATE `" . DB_PREFIX . "cednewegg_profile_products` SET `id`='' WHERE `product_id` IN (" . implode(',', $chunk) . ")";
                $this->db->query($sql);
                $successes[] = "NewEgg Ids Removed Successfully for Product Ids " . implode(',', $chunk);
            }
            if (!empty($successes)) {
                $this->session->data['success'] =  implode($successes, '</br>') . '';
            }
            if (!empty($errors)) {
                $this->error['warning'] = $errors;
            }
        }
        $this->getList();
    }


    public function importProducts()
    {
        $successes = array();
        $errors = array();
        $product_lib = new cedneweggProduct($this->registry);
        $this->load->model('extension/module/cednewegg/product');

        if (isset($this->request->post['batch_id']) && (int)$this->request->post['batch_id']) {
            $items_res = $product_lib->getneweggItems($this->request->post['batch_id']);
            if (isset($items_res['success']) && $items_res['success']) {
                $res = $this->model_extension_module_cednewegg_product->syncneweggIds($items_res['message']);
                $successes = array_merge($successes, $res['success']);
                $errors = array_merge($errors, $res['error']);
            } else {
                $errors[] = $items_res['message'];
            }
        } else {
            $errors[] = 'Invalid Batch Id';
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(array(
            'success' => $successes,
            'error' => $errors
        )));
    }

    public function eventEditProduct()
    {
        if (isset($this->request->get['product_id']) && $this->request->get['product_id']) {
            $product_lib = new cedneweggProduct($this->registry);
            $product_lib->syncInventoryPrice(array($this->request->get['product_id']));
        }
    }

    public function refresh()
    {
        if (VERSION < 3.0) {
            $data = $this->load->language('extension/module/cednewegg/product');
        } else {
            $this->load->language('extension/module/cednewegg/product');
        }
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/cednewegg/product');

        $profile_ids = $this->db->query("SELECT * FROM `" . DB_PREFIX . "cednewegg_profile`");

        $profile_ids = $profile_ids->rows;

        if (isset($profile_ids)) {

            foreach ($profile_ids as $values) {
                $profile_id = $values['id'];
                $product_category = json_decode($values['profile_store_category'], true);
                $product_manufacturer = json_decode($values['product_manufacturer'], true);

                $this->model_extension_module_cednewegg_product->updateProduct($profile_id, $product_category, $product_manufacturer);
            }

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
            $this->response->redirect($this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token . $url, true));
        }
        $this->getlist();
    }



     public function validate()
     {
        // echo "<pre>"; 
        // print_r($_POST['selected']);
        // die();


        $this->load->model('extension/module/cednewegg/newegg');
        $this->load->model('catalog/category');
        $this->load->model('extension/module/cednewegg/product');
        if(isset($_POST['selected'])){ 
         foreach($_POST['selected'] as $productids)
            { 
        //echo $productids; die()
        $pofileid = $this->model_extension_module_cednewegg_product->getProfileIdByProductId($productids);
        //echo $pofileid; die();
        $this->validated_product($pofileid,$productids);
            }
        }else{
            $this->getlist();
          }
    

     } 

        public function validated_product($pofileid,$productids)
        {
        $atrributedata = $this->model_extension_module_cednewegg_product->getProfileInfo($pofileid);
         $this->load->model('catalog/product');

        //$atrributedata = $this->getProfileInfo($pofileid);
        // echo "<pre>";
        // print_r($atrributedata['required_attributes']); 
        // die();
        $result['error']=[];
         $result['status']=[];
        foreach($atrributedata['required_attributes'] as $neweggAttribute){
        // print_r($neweggAttribute['newegg_attribute_name']); die();
          switch ($neweggAttribute['newegg_attribute_name']) {
                case 'SellerPartNumber':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                            
                                            $result['error'] ='SellerPartNumber is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'SellerPartNumber is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        } 

                                    } else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] ='SellerPartNumber is a required field. </br>';
                                }
                            }
                                break;
                case 'Manufacturer':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';

                                            $result['error'] .='Manufacturer is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'Manufacturer is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        } 

                                    } else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] .= 'Manufacturer is a required field. </br>';
                                }
                        }
                                break;
                case 'ManufacturerPartNumberOrISBN':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                            $result['error'] .= 'ManufacturerPartNumberOrISBN is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'ManufacturerPartNumberOrISBN is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        } 

                                    } else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] .='ManufacturerPartNumberOrISBN is a required field. </br>';
                                }
                            }
                                break;
                case 'WebsiteShortTitle':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                            $result['error'] = 'WebsiteShortTitle is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'WebsiteShortTitle is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        } 

                                    } else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] .='WebsiteShortTitle is a required field. </br>';
                                }
                            }
                break; 

                case 'ItemWeight':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                            
                                            $result['error'] .= 'ItemWeight is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'ProductDescription is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        } 

                                    } else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] .= 'ProductDescription is a required field. </br>';
                                }
                            }
                break; 
                case 'BulletDescription':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                            $result['error'] = 'BulletDescription is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'BulletDescription is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        } 

                                    } else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] .= 'BulletDescription is a required field. </br>';
                                }
                            }
                break; 

                case 'PacksOrSets':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                           
                                            $result['error'] = 'PacksOrSets is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'PacksOrSets is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        } 

                                    } else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] .= 'PacksOrSets is a required field. </br>';
                                }
                            }
                break;  
                case 'ItemCondition':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                            
                                            $result['error'] .= 'ItemCondition is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'ItemCondition is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        } 

                                    }else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] .='ItemCondition is a required field. </br>';
                                }
                                
                            }
                break; 
                case 'ShippingRestriction':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){

                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode = $neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'ShippingRestriction is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'ShippingRestriction is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }

                                } else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] = 'ShippingRestriction is a required field. </br>';
                                }
                            }
                break; 
                case 'SellingPrice':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode_default = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                          
                                            $result['error'] = 'SellingPrice is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'SellingPrice is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }

                                }  else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] ='SellingPrice is a required field. </br>';
                                }
                            }
                break;   
                case 'Shipping':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                          
                                            $result['error'] = 'Shipping is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'SellingPrice is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }

                                }   else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] =  'Shipping is a required field. </br>';
                                }
                            }
                break; 

            case 'Quantity':
                              if ($neweggAttribute['opencart_attribute_code']!='') {
                                    $attributeCode='';
                                    if(($neweggAttribute['opencart_attribute_code'] == '0') || ($neweggAttribute['opencart_attribute_code'] == '')){
                                        $attributeCode = $neweggAttribute['default'];
                                    }
                                    $attributeCode =$neweggAttribute['opencart_attribute_code'];
                                    
                                    if ($attributeCode){                                    
                                        $attributeValue = $this->getMappingValues($productids,$attributeCode);
                                        if($attributeValue == ''){
                                            $result['status']='Invalid';
                                          
                                            $result['error'] =  'Quantity is a required field. </br>';
                                        }else{
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }
                                   
                                }elseif($attributeCode==0){
                                    if($attributeCode_default == ''){
                                            $result['status']='Invalid'; 
                                            $result['error'] = 'SellingPrice is a required field. </br>';
                                    }else{ 
                                            $result['status']='Valid';
                                            $result['error']='Valid';
                                        }

                                }   else { 
                                 $neweggAttribute['opencart_attribute_code'];
                                    //die('ui9');
                                    $result['error'] =  'Shipping is a required field. </br>';
                                }
                            }
                break;   
                default:
                    if (isset($neweggAttribute['newegg_attribute_name'])) {
                            $attributeCode = '';
                          // die(__DIR__);
                        } else {
                            $result['error'] = $neweggAttribute['newegg_attribute_name'] . ' is a required field. </br>';
                        }
                        break;
                    }
          }
//print_r($result); die();
    $products = $this->model_extension_module_cednewegg_product->savevalidationdata($productids,$result);
    $this->index();
     }


 public function getMappingValues($productids,$attributeCode)
    {
        // echo $productids.'-----'.$attributeCode;
        // die('uio');
         $this->load->model('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($productids);
        //print_r($productsattributes); 
       if(!empty($product_info[$attributeCode])){
          return $data ='1';
        }else{
             return $data = '';
        }
      
    }

  

public function getattributename($attributeCode)
    {
        $this->load->model('catalog/attribute');
        $store_attributes = $this->model_catalog_attribute->getAttributes();
        //print_r($store_attributes); die();
        foreach($store_attributes as $storeattributes){
         if($storeattributes['attribute_id']==$attributeCode){
           return $storeattributes['name'];
          }
        }
    }




}
