<?php

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
 * @package   cedebay
 * @author    CedCommerce Core Team
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
class ControllerExtensionModuleCedNewEggLog extends Controller
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

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/cednewegg/log');
 
        $this->getList();
    }

    protected function getList()
    {

        if (VERSION < 3.0) {
            $data = $this->load->language('extension/module/cednewegg/log');
        } else {
            $this->load->language('extension/module/cednewegg/log');
        }

        if (isset($this->request->get['filter_method'])) {
            $filter_method = $this->request->get['filter_method'];
        } else {
            $filter_method = null;
        }


        if (isset($this->request->get['filter_type'])) {
            $filter_type = $this->request->get['filter_type'];
        } else {
            $filter_type = null;
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

        if (isset($this->request->get['filter_method'])) {
            $url .= '&filter_method=' . urlencode(html_entity_decode($this->request->get['filter_method'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_type'])) {
            $url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
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
            'href' => $this->url->link('extension/module/cednewegg/log', $this->session_token_key . '=' . $this->session_token . $url, true)
        );

        $data['delete'] = $this->url->link('extension/module/cednewegg/log/delete', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');

        $data['products'] = array();
        $filter_data = array(
            'filter_method' => $filter_method,
            'filter_type' => $filter_type,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $this->load->model('tool/image');
        $this->load->model('extension/module/cednewegg/log');

        $product_total = $this->model_extension_module_cednewegg_log->getTotalLogs($filter_data);
        $results = $this->model_extension_module_cednewegg_log->getLogs($filter_data);
        foreach ($results as $result) {
            $action = array();

            $data['view'] = $this->language->get('text_view');

            $action[] = array(
                'text' => $this->language->get('text_view'),
                'href' => $this->url->link('extension/module/cednewegg/log/deletelog', $this->session_token_key . '=' . $this->session_token . '&id=' . $result['id'] . $url, 'SSL')

            );

            $data['logs'][] = array(
                'id' => $result['id'],
                'method' => $result['method'],
                'type' => $result['type'],
                'message' => $result['message'],
                'response' => $result['data'],
                'created_at' => $result['created_at'],
                'selected' => isset($this->request->post['selected']) && in_array($result['id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        // $data['heading_title'] = $this->language->get('heading_title');

        // $data['text_enabled'] = $this->language->get('text_enabled');
        // $data['text_list'] = $this->language->get('text_list');
        // $data['button_delete'] = $this->language->get('button_delete');
        // $data['text_disabled'] = $this->language->get('text_disabled');
        // $data['text_no_results'] = $this->language->get('text_no_results');
        // $data['text_image_manager'] = $this->language->get('text_image_manager');

        // $data['column_id'] = $this->language->get('column_id');
        // $data['column_method'] = $this->language->get('column_method');
        // $data['column_type'] = $this->language->get('column_type');
        // $data['column_message'] = $this->language->get('column_message');
        // $data['column_response'] = $this->language->get('column_response');
        // $data['column_created_at'] = $this->language->get('column_created_at');
        // $data['column_action'] = $this->language->get('column_action');

        // $data['button_filter'] = $this->language->get('button_filter');
        // $data['column_method'] = $this->language->get('column_method');

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

    $data['sort_method'] = $this->url->link('extension/module/cednewegg/log', $this->session_token_key . '=' . $this->session_token . '&sort=p.method' . $url, 'SSL');
    $data['sort_type'] = $this->url->link('extension/module/cednewegg/log', $this->session_token_key . '=' . $this->session_token . '&sort=p.type' . $url, 'SSL');


       
        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/log/log_list', $data));
        }
    }

    public function deletelog()
    {
        $id = $this->request->get['id'];
        $this->processDeleteLog($id);
        $this->getList();
    }
    public function delete()
    {
        $ids = array();
        if (isset($this->request->post['selected'])) {
            $ids = $this->request->post['selected'];
        }
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->processDeleteLog($id);
            }
        } else {
            $this->error['warning'] = 'Please select ids to delete';
        }
        $this->getList();
    }

    public function processDeleteLog($id)
    {
        $sql = "DELETE FROM `" . DB_PREFIX . "cednewegg_logs` WHERE `id` = '" . (int)$id . "'";
        $this->db->query($sql);
        $this->session->data['success'] = 'Log(s) Deleted Successfully';
    }


    public function viewResponse()
    {
        $data = '';
        $id = isset($this->request->post['id']) ? $this->request->post['id'] : '';
        $sql = "SELECT `data` FROM `" . DB_PREFIX . "cednewegg_logs` WHERE `id`=" . (int)$id;
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            $data = $query->row['data'];
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($data);
    }
}
