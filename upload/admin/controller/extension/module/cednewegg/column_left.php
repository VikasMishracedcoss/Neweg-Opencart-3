<?php
class ControllerExtensionModuleCedNewEggColumnLeft extends Controller
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

    public function eventMenu($route, &$data)
    {
        if (VERSION < 3.0) {
            $data  = $this->load->language('extension/module/cednewegg');
        } else {
            $this->load->language('extension/module/cednewegg');
        }

        $cednewegg_menus = array();
        $cednewegg_menus[] = array(
            'name'       => $this->language->get('text_configuration'),
            'href'     => $this->url->link('extension/module/cednewegg', $this->session_token_key . '=' . $this->session_token, true),
            'children' => array()
        );

        $cednewegg_menus_profile = array();
        $cednewegg_menus[] = array( 
            'name' => $this->language->get('manage_profile'),
            'href' =>  $this->url->link('extension/module/cednewegg/profile', $this->session_token_key . '=' . $this->session_token, true),
            'children' => array()
            );
        $cednewegg_list_menus = array();
          if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
            $cednewegg_list_menus[] = [
                'name' => $this->language->get('manage_products'),
                'href' =>  $this->url->link('extension/module/cednewegg/product', $this->session_token_key . '=' . $this->session_token, true),
                'children' => array()
            ];
        }
        if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
            $cednewegg_menus[] = [
                'name' => $this->language->get('listing_products'),
                'children' => $cednewegg_list_menus
            ];
        }

        $cednewegg_order_menus = array();
        if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
            $cednewegg_order_menus[] = array(
                'name'       => $this->language->get('text_mg_order'),
                'href'     => $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token, true),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
            $cednewegg_order_menus[] = array(
                'name'       => $this->language->get('text_mg_forder'),
                'href'     => $this->url->link('extension/module/cednewegg/order/fail', $this->session_token_key . '=' . $this->session_token, true),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
            $cednewegg_menus[] = array(
                'name'       => $this->language->get('text_order'),
                'children' => $cednewegg_order_menus
            );
        }


         $cednewegg_dev_menus = array();
        // if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
        //     $cednewegg_dev_menus[] = array(
        //         'name'       => $this->language->get('text_inventry_price_cron'),
        //         'href'     => $this->url->link('extension/module/cednewegg/order', $this->session_token_key . '=' . $this->session_token, true),
        //         'children' => array()
        //     );
        // }

        if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
            $cednewegg_dev_menus[] = array(
                'name'       => $this->language->get('text_cron'),
                'href'     => $this->url->link('extension/module/cednewegg/cron', $this->session_token_key . '=' . $this->session_token, true),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
            $cednewegg_dev_menus[] = array(
                'name'       => $this->language->get('text_activity_log'),
                'href'     => $this->url->link('extension/module/cednewegg/log', $this->session_token_key . '=' . $this->session_token, true),
                'children' => array()
            );
        }

        // if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
        //     $cednewegg_dev_menus[] = array(
        //         'name'       => $this->language->get('text_feed'),
        //         'href'     => $this->url->link('extension/module/cednewegg/order/fail', $this->session_token_key . '=' . $this->session_token, true),
        //         'children' => array()
        //     );
        // }

        if ($this->user->hasPermission('access', 'extension/module/cednewegg')) {
            $cednewegg_menus[] = array(
                'name'       => $this->language->get('text_developer'),
                'children' => $cednewegg_dev_menus
            );
        }
     

        $data['menus'][] = array(
            'id'       => 'menu-cednewegg',
            'icon'       => 'fa-rocket',
            'name'       => $this->language->get('heading_title'),
            'href'     => '',
            'children' => $cednewegg_menus
        );
    }
}
