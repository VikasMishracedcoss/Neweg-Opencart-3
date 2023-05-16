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
 
class ControllerExtensionModuleCedNewEggCron extends Controller
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
        $this->load->language('extension/module/cednewegg/cron');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->getList();
    }

  
    protected function getList()
    {
       
        if (VERSION < 3.0) {
            $this->load->language('extension/module/cednewegg/cron');
        } else {
            $data = $this->load->language('extension/module/cednewegg/cron');
        }
        if (isset($this->request->get['filter_id'])) {
            $filter_id = $this->request->get['filter_id'];
        } else {
            $filter_id = null;
        }

        if (isset($this->request->get['filter_job_code'])) {
            $filter_job_code = $this->request->get['filter_job_code'];
        } else {
            $filter_job_code = null;
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
       
        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }
        
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'id';
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

        if (isset($this->request->get['filter_id'])) {
            $url .= '&filter_id=' . $this->request->get['filter_id'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }


        if (isset($this->request->get['filter_job_code'])) {
            $url .= '&filter_job_code=' . $this->request->get['filter_job_code'];
        }
        
        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter__id'])) {
            $url .= '&filter__id=' . $this->request->get['filter__id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['id'])) {
            $url .= '&id=' . $this->request->get['id'];
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
            'href' => $this->url->link('extension/module/cednewegg/cron', $this->session_token_key . '=' . $this->session_token . $url, 'SSL')
        );

        $data['newegg_order_statuses'] = array(
            "Completed",
            "Active",
            "Inactive",
            "Cancelled",
        );

        $data['syncall'] = $this->url->link('extension/module/cednewegg/order/syncall', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');

        $data['cron'] = array();

        $filter_data = array(
        'filter_id'           => $filter_id,
        'filter_job_code'     => $filter_job_code,
        'filter_date_added'   => $filter_date_added,
        'filter_date_modified'=> $filter_date_modified,
        'filter_status'       => $filter_status,
        'sort'                => $sort,
        'start'               => ($page - 1) * $this->config->get('config_limit_admin'),
        'limit'               => $this->config->get('config_limit_admin')
        );
    $this->load->model('extension/module/cednewegg/cron');
    $cron_total = $this->model_extension_module_cednewegg_cron->get_total_cron($filter_data);
    //print_r($cron_total); die(__DIR__);
    $results = $this->model_extension_module_cednewegg_cron->getallcron($filter_data);
   // print_r($results); die(__DIR__);
        
    if ($results) {
        foreach ($results as $result) {
                //print_r($result['newegg_order_id']); die();
                $data['corn'][] = array(
                'id'              => $result['id'],
                'job_code'        => $result['job_code'],
                'message'         => $result['message'],
                'created_at'      => $result['created_at'],
                'finished_at'     => $result['finished_at'],
                'status'          => $result['status'],
                'date_added'      => !empty($result['created_at']) ? date($this->language->get('date_format_short'), strtotime($result['created_at'])) : '',
                'date_modified'    => !empty($result['date_modified']) ? date($this->language->get('date_format_short'), strtotime($result['finished_at'])) : '',
                'selected'         => isset($this->request->post['selected']) && in_array($result['id'], $this->request->post['selected']),
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

        if (isset($this->request->get['filter_id'])) {
            $url .= '&filter_id=' . $this->request->get['filter_id'];
        }

        if (isset($this->request->get['filter_job_code'])) {
            $url .= '&filter_job_code=' . $this->request->get['filter_job_code'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }


        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('extension/module/cednewegg/cron', $this->session_token_key . '=' . $this->session_token . '&sort=id' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=status' . $url, 'SSL');
        $data['sort_total'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=o.total' . $url, 'SSL');
        $data['sort_date_added'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=o.date_added' . $url, 'SSL');
        $data['sort_date_modified'] = $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token . '&sort=o.date_modified' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_id'])) {
            $url .= '&filter_id=' . $this->request->get['filter_id'];
        }

        if (isset($this->request->get['filter_job_code'])) {
            $url .= '&filter_job_code=' . $this->request->get['filter_job_code'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_id'])) {
            $url .= '&filter_id=' . $this->request->get['filter_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        $pagination = new Pagination();
        $pagination->total = $cron_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('extension/module/cednewegg/cron', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($cron_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($cron_total - $this->config->get('config_limit_admin'))) ? $cron_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $cron_total, ceil($cron_total / $this->config->get('config_limit_admin')));

        $data['filter_id']            = $filter_id;
        $data['filter_job_code']      = $filter_job_code;
        $data['filter_date_modified'] = $filter_date_modified;
        $data['filter_date_added']    = $filter_date_added;
        $data['filter_status']        = $filter_status;

        $data['header']  = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

       //print_r($data); die();

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view('extension/module/cednewegg/activity/cron', $data));
        }
    }

}