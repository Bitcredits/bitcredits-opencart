<?php 

class ControllerPaymentBitcredits extends Controller {
  private $error = array();
  private $payment_module_name  = 'bitcredits';

  private function validate() {
    if (!$this->user->hasPermission('modify', 'payment/'.$this->payment_module_name)) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (!$this->request->post['bitcredits_api_key']) {
      $this->error['api_key'] = $this->language->get('error_api_key');
    }
        
    if (!$this->error) {
      return TRUE;
    } else {
      return FALSE;
    }  
  }
  
  public function index() {
    $this->load->language('payment/'.$this->payment_module_name);
    $this->load->model('setting/setting');
    
    if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
      $this->model_setting_setting->editSetting($this->payment_module_name, $this->request->post);
      $this->session->data['success'] = $this->language->get('text_success');
      $this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
    }
     if (isset($this->error['warning'])) {
      $this->data['error_warning'] = $this->error['warning'];
    } else {
      $this->data['error_warning'] = '';
    }
    
    $this->document->setTitle($this->language->get('heading_title'));
    
    $this->data['heading_title']           = $this->language->get('heading_title');

    $this->data['text_enabled']            = $this->language->get('text_enabled');
    $this->data['text_disabled']           = $this->language->get('text_disabled');
        
    $this->data['entry_api_key']           = $this->language->get('entry_api_key');
    $this->data['entry_api_endpoint']      = $this->language->get('entry_api_endpoint');
    $this->data['entry_confirmed_status']  = $this->language->get('entry_confirmed_status');
    $this->data['entry_invalid_status']    = $this->language->get('entry_invalid_status');
    $this->data['entry_status']            = $this->language->get('entry_status');
    $this->data['entry_sort_order']        = $this->language->get('entry_sort_order');
    
    $this->data['button_save']             = $this->language->get('button_save');
    $this->data['button_cancel']           = $this->language->get('button_cancel');

    $this->data['tab_general']             = $this->language->get('tab_general');
    
    if (isset($this->error['api_key'])) {
      $this->data['error_api_key'] = $this->error['api_key'];
    } else {
      $this->data['error_api_key'] = '';
    }
    if (isset($this->error['api_endpoint'])) {
      $this->data['error_api_endpoint'] = $this->error['api_endpoint'];
    } else {
      $this->data['error_api_endpoint'] = '';
    }

    $this->data['breadcrumbs'] = array();

    $this->data['breadcrumbs'][] = array(
      'text'    => $this->language->get('text_home'),
      'href'    => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => false
    );

    $this->data['breadcrumbs'][] = array(
      'text'    => $this->language->get('text_payment'),
      'href'    => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => ' :: '
    );

    $this->data['breadcrumbs'][] = array(
      'text'    => $this->language->get('heading_title'),
      'href'    => $this->url->link('payment/bitcredits', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => ' :: '
    );

    $this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/'.$this->payment_module_name.'&token=' . $this->session->data['token'];
    $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];  

    $this->load->model('localisation/order_status');
    $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
    
    if (isset($this->request->post[$this->payment_module_name.'_api_key'])) {
      $this->data[$this->payment_module_name.'_api_key'] = $this->request->post[$this->payment_module_name.'_api_key'];
    } else {
      $this->data[$this->payment_module_name.'_api_key'] = $this->config->get($this->payment_module_name.'_api_key');
    } 
    if (isset($this->request->post[$this->payment_module_name.'_api_endpoint'])) {
      $this->data[$this->payment_module_name.'_api_endpoint'] = $this->request->post[$this->payment_module_name.'_api_endpoint'];
    } else {
      $this->data[$this->payment_module_name.'_api_endpoint'] = $this->config->get($this->payment_module_name.'_api_endpoint') ?: 'https://api.bitcredits.io';
    } 
    if (isset($this->request->post[$this->payment_module_name.'_confirmed_status_id'])) {
      $this->data[$this->payment_module_name.'_confirmed_status_id'] = $this->request->post[$this->payment_module_name.'_confirmed_status_id'];
    } else {
      $this->data[$this->payment_module_name.'_confirmed_status_id'] = $this->config->get($this->payment_module_name.'_confirmed_status_id'); 
    } 
    if (isset($this->request->post[$this->payment_module_name.'_invalid_status_id'])) {
      $this->data[$this->payment_module_name.'_invalid_status_id'] = $this->request->post[$this->payment_module_name.'_invalid_status_id'];
    } else {
      $this->data[$this->payment_module_name.'_invalid_status_id'] = $this->config->get($this->payment_module_name.'_invalid_status_id'); 
    } 
    if (isset($this->request->post[$this->payment_module_name.'_status'])) {
      $this->data[$this->payment_module_name.'_status'] = $this->request->post[$this->payment_module_name.'_status'];
    } else {
      $this->data[$this->payment_module_name.'_status'] = $this->config->get($this->payment_module_name.'_status');
    }
    if (isset($this->request->post[$this->payment_module_name.'_sort_order'])) {
      $this->data[$this->payment_module_name.'_sort_order'] = $this->request->post[$this->payment_module_name.'_sort_order'];
    } else {
      $this->data[$this->payment_module_name.'_sort_order'] = $this->config->get($this->payment_module_name.'_sort_order');
    }
    $this->template = 'payment/'.$this->payment_module_name.'.tpl';
    $this->children = array(
      'common/header',  
      'common/footer'  
    );
    
    $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
  }
}
