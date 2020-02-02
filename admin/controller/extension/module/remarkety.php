<?php
class ControllerExtensionModuleRemarkety extends Controller
{
    const REMARKETY_MODULE_VERSION = 'oc23.1.1.16';

    const REMARKETY_INSTALL_API_URL = 'https://app.remarkety.com/public/install/notify';

    const REMARKETY_UNINSTALL_API_URL = 'https://app.remarkety.com/public/install/uninstall';

    const REMARKETY_PLATFORM = 'OPENCART';

    const REMARKETY_SUPPORT_EMAIL = 'support@remarkety.com';
    const REMARKETY_SUPPORT_PHONE = '+1 800 570-7564';
    const REMARKETY_LOGIN_URL = 'https://app.remarkety.com';

	protected $error = array();

    public function index()
    {
        $this->remarketyLog = new Log('remarkety.log');

        $this->load->language('module/remarkety');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (isset($this->request->get['type']) && 'reinstall' == $this->request->get['type']) {
            $this->_reinstall();
        } elseif (isset($this->request->get['type']) && 'uninstall' == $this->request->get['type']) {
            $this->_uninstallRemarkety();
            $this->response->redirect($this->url->link('extension/module/remarkety', '&token=' . $this->session->data['token'], 'SSL'));
        } elseif (isset($this->request->get['type']) && in_array($this->request->get['type'], array('queue', 'queue_delete', 'queue_resend'))) {
            $this->_showQueueList($this->request->get['type']);
            return true;
        } elseif ($this->config->get('remarkety_installed')) {
            $installType = null;
        } else {
            $installType = (isset($this->request->get['type']) && in_array($this->request->get['type'],array('login','register')))
                ? $this->request->get['type'] : 'register';
        }

        if ('register' == $installType) {
            $data = $this->_createAccount();
        } elseif ('login' == $installType) {
            $data = $this->_loginToAccount();
        } else {
            $data = $this->_welcome();
        }
        $data['installType'] = $installType;

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extensions'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/remarkety', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_reinstall'] = $this->language->get('button_reinstall');

        $data['action'] = $this->url->link('extension/module/remarkety', 'type='.$installType.'&token=' . $this->session->data['token'], 'SSL');
        $data['reinstall'] = $this->url->link('extension/module/remarkety', 'type=reinstall&token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/remarkety.tpl', $data));
    }

    protected function _showQueueList($actinType)
    {

        $this->load->model('module/remarkety');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (in_array($actinType, array('queue_delete', 'queue_resend'))
            && isset($this->request->get['queue_id']) && $this->request->get['queue_id'])
        {

            if ('queue_delete' == $actinType) {
                $this->model_module_remarkety->fromFront()->deleteQueue($this->request->get['queue_id']);
                $this->session->data['success'] = $this->language->get('text_success_removed_queue');
            } elseif ('queue_resend' == $actinType) {
                $this->model_module_remarkety->fromFront()->runQueue($this->request->get['queue_id']);
                $this->session->data['success'] = $this->language->get('text_success_resend_queue');
            }

            $args = $this->request->request;
            $args['type'] = 'queue';
            $args['page'] = 1;
            unset($args['route'], $args['queue_id']);

            foreach ($args as $key => $value) {
                $urlArgs[] = $key . '=' .$value;
            }
            $this->response->redirect($this->url->link('extension/module/remarkety', '&'. join('&', $urlArgs), 'SSL'));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $postData = $this->request->post;

            if (isset($postData['intervals'])) {
                if (empty($postData['intervals'])) {
                    $data['error_intervals'] = $this->language->get('error_intervals');
                } else {
                    $this->model_setting_setting->editSetting('remarkety_queue', array(
                        'remarkety_queue_intervals' => $postData['intervals'],
                    ));

                    $this->session->data['success'] = $this->language->get('queue_config_success');
                    $this->response->redirect($this->url->link('extension/module/remarkety&type=queue', 'token=' . $this->session->data['token'], 'SSL'));
                }
            }
        }

        if (!isset($data['error_intervals'])) {
            $data['error_intervals'] = '';
        }
        if (isset($this->request->post['intervals'])) {
            $data['intervals'] = $this->request->post['intervals'];
        } else {
            $data['intervals'] = $this->config->get('remarkety_queue_intervals');
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'queue_id';
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

        $adminLimit = $this->config->get('config_limit_admin');
        $data['queue_rows'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $adminLimit,
            'limit' => $adminLimit
        );

        $queueRowsTotal = $this->model_module_remarkety->getTotalQueueRows();
        $results = $this->model_module_remarkety->getQueueRows($filter_data);

        foreach ($results as $result) {
            $data['queue_rows'][] = $result;
        }

        $url = '';
        if ($order == 'ASC') {
            $url .= '&order=DESC';
            $urlAction = '&order=ASC';
        } else {
            $urlAction = '&order=DESC';
            $url .= '&order=ASC';
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
            $urlAction .= '&page=' . $this->request->get['page'];
        }

        $data['delete_link'] = $this->url->link('extension/module/remarkety&type=queue_delete', 'token=' . $this->session->data['token'] . '&sort=' . $sort . $urlAction . '&queue_id=', 'SSL');
        $data['resend_link'] = $this->url->link('extension/module/remarkety&type=queue_resend', 'token=' . $this->session->data['token'] . '&sort=' . $sort . $urlAction . '&queue_id=', 'SSL');

        $data['sort_queue_id'] = $this->url->link('extension/module/remarkety&type=queue', 'token=' . $this->session->data['token'] . '&sort=queue_id' . $url, 'SSL');
        $data['sort_event_type'] = $this->url->link('extension/module/remarkety&type=queue', 'token=' . $this->session->data['token'] . '&sort=event_type' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('extension/module/remarkety&type=queue', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
        $data['sort_attempts'] = $this->url->link('extension/module/remarkety&type=queue', 'token=' . $this->session->data['token'] . '&sort=attempts' . $url, 'SSL');
        $data['sort_last_attempt'] = $this->url->link('extension/module/remarkety&type=queue', 'token=' . $this->session->data['token'] . '&sort=last_attempt' . $url, 'SSL');
        $data['sort_next_attempt'] = $this->url->link('extension/module/remarkety&type=queue', 'token=' . $this->session->data['token'] . '&sort=next_attempt' . $url, 'SSL');

        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $queueRowsTotal;
        $pagination->page = $page;
        $pagination->limit = $adminLimit;
        $pagination->url = $this->url->link('extension/module/remarkety&type=queue', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($queueRowsTotal) ? (($page - 1) * $adminLimit) + 1 : 0, ((($page - 1) * $adminLimit) > ($queueRowsTotal - $adminLimit)) ? $queueRowsTotal : ((($page - 1) * $adminLimit) + $adminLimit), $queueRowsTotal, ceil($queueRowsTotal / $adminLimit));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['queue_config_title'] = $this->language->get('queue_config_title');
        $data['entry_intervals'] = $this->language->get('entry_intervals');
        $data['queue_config_intervals_description'] = $this->language->get('queue_config_intervals_description');
        $data['button_save'] = $this->language->get('button_save');

        $data['queue_heading_title'] = $this->language->get('queue_heading_title');
        $data['queue_text_list'] = $this->language->get('queue_text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_resend'] = $this->language->get('button_resend');

        $data['column_name_queue_id'] = $this->language->get('column_name_queue_id');
        $data['column_name_event_type'] = $this->language->get('column_name_event_type');
        $data['column_name_status'] = $this->language->get('column_name_status');
        $data['column_name_attempts'] = $this->language->get('column_name_attempts');
        $data['column_name_last_attempts'] = $this->language->get('column_name_last_attempts');
        $data['column_name_next_attempts'] = $this->language->get('column_name_next_attempts');
        $data['column_action'] = $this->language->get('column_action');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('queue_heading_title'),
            'href' => $this->url->link('extension/module/remarkety', 'token=' . $this->session->data['token'], 'SSL')
        );

        $this->response->setOutput($this->load->view('module/remarkety_queue.tpl', $data));
    }

    protected function _uninstallRemarkety()
    {
        $data = array(
            'key' => $this->config->get('remarkety_api_key'),
            'email' => $this->config->get('remarkety_email'),
        );

        if($this->_makeUninstallRequest($data)){

            $this->session->data['success'] = $this->language->get('text_uninstall_success');
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('remarkety', array(
                'remarkety_api_key' => null,
                'remarkety_email' => $this->config->get('remarkety_email'),
                'remarkety_store_id' => $this->config->get('remarkety_store_id'),
                'remarkety_installed' => null
            ));
        }
    }

    protected function _welcome()
    {
        $data['uninstall_url'] = $this->url->link('extension/module/remarkety', 'type=uninstall&token=' . $this->session->data['token'], 'SSL');
        $data['queue_url'] = $this->url->link('extension/module/remarkety', 'type=queue&token=' . $this->session->data['token'], 'SSL');

        $data['button_uninstall'] = $this->language->get('button_uninstall');
        $data['text_welcome_to_remarkety'] = $this->language->get('text_welcome_to_remarkety');
        $data['text_sign_in_to_your_account'] = $this->language->get('text_sign_in_to_your_account');
        $data['text_here'] = $this->language->get('text_here');
        $data['text_create_campaigns_send_emails'] = $this->language->get('text_create_campaigns_send_emails');
        $data['text_increase_sales_and_customers'] = $this->language->get('text_increase_sales_and_customers');
        $data['text_meed_help'] = $this->language->get('text_meed_help');
        $data['text_support_email'] = self::REMARKETY_SUPPORT_EMAIL;
        $data['text_support_phone'] = self::REMARKETY_SUPPORT_PHONE;
        $data['text_remarkety_login_url'] = self::REMARKETY_LOGIN_URL;

        $data['button_request_queue'] = $this->language->get('button_request_queue');

        return $data;
    }

    protected function _reinstall()
    {
        $this->model_setting_setting->editSetting('remarkety', array(
            'remarkety_api_key' => null,
            'remarkety_email' => $this->config->get('remarkety_email'),
            'remarkety_store_id' => $this->config->get('remarkety_store_id'),
            'remarkety_installed' => null
        ));

        $this->response->redirect($this->url->link('extension/module/remarkety', 'type=register&token=' . $this->session->data['token'], 'SSL'));
    }

    protected function _loginToAccount()
    {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate('login')) {

            $existingApiKey = $this->config->get('remarkety_api_key');
            $postData = $this->request->post;
            $postData['key'] = ($existingApiKey) ? $existingApiKey : $this->_generateKey($postData);

            $this->model_setting_setting->editSetting('remarkety', array(
                'remarkety_api_key' => $postData['key'],
                'remarkety_email' => null,
                'remarkety_store_id' => $postData['store_id'],
                'remarkety_installed' => null
            ));

            if($this->_makeLoginRequest($postData)){
                $this->model_setting_setting->editSetting('remarkety', array(
                    'remarkety_api_key' => $postData['key'],
                    'remarkety_email' => $postData['email'],
                    'remarkety_store_id' => $postData['store_id'],
                    'remarkety_installed' => time()
                ));

                $this->session->data['success'] = $this->language->get('text_success');

                $this->response->redirect($this->url->link('extension/module/remarkety', '&token=' . $this->session->data['token'], 'SSL'));
            }
        }

        $data['register_url'] = $this->url->link('extension/module/remarkety', 'type=register&token=' . $this->session->data['token'], 'SSL');

        $data['text_dont_have_account'] = $this->language->get('text_dont_have_account');
        $data['text_click_here'] = $this->language->get('text_click_here');
        $data['text_login_to_remarkety'] = $this->language->get('text_login_to_remarkety');

        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_store_id'] = $this->language->get('entry_store_id');

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }
        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }
        if (isset($this->error['store_id'])) {
            $data['error_store_id'] = $this->error['store_id'];
        } else {
            $data['error_store_id'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = $this->config->get('remarkety_email');
        }
        if (isset($this->request->post['store_id'])) {
            $data['store_id'] = $this->request->post['store_id'];
        } else {
            $data['store_id'] = $this->config->get('remarkety_store_id');
        }

        $data['store_list'] = $this->_getStoreList();

        return $data;
    }

    protected function _createAccount()
    {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate('register')) {

            $existingApiKey = $this->config->get('remarkety_api_key');
            $postData = $this->request->post;
            $postData['key'] = ($existingApiKey) ? $existingApiKey : $this->_generateKey($postData);
            unset($postData['remarkety_agreement']);

            $this->model_setting_setting->editSetting('remarkety', array(
                'remarkety_api_key' => $postData['key'],
                'remarkety_email' => null,
                'remarkety_store_id' => $postData['store_id'],
                'remarkety_installed' => null
            ));

            if($this->_makeCreateAccountRequest($postData)){
                $this->model_setting_setting->editSetting('remarkety', array(
                    'remarkety_api_key' => $postData['key'],
                    'remarkety_email' => $postData['email'],
                    'remarkety_store_id' => $postData['store_id'],
                    'remarkety_installed' => time()
                ));

                $this->session->data['success'] = $this->language->get('text_success');

                $this->response->redirect($this->url->link('extension/module/remarkety', '&token=' . $this->session->data['token'], 'SSL'));
           }
        }

        $data['login_url'] = $this->url->link('extension/module/remarkety', 'type=login&token=' . $this->session->data['token'], 'SSL');

        $data['text_already_registered_to_remarkety'] = $this->language->get('text_already_registered_to_remarkety');
        $data['text_create_new_remarkety_account'] = $this->language->get('text_create_new_remarkety_account');
        $data['text_click_here'] = $this->language->get('text_click_here');
        $data['text_terms_of_use'] = $this->language->get('text_terms_of_use');

        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_first_name'] = $this->language->get('entry_first_name');
        $data['entry_last_name'] = $this->language->get('entry_last_name');
        $data['entry_phone'] = $this->language->get('entry_phone');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_agreement'] = $this->language->get('entry_agreement');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_store_id'] = $this->language->get('entry_store_id');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }
        if (isset($this->error['firstName'])) {
            $data['error_firstName'] = $this->error['firstName'];
        } else {
            $data['error_firstName'] = '';
        }
        if (isset($this->error['lastName'])) {
            $data['error_lastName'] = $this->error['lastName'];
        } else {
            $data['error_lastName'] = '';
        }
        if (isset($this->error['phone'])) {
            $data['error_phone'] = $this->error['phone'];
        } else {
            $data['error_phone'] = '';
        }
        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }
        if (isset($this->error['agreement'])) {
            $data['error_agreement'] = $this->error['agreement'];
        } else {
            $data['error_agreement'] = '';
        }
        if (isset($this->error['store_id'])) {
            $data['error_store_id'] = $this->error['store_id'];
        } else {
            $data['error_store_id'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = $this->config->get('remarkety_email');
        }
        if (isset($this->request->post['firstName'])) {
            $data['firstName'] = $this->request->post['firstName'];
        } else {
            $data['firstName'] = '';
        }
        if (isset($this->request->post['lastName'])) {
            $data['lastName'] = $this->request->post['lastName'];
        } else {
            $data['lastName'] = '';
        }
        if (isset($this->request->post['phone'])) {
            $data['phone'] = $this->request->post['phone'];
        } else {
            $data['phone'] ='';
        }
        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }
        if (isset($this->request->post['store_id'])) {
            $data['store_id'] = $this->request->post['store_id'];
        } else {
            $data['store_id'] = $this->config->get('remarkety_store_id');
        }

        $data['store_list'] = $this->_getStoreList();

        return $data;
    }

    protected function _makeUninstallRequest($data)
    {
        $data = array_merge($data, $this->_getShopData());
        $this->remarketyLog = new Log('remarkety.log');

        $this->remarketyLog->write('Uninstall request: ' . serialize($data));
        $result = $this->_sendRequest($data, self::REMARKETY_UNINSTALL_API_URL);
        $this->remarketyLog->write('Uninstall response: ' . serialize($result));

        if(isset($result['status'])){
            if('ERROR' == $result['status']){
                $this->session->data['error'] = $this->language->get('text_error') . ': '.$result['message'];
            }else{
                return true;
            }
        }else{
            $this->session->data['error'] = $this->language->get('text_error');
        }

        return false;
    }

    protected function _makeCreateAccountRequest($data)
    {
	    $storeId = $data['store_id'];
	    $data = array_merge($data, $this->_getShopData($storeId));
	    $data['acceptTerms'] = 1;
	    $data['isNewUser'] = true;
	    $data['shopId'] = $storeId;
        unset($data['store_id']);

        $this->remarketyLog->write('CreateAccount request: ' . serialize($data));
        $result = $this->_sendRequest($data, self::REMARKETY_INSTALL_API_URL);
        $this->remarketyLog->write('CreateAccount response: ' . serialize($result));

        if(isset($result['status'])){
            if('ERROR' == $result['status']){
                $this->error['warning'] = $this->language->get('text_error') . ': '.$result['message'];
            }else{
                return true;
            }
        }else{
            $this->error['warning'] = $this->language->get('text_error');
        }

        return false;
    }

    protected function _getStoreList()
    {
        $data[] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name') . $this->language->get('text_default'),
        );

        $this->load->model('setting/store');
        $results = $this->model_setting_store->getStores();

        foreach ($results as $result) {
            $data['stores'][] = array(
                'store_id' => $result['store_id'],
                'name'     => $result['name'],
            );
        }

        return $data;
    }

    protected function _makeLoginRequest($data)
    {
	    $storeId = $data['store_id'];
	    $data = array_merge($data, $this->_getShopData($storeId));
	    $data['acceptTerms'] = 1;
	    $data['isNewUser'] = false;
	    $data['shopId'] = $storeId;
        unset($data['store_id']);

        $this->remarketyLog->write('Login request: ' . serialize($data));
        $result = $this->_sendRequest($data, self::REMARKETY_INSTALL_API_URL);
        $this->remarketyLog->write('Login response: ' . serialize($result));

        if(isset($result['status'])){
            if('ERROR' == $result['status']){
                $this->error['warning'] = $this->language->get('text_error') . ': '.$result['message'];
            }else{
                return true;
            }
        }else{
            $this->error['warning'] = $this->language->get('text_error');
        }

        return false;
    }

    protected function _sendRequest($data, $apiUrl)
    {
        $timeout = 10;
        $maxredirects = 5;

        $body = http_build_query($data, '', '&');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_MAXREDIRS, $maxredirects);

        curl_setopt($curl, CURLOPT_URL, $apiUrl);
        curl_setopt($curl, CURL_HTTP_VERSION_1_1, true);
        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        if ($response == false) {
            return [
                "status"    => "ERROR",
                "message"   => curl_error($curl)." (".curl_errno($curl).")"
            ];
        }
        return $this->_getResponseBody($response);
    }

    protected function _getResponseBody($response_str)
    {
        $parts = preg_split('|(?:\r?\n){2}|m', $response_str, 2);
        if (isset($parts[1])) {
            return (array)json_decode($parts[1]);
        }
        return '';
    }

	protected function _getShopData($storeId = 0)
	{
		$this->load->model('setting/store');//$this->model_setting_store->load($storeId);
		$store = $this->model_setting_store->getStore($storeId);
		if (!empty($store) && isset($store['url']))
			$domain = $store['url'];
		else
			$domain = HTTP_CATALOG;
		return array(
			'domain' => $domain,
			'platform' => self::REMARKETY_PLATFORM,
			'version' => VERSION,
			'pluginVersion' => self::REMARKETY_MODULE_VERSION
		);
	}

    protected function _generateKey($data)
    {
        return md5($data['email'] . time());
    }

	protected function _validate($type)
    {

		if (!$this->user->hasPermission('modify', 'extension/module/remarkety')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        if('register' == $type || 'login' == $type){
            if (!$this->request->post['email']) {
                $this->error['email'] = $this->language->get('error_email');
            }
            if (!$this->request->post['password']) {
                $this->error['password'] = $this->language->get('error_password');
            }
        }
        if('register' == $type){
            if (!$this->request->post['firstName']) {
                $this->error['firstName'] = $this->language->get('error_firstName');
            }
            if (!$this->request->post['lastName']) {
                $this->error['lastName'] = $this->language->get('error_lastName');
            }
            if (!$this->request->post['phone']) {
                $this->error['phone'] = $this->language->get('error_phone');
            }
            if (!isset($this->request->post['remarkety_agreement'])) {
                $this->error['agreement'] = $this->language->get('error_agreement');
            }
        }

		return !$this->error;
	}


    public function install() {

        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('remarkety', 'catalog/model/account/customer/editCustomer/before', 'extension/module/remarkety/onPreCustomerEdit');
        $this->model_extension_event->addEvent('remarkety', 'catalog/model/account/customer/editCustomer/after', 'extension/module/remarkety/onPostCustomerEdit');

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "remarkety_queue` (
		  `queue_id` int(11) NOT NULL AUTO_INCREMENT,
		  `event_type` varchar(20),
		  `data` text,
		  `attempts` smallint(5) NOT NULL DEFAULT '1',
		  `last_attempt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  `next_attempt` timestamp NULL DEFAULT NULL,
		  `status` smallint(6) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`queue_id`)
		)");

        $this->db->query("ALTER TABLE  `" . DB_PREFIX . "customer` ADD  `cart_last_change` DATETIME NOT NULL AFTER  `date_added`");
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart_last_change=NOW()");
    }

    public function uninstall()
    {
        $this->_uninstallRemarkety();

        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('remarkety');

        $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP `cart_last_change`;");
    }


}