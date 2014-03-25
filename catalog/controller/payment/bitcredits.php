<?php
/*
©2014 Bitcredits Inc
Permission is hereby granted to any person obtaining a copy of this software
and associated documentation for use and/or modification in association with
Bitcredits Inc services.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

class ControllerPaymentBitcredits extends Controller {
  private $payment_module_name  = 'bitcredits';
  protected function index() {
    $this->language->load('payment/'.$this->payment_module_name);
    $this->data['button_bitcredits_confirm'] = $this->language->get('button_bitcredits_confirm');

    $this->load->model('checkout/order');
    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
    $this->data['orderamount'] = $order_info['total'];

    $this->data['api_endpoint'] = $this->config->get($this->payment_module_name.'_api_endpoint');

    $this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
    $this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
    $this->data['email'] = $order_info['email'];

    $this->data['continue'] = $this->url->link('checkout/success');
    
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/bitcredits.tpl')) {
      $this->template = $this->config->get('config_template') . '/template/payment/bitcredits.tpl';
    } else {
      $this->template = 'default/template/payment/bitcredits.tpl';
    }  
    
    $this->render();
  }
  
  public function send() {

    if (!isset($_COOKIE['bitc'])){
      echo json_encode(array('error'=>'Error: No bitc cookie found.'));
      return;
    }

    $api_endpoint = $this->config->get($this->payment_module_name.'_api_endpoint');
    $api_key = $this->config->get($this->payment_module_name.'_api_key');
    
    $this->load->model('checkout/order');
    $order_id = $this->session->data['order_id'];
    $order = $this->model_checkout_order->getOrder($order_id);
    $total = $order['total'];

    $method = '/v1/transactions';
    $data = array(
      'api_key' => $api_key,
      'src_token' => $_COOKIE['bitc'],
      'dst_account' => '/opencart/orders/'.$order_id,
      'dst_account_create' => true,
      'amount' => $total,
      'data' => array(
        'email' => $order['email'],
        'firstname' => $order['payment_firstname'],
        'lastname' => $order['payment_lastname'],
        'order_id' => (string)$order_id
      )
    );
    
    $ch = curl_init();
    $data_string = json_encode($data);
    curl_setopt($ch, CURLOPT_URL, $api_endpoint . $method);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
    $result = curl_exec($ch);
    $res = json_decode($result, true);

    if ($res == null || !isset($res['status'])) {
      echo json_encode(array('error'=>'Error: Transaction not completed.'));
      return;
    } elseif ($res['status'] == 'error') {
      if (isset($res['message'])) {
        echo json_encode(array('error'=>'Error: Error while processing payment: '.$res['message']));
        return;
      } else {
        echo json_encode(array('error'=>'Error: Transaction not completed. No error message was provided.'));
        return;
      }
    }

    $this->model_checkout_order->confirm($order_id, $this->config->get($this->payment_module_name.'_confirmed_status_id'), '', false);

    echo json_encode(array(
      'url' => $this->url->link('checkout/success/')
    ));
  }
}
?>
